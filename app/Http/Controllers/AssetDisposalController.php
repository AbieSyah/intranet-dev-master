<?php

namespace App\Http\Controllers;

use App\Mail\DisposalBuyerConfirmation;
use App\Models\AssetDisposal;
use App\Models\AssetDisposalItem;
use App\Models\AssetDisposalLog;
use App\Models\AssetHistory;
use App\Models\DisposalApprovalPath;
use App\Models\Employee;
use App\Models\ITAsset;
use App\Models\Master\LineApproval;
use App\Notifications\DisposalApprovalRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class AssetDisposalController extends Controller
{
    public function getDisposalRequests(Request $request)
    {
        if ($request->ajax()) {
            // Eager load requester and logs for history tracking
            $disposals = null;
            // dd($disposals);
            if (isset($request->my) && $request->my == true) {
                $disposals = Auth::user()->employee->assetDisposals;
            } else {
                $disposals = AssetDisposal::with(['requester', 'approvalPaths', 'disposalItems'])->latest('id');
            }

            if ($request->filter == 'on-proccess') {
                $disposals = $disposals->filter(function($disposal) {
                    return $disposal->doc_status == AssetDisposal::DOC_STATUS_DRAFT;
                });
            } else if ($request->filter == 'done') {
                $disposals = $disposals->filter(function($disposal) {
                    return $disposal->doc_status !== AssetDisposal::DOC_STATUS_DRAFT;
                });
            }

            $datatable = DataTables::of($disposals)
                    ->addColumn('total_price', function($item) {
                        return $item->disposalItems->sum('sale_price');
                    })
                    ->addColumn('encrypted_id', function($item) {
                        return encrypt($item->id);
                    })
                    ->addColumn('revision_url', function($item) {
                        return $item->current_status == AssetDisposal::STATUS_REVISION? 
                            route('asset-disposal.revision', ['id' => encrypt($item->id)]) 
                                : null;
                    })
                    ->addColumn('view_url', function($item) {
                        return route('asset-disposal.show', encrypt($item->id));
                    })
                    // We use a helper to get the current step person from the logs
                    ->addColumn('current_approver', function($item) {
                        return $item->doc_status == AssetDisposal::DOC_STATUS_APPROVED? $item->buyer_name."(Buyer)" : $item->currentStep()->role_name;
                    })
                    ->addColumn('document_url', function($item) {
                        if ($item->doc_status == AssetDisposal::DOC_STATUS_COMPLETE) {
                            return URL::signedRoute('disposal.public-review', 
                                [
                                    'id' => encrypt($item->id)
                                ]
                            );
                        }
                        return null;
                    });

            if (isset($request->my) && $request->my == true) {
                $datatable->addColumn('cancel_url', function($item) {
                    return $item->doc_status == AssetDisposal::DOC_STATUS_DRAFT? 
                        route('asset-disposal.cancel', encrypt($item->id)) : null;
                });
            }

            return $datatable->make(true);
        }
    }
    
    public function index() {
        $approvalList = AssetDisposal::MyPendingApproval()->latest('updated_at')->get();
        $revisionList = AssetDisposal::MyRevisionApproval()->latest('updated_at')->get()->load('logs');

        // dd($approvalList, $revisionList);

        return view('pages.administrator.asset-disposal.index', compact('approvalList', 'revisionList'));
    }

    /**
     * Display a listing of the resource.
     */
    public function preview(Request $request) {
        $itAssets = ITAsset::whereIn('asset_code', $request->asset_codes)->get()->load('assetType', 'employee', 'employee.department');
        // $employees = LineApprovalEmployee::whereHas('lineApproval', fn($query) => $query->where('approval_type', 'Asset Disposal'))->get()->load('employee');
        // $lineApproval = LineApproval::firstWhere("approval_type", "Asset Disposal");
        // $approvers = [
        //     $lineApproval->approve1,
        //     $lineApproval->approve2,
        //     $lineApproval->approve3,
        // ];

        $redirectLink = route('it_asset.index');
        $employees = Employee::with('department', 'position', 'user')->get();
        // dd($approvers);
        return view('pages.administrator.asset-disposal.form', compact('itAssets', 'redirectLink', 'employees'));
    }

    public function revision($id, Request $request) {
        

        $assetDisposal = AssetDisposal::find(decrypt($id));

        if (Auth::user()->employee->id !== $assetDisposal->requester_id) {
            abort(404);
        }
        
        $assetDisposal = $assetDisposal->load(
            'approvalPaths', 
            'approvalPaths.employee', 
            'disposalItems', 
            'disposalItems.itAsset', 
            'disposalItems.itAsset.employee',
            'disposalItems.itAsset.employee.department',
        );

        $latestLog = $assetDisposal->logs->sortByDesc('id')->first();
        $isRevision = true;

        // $approvers = $assetDisposal->approvalPaths->pluck('employee');
        $redirectLink = $request->origin == "service-desk"? route('service-desk.index') : route('asset-disposal.index');

        return view('pages.administrator.asset-disposal.form', compact('assetDisposal', 'latestLog', 'isRevision', 'redirectLink'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if ($request->ajax()) {
            try {

                $validator = Validator::make($request->all(), [
                    'reason' => 'required|string',
                    'buyer_name' => 'required|string',
                    'buyer_phone' => 'required|string',
                    'buyer_email' => 'required|email',
                    'buyer_address' => 'required|string',
                    'itAsset.*.sale_price' => 'required|min:1',
                    'disposal_file' => 'nullable|file|max:1024|mimes:jpeg,png,jpg,pdf',
                    'approver.*.id' => 'required',
                    'approver.*.email' => 'required|email',
                ], [
                    'disposal_file.max' => 'The file size must not exceed 1MB.',
                    'disposal_file.mimes' => 'Only JPEG, PNG, JPG, and PDF files are allowed.',
                    'approver.*.id.required' => 'Approver is required.',
                    'approver.*.email.required' => 'Approver email is required.',
                ]);

                if ($validator->fails()) {
                    // This throws an error that goes straight to your 'catch' block
                    throw new \Exception($validator->errors()->first());
                }
                
                DB::beginTransaction();

                $disposal = null;

                if (!isset($request->asset_disposal)) {
                    $disposal = new AssetDisposal();
                } else {
                    $disposal = AssetDisposal::find(decrypt($request->asset_disposal))->load('approvalPaths');
                }

                if ($request->hasFile('disposal_file')) {
                    // IF UPDATE: Remove the old file if it exists
                    if ($disposal->file_path && Storage::disk('public')->exists($disposal->file_path)) {
                        Storage::disk('public')->delete($disposal->file_path);
                    }

                    // PUSH NEW FILE: Store the new file
                    $file = $request->file('disposal_file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('disposals', $fileName, 'public');
                    
                    // Set the path for the database
                    $disposal->file_path = $path;
                }

                // template
                $todayPrefix = 'DISP-' . now()->format('ymd') . '-';

                $lastDisposal = AssetDisposal::where('transaction_number', 'like', $todayPrefix . '%')
                    ->orderBy('transaction_number', 'desc')
                    ->first();

                if ($lastDisposal) {
                    $lastNumber = (int) substr($lastDisposal->transaction_number, -3);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $user = Auth::user();

                // create or update disposal
                $disposal->transaction_number = $todayPrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $disposal->requester_id = $user->employee->id;
                $disposal->reason = $request->reason;
                $disposal->buyer_name = $request->buyer_name;
                $disposal->buyer_phone = $request->buyer_phone;
                $disposal->buyer_email = $request->buyer_email;
                $disposal->buyer_address = $request->buyer_address;
                $disposal->doc_status = AssetDisposal::DOC_STATUS_DRAFT; 
                $disposal->current_step = 1;
                $disposal->current_status = AssetDisposal::STATUS_WAITING;
                $disposal->save();

                // get all encrypted itAsset id from front end and decrypt it 
                $itAssets = collect($request->itAsset);
                $itAssetIds = $itAssets->pluck('id');
                $itAssetIds = $itAssetIds->map(function($item) {
                    return decrypt($item);
                });

                // If in update state, the itAssets will contain disposalItemId. Wich we take the data in here
                $assetDisposalIds = $itAssets->pluck('disposalItemId')->filter(function($item) {
                    return $item != null;
                });
                if ($assetDisposalIds->isNotEmpty()) { 
                    // decrypt all assetDisposalIds
                    $assetDisposalIds = $assetDisposalIds->map(function($item) {
                        return decrypt($item);
                    });
                }

                // gets all ITAssets that available in frontend
                $existedItAssets = ITAsset::whereIn('id', $itAssetIds)->get();

                // create AssetDisposal item if its on edit mode or create if its in create mode
                $itAssets->each(function($itAsset) use ($disposal, $existedItAssets) {
                    $assetId = decrypt($itAsset['id']);
                    $existedItAsset = $existedItAssets->find($assetId);
                    $disposalItemId = isset($itAsset['disposalItemId'])? decrypt($itAsset['disposalItemId']) : null;
                    $disposalItem = [
                        'asset_disposal_id' => $disposal->id,
                        'it_asset_id' => $assetId,
                        'current_status' => $existedItAsset->status,
                        'buy_price' => $existedItAsset->price,
                        'sale_price' => $itAsset['sale_price'],
                        'reason' => $itAsset['reason']
                    ];

                    $existedItAsset->update([
                        'status' => ITAsset::STATUS_ON_DISPOSAL
                    ]);

                    AssetDisposalItem::updateOrCreate([
                        'id' => $disposalItemId
                    ], $disposalItem);
                });

                // if the asset_disposal doesnt exists(means its on create mode), create a new disposal approval path.
                if (!isset($request->asset_disposal)) {
                    $approvers = Employee::whereIn('id', collect($request->approver)->pluck('id')->map(fn($id) => decrypt($id)))->get()->keyBy('id')->load('department', 'position');
                    // Loop through Approvers (Snapshotting the Workflow)

                    $newApprovalPaths = collect([]);
                    foreach ($request->approver as $index => $approverData) {
                        $newApprovalPaths[] = DisposalApprovalPath::create([
                            'employee_id' => decrypt($approverData['id']),
                            'asset_disposal_id' => $disposal->id,
                            'position' => $approvers->find(decrypt($approverData['id']))->position->nama ?? '-',
                            'department' => $approvers->find(decrypt($approverData['id']))->department->name ?? '-',
                            'email' => $approverData['email'],
                            'role_name' => $approverData['role'],
                            'step_order' => $index,
                        ]);
                    }

                    // create for the first time
                    AssetDisposalLog::create([
                        'asset_disposal_id' => $disposal->id,
                        'disposal_approval_path_id' => $newApprovalPaths->firstWhere('step_order', 1)->id,
                        'status' => AssetDisposal::STATUS_WAITING
                    ]);

                    // dd($disposal->disposalItems);
                    $existedItAssets->each(function($itAsset) use ($disposal, $user) {
                        $disposal->assetHistories()->create([
                            'it_asset_id' => $itAsset->id,
                            'action_type' => AssetHistory::TYPE_DISPOSAL_REQUEST,
                            'description' => "Disposal request by {$user->employee->fullname}",
                            'user_id' => $user->id,
                        ]);
                    });
                } else {                    
                    // create log after user revised
                    AssetDisposalLog::create([
                        'asset_disposal_id' => $disposal->id,
                        'disposal_approval_path_id' => $disposal->currentStep()->id,
                        'status' => AssetDisposal::STATUS_REVISED,
                    ]);

                    $disposal->current_step = $disposal->approvalPaths->sortBy('step_order')->first()->step_order;
                    $disposal->save();

                    AssetDisposalLog::create([
                        'asset_disposal_id' => $disposal->id,
                        'disposal_approval_path_id' => $disposal->approvalPaths->sortBy('step_order')->first()->id,
                        'status' => AssetDisposal::STATUS_WAITING,
                    ]);
                }

                DB::commit();

                $disposal->currentStep()->notify(new DisposalApprovalRequest($disposal));

                return response()->json([
                    'status' => 'success',
                    'message' => 'Disposal request ' . $disposal->transaction_number . ' has been submitted.'
                ], 200);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to save disposal: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id, $type = null) // $type can be null or review
    {   
        // dd($request->route()->getName(), $type, $id, decrypt($id));

        $assetDisposal = AssetDisposal::findOrFail(decrypt($id));

        // if (
        //     $request->route()->getName() == 'asset-disposal.review' &&
        //     Auth::user()->employee->id !== $assetDisposal->currentStep()->employee->id
        // ) {
        //     abort(403);
        // }

        $assetDisposal = $assetDisposal->load('requester.department', 'disposalItems','disposalItems.itAsset', 'logs.approvalPath.employee');

        $badgeColors = [
            AssetDisposal::STATUS_WAITING => 'bg-secondary text-light',
            AssetDisposal::STATUS_APPROVED => 'bg-success',
            AssetDisposal::STATUS_REJECTED => 'bg-danger text-light',
            AssetDisposal::STATUS_REVISION => 'bg-warning text-light',
            AssetDisposal::STATUS_REVISED => 'bg-info',
            AssetDisposal::STATUS_CANCELED => 'bg-primary',
            AssetDisposal::STATUS_COMPLETE => 'bg-success',
        ];


        // dd($request->all());

        $origin = $request->origin?? null;

        return view('pages.administrator.asset-disposal.show', compact('assetDisposal', 'badgeColors', 'type', 'origin'));
    }

    public function feedback($id, Request $request) {
        $assetDisposal = AssetDisposal::find(decrypt($id))->load('approvalPaths');

        $approver = Auth::user()->employee;

        if ($assetDisposal->currentStep()->employee_id !== $approver->id) {
            abort(403, 'You do not have permission to access this disposal request.');
        }

        try {
            DB::beginTransaction();

            $currentApprovalStep = $assetDisposal->currentStep();

            if (isset($request->approve)) {
                $nextPath = $assetDisposal->approvalPaths
                    ->where('step_order', '>', $assetDisposal->current_step)
                    ->sortBy('step_order')
                    ->first();
                    
                if ($nextPath) {
                    $assetDisposal->logs()->create([
                        'disposal_approval_path_id' => $currentApprovalStep->id,
                        'status' => AssetDisposal::STATUS_APPROVED,
                        'actioned_at' => now()
                    ]);
                    $assetDisposal->update(['current_step' => $nextPath->step_order]);
                    $assetDisposal->logs()->create([
                        'disposal_approval_path_id' => $nextPath->id,
                        'status' => AssetDisposal::STATUS_WAITING,
                        'actioned_at' => now()
                    ]);
                    $nextPath->employee->user->notify(new DisposalApprovalRequest($assetDisposal));
                } else {
                    $assetDisposal->load('disposalItems.itAsset');

                    // dd($assetDisposal);
                    $assetDisposal->logs()->create([
                        'disposal_approval_path_id' => $currentApprovalStep->id,
                        'status' => AssetDisposal::STATUS_APPROVED,
                        'actioned_at' => now()
                    ]);
                    $assetDisposal->update(['doc_status' => AssetDisposal::DOC_STATUS_APPROVED]);

                    $assetDisposal->logs()->create([
                        'disposal_approval_path_id' => null,
                        'status' => AssetDisposal::STATUS_WAITING,
                        'for_buyer' => true,
                        'actioned_at' => now(),
                    ]);

                    foreach ($assetDisposal->disposalItems as $index => $disposalItem) {
                        $disposalItem->itAsset->update([
                            'status' => ITAsset::STATUS_DISPOSED
                        ]);
                        $disposalItem->itAsset->histories()->create([
                            'action_type' => AssetHistory::TYPE_DISPOSED,
                            'reference_type' => AssetHistory::REFERENCE_DISPOSAL,
                            'reference_id' => $assetDisposal->id,
                            'description' => "Asset disposal approved at ".now()->format('d-M-Y h:i A'),
                            'user_id' => Auth::user()->id,
                        ]);
                    }

                    $assetDisposal->requester->user->notify(new DisposalApprovalRequest($assetDisposal, DisposalApprovalRequest::APPROVED));
                    $this->sendValidationEmail($assetDisposal);
                }
            } else {
                $assetDisposal->logs()->create([
                    'disposal_approval_path_id' => $currentApprovalStep->id,
                    'status' => AssetDisposal::STATUS_REVISION,
                    'comments' => $request->comment,
                    'actioned_at' => now()
                ]);

                $assetDisposal->requester->user->notify(new DisposalApprovalRequest(
                    $assetDisposal, 
                    DisposalApprovalRequest::REVISION
                ));
            }

            DB::commit();

            if ($request->origin == 'service-desk') {
                return redirect()->route('service-desk.index')->with('success', 'Request has been processed successfully.');
            } elseif ($request->route()->getName() == 'asset-disposal.feedback') {
                return redirect()->back()->with('success', 'Request has been processed successfully.');
            } else {
                return redirect()->route('asset-disposal.index')->with('success', 'Request has been processed successfully.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->with('error', 'Process failed: ' . $e->getMessage());
        }
    }

    public function sendValidationEmail(AssetDisposal $assetDisposal)
    {
        // Create a signed URL (expires in 24 hours)
        $url = URL::signedRoute(
            'disposal.public-review', ['id' => encrypt($assetDisposal->id)]
        );

        Mail::to($assetDisposal->buyer_email)->send(new DisposalBuyerConfirmation($assetDisposal, $url));
    }

    public function buyerReview($id) {
        $decryptedId = decrypt($id);
        // Eager load items and asset details
        $transaction = AssetDisposal::findOrFail($decryptedId)->load(['disposalItems.itAsset.assetType', 'approvalPaths.employee.position', 'requester.position', 'logs']);

        // If already confirmed, show the Invoice view instead
        if ($transaction->doc_status === AssetDisposal::DOC_STATUS_COMPLETE) {
            Carbon::setLocale('id');

            $buyerQrcode = base64_encode(QrCode::format('svg')->size(60)->generate(
                URL::signedRoute('asset-disposal.verify-signature', [
                    'id' => encrypt($transaction->id),
                    'type' => 'buyer',
                ],
            )));

            $signatures = [];
            $signatures[] = [
                'name' => $transaction->requester->fullname,
                'date' => Carbon::parse($transaction->created_at)->translatedFormat('d/m/Y'),
                'position' => $transaction->requester->position->nama,
                'qrcode' => base64_encode(QrCode::format('svg')->size(70)->generate(
                    URL::signedRoute('asset-disposal.verify-signature', [
                        'id' => encrypt($transaction->id),
                        'type' => 'submitter',
                    ],
                )))
            ];

            foreach ($transaction->approvalPaths->sortBy('step_order') as $key => $path) {
                $log = $transaction->logs->where('disposal_approval_path_id', $path->id)->firstWhere('status', AssetDisposal::STATUS_APPROVED);
                $signatures[] = [
                    'name' => $path->employee->fullname,
                    'date' => Carbon::parse($log->actioned_at)->translatedFormat('d/m/Y'),
                    'position' => $path->employee->position->nama,
                    'qrcode' => base64_encode(QrCode::format('svg')->size(70)->generate(
                        URL::signedRoute('asset-disposal.verify-signature', [
                            'id' => encrypt($transaction->id),
                            'type' => 'approver',
                            'pathId' => encrypt($path->id)
                        ],
                    )))
                ];
            }

            $pdf = Pdf::loadView('pages.administrator.asset-disposal.public.disposal-report', [
                'transaction' => $transaction->toArray(),
                'signatures' => $signatures,
                'buyerQrcode' => $buyerQrcode,
                'totalSalePrice' => $transaction->disposalItems->sum('sale_price')

            ])->setPaper('a4', 'portrait');

            return $pdf->stream('Berita_acara_'.$transaction->transaction_number.'.pdf');
            // return view('pages.administrator.asset-disposal.public.disposal-report', compact('transaction'));
        }

        return view('pages.administrator.asset-disposal.public.disposal-review', compact('transaction'));
    }

    public function buyerConfirm(Request $request, $id) {
        $request->validate([
            'agreement' => 'accepted' // Ensures checkbox is checked
        ]);

        try {
            DB::beginTransaction();

            $transaction = AssetDisposal::findOrFail(decrypt($id));

            $transaction->logs()->create([
                'status' => AssetDisposal::STATUS_COMPLETE,
                'comments' => "Buyer confirmed its purchase IP:{$request->ip()}",
                'actioned_at' => now()
            ]);

            $transaction->update([
                'doc_status' => AssetDisposal::DOC_STATUS_COMPLETE,
                'current_status' => AssetDisposal::STATUS_COMPLETE,
                'buyer_confirmed' => true,
                'validated_at' => now(),
                'buyer_ip' => $request->ip() // Optional: for audit trail
            ]);

            DB::commit();

            // dd($transaction);

            return redirect()->signedRoute('disposal.public-review', ['id' => $id])
                        ->with('success', 'Purchase confirmed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with("error", 'An error occured. Please try again letter');
            // dd($e);
        }
    }

    public function verifySignature($id, $type, $pathId = null)
    {
        $transaction = AssetDisposal::with('approvalPaths.employee.position', 'approvalPaths.logs')->findOrFail(decrypt($id));

        $person = [];
        if ($type === 'approver') {
            // Map signatures level1, level2, or level3 from your signatures array
            $path = $transaction->approvalPaths->firstWhere('id', decrypt($pathId));
            $person = [
                'name'     => $path->employee->fullname,
                'role'     => $path->employee->position->nama,
                'org'      => 'PT. Hisamitsu Pharma Indonesia',
                'date'     => $path->logs->firstWhere('status', AssetDisposal::STATUS_APPROVED)->actioned_at->format('d-M-Y'),
                'verified' => true,
                'type'     => $type
            ];
        } else if ($type === 'submitter') {
            $person = [
                'name'     => $transaction->requester->fullname,
                'role'     => $transaction->requester->position->nama,
                'org'      => 'PT. Hisamitsu Pharma Indonesia',
                'date'     => $transaction->created_at->format('d-M-Y'),
                'verified' => true,
                'type'     => $type
            ];
        } else {
            $person = [
                'name'     => $transaction->buyer_name,
                'role'     => 'Authorized Buyer',
                'email'    => $transaction->buyer_email,
                'phone'    => $transaction->buyer_phone,
                'org'      => $transaction->buyer_address,
                'date'     => $transaction->validated_at->format('d-M-Y'), // Or confirmation date
                'verified' => true,
                'type'     => $type
            ];
        }

        return view('pages.administrator.asset-disposal.public.signature', compact('person', 'transaction', 'type'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancel($id, Request $request)
    {
        $id = decrypt($id);
        $assetDisposal = AssetDisposal::find($id)->load('approvalPaths');
        try {
            DB::beginTransaction();
            $assetDisposal->update([
                'doc_status' => AssetDisposal::DOC_STATUS_CANCELED,
            ]);

            $assetDisposal->disposalItems->each(function($item) {
                $item->itAsset->update([
                    'status' => $item->current_status,
                ]);
            });

            $assetDisposal->logs()->create([
                'disposal_approval_path_id' => $assetDisposal->currentStep()->id,
                'status' => AssetDisposal::STATUS_CANCELED,
                'comments' => "User canceled the proccess, reason: ".$request->reason,
                'actioned_at' => now()
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Disposal request successfully canceled');
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            return redirect()->back()->with('error', 'An error occured');
        }
    }
}
