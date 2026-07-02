<?php

namespace App\Http\Controllers;

use App\Exports\ITAssetTemplateExport;
use App\Models\Area;
use App\Models\AssetDisposal;
use App\Models\AssetHistory;
use App\Models\AssetType;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ITAsset;
use App\Models\Log;
use App\Models\Position;
use App\Models\ServiceTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isEmpty;

class ITAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getLatestAssetCode()
    {
        $todayDate = date('ym');
        $itAsset = ITAsset::where('asset_code', 'LIKE', "%$todayDate%")->latest('id')->first();
        $latestAssetCode = $itAsset ? explode('-', $itAsset->asset_code) : null;
        $nextAssetCode = $latestAssetCode? str_pad((int)$latestAssetCode[1] + 1, 4, '0', STR_PAD_LEFT) : '0001';
        return compact('latestAssetCode', 'nextAssetCode');
    }

    public function index()
    {
        $statuses = [
            [
                'value' => ITAsset::STATUS_ACTIVE,
                'label' => 'Active',
            ], [
                'value' => ITAsset::STATUS_BACKUP,
                'label' => 'Backup',
            ], [
                'value' => ITAsset::STATUS_BROKEN,
                'label' => 'Broken',
            ], 
        ];

        $itAssetIds = ITAsset::all()->pluck('employee_id')->values()->toArray();

        $assetCodes = $this->getLatestAssetCode();
        $employees = Employee::with('department', 'position')->get();
        // $employees = collect($employees)->map(function($employee) {
        //     return (object)[
        //         'id' => $employee->id,
        //         'fullname' => $employee->fullname,
        //         'department_name' => $employee->department ? $employee->department->name : 'N/A'
        //     ];
        // });
        $assetTypes = AssetType::latest()->get();
        $departments = Department::all();
        $area = Area::all();
        
        return view('pages.administrator.it-asset.index', compact('assetCodes', 'employees', 'assetTypes', 'itAssetIds', 'statuses', 'departments', 'area'));
    }

    public function disposed(Request $request)
    {
        return view("pages.administrator.it-asset.disposed.index");
    }

    public function getItAssets(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $itAssets = null;

            if ($request->status == 'disposed') {
                $itAssets = ITAsset::where('status', ITAsset::STATUS_DISPOSED);
            } else {
                $itAssets = ITAsset::whereNot('status', ITAsset::STATUS_DISPOSED);
            }

            if($request->has('asset_type')) $itAssets->whereHas('assetType', function($q) use ($request) {
                $q->where('id', $request->asset_type);
            });
            if($request->has('area')) $itAssets->where('employee_area', $request->area);
            if($request->has('department')) $itAssets->where('employee_department', $request->department);

            $itAssets = $itAssets->latest('year_registered')->get()->load(['employee', 'assetType', 'disposalItems.assetDisposal']);

            // foreach ($itAssets as $key => $itAsset) {
            //     dump(
            //         $itAsset->currentActiveDisposal()->isEmpty()
            //     );
            // }

            $dataTable = DataTables::of($itAssets)
                ->addColumn('is_disabled', function($item) {
                    return !$item->currentActiveDisposal()->isEmpty() || !$item->asset_code? true : false;
                })
                ->addColumn('disabled_message', function($item) {
                    return !$item->currentActiveDisposal()->isEmpty()? 'IT Asset on Disposal Proccess' : (!$item->asset_code? 'Asset code is required' : null);
                })
                ->addColumn('disposal_url', function($item) {
                    return !$item->currentActiveDisposal()->isEmpty()? route('asset-disposal.show', [encrypt($item->latestDisposalItem()->assetDisposal->id)]) : null;
                })
                ->addColumn('show_url', function($item) {
                    return route("it_asset.show", ['id' => encrypt($item->id)]);
                })
                ->addColumn('delete_url', function($item) {
                    return route('it_asset.destroy', encrypt($item->id));
                })
                ->addColumn('encrypted', function($item) {
                    return encrypt($item->id);
                });
            if ($request->status !== 'disposed') {
                $dataTable->addColumn('edit_url', function($item) {
                    return route('it_asset.edit', encrypt($item->id));
                })
                // ->addColumn('delete_url', function($item) {
                //     return route('it_asset.destroy', encrypt($item->id));
                // })
                ->addColumn('movement_url', function($item) {
                    return route('it_asset.movement', encrypt($item->id));
                });
            }

            return $dataTable->make(true);
        }
    }

    public function getItAsset(Request $request, $id)
    {
        $itAsset = ITAsset::where('id', decrypt($id))->with(['employee', 'assetType'])->first();
        return response()->json($itAsset);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     // return view('pages.administrator.it-asset.create');
    // }

    public function owners(Request $request) {
        $search = $request->search;

        $employees = Employee::with(['department', 'position'])
            ->when($search, function($query) use ($search) {
                $query->where('fullname', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%")
                    ->orWhereHas('department', function($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('position', function($q) use ($search) {
                        $q->where('nama', 'like', "%$search%");
                    });
            })
            ->limit(20) // Batasi hanya 20 data per request agar ringan
            ->get()
            ->map(function($emp) {
                return [
                    'id' => encrypt($emp->id), // Gunakan encrypted ID sesuai kebutuhan Anda
                    'text' => "{$emp->fullname} - " . ($emp->position->nama ?? 'N/A') . " (" . ($emp->department->name ?? 'N/A') . ")"
                ];
            });

        return response()->json($employees);
    }

    public function preview(Request $request) {
        $path = $request->file('file')->getRealPath();

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Konversi sheet ke collection dan filter row kosong di awal
        $data = collect($sheet->toArray(null, true, true, false));
            // ->filter(fn($row) => !empty($row[0]));

        $assetTypes = AssetType::all(); // Gunakan all() jika memang butuh semua untuk dropdown/find
        $employees = Employee::with('department', 'position', 'area')->whereIn('nik', $data->pluck(8)->unique()->values()->toArray())->get()->keyBy('nik'); // Eager load untuk menghindari N+1

        // Mapping rows
        $rows = $data->skip(1)->map(function($row) use ($assetTypes, $employees) {
            // $assetType = $assetTypes->where('name', $row[1])->first();
            return [
                'asset_code'      => str_replace(' ', '', $row[0]),
                // 'asset_type'      => $assetType?->name,
                'asset_type'      => strtolower($row[1]),
                'brand'           => $row[2],
                'specification'   => $row[3],
                'software'        => $row[4],
                'year_registered' => Carbon::parse($row[5])->format('Y-m-d'),
                'price'           => (int) str_replace([',', 'Rp'], '', $row[6]),
                'status'          => strtolower($row[7]),
                'employee'        => $employees->get($row[8])? [
                    'encrypted_id' => encrypt($employees->get($row[8])->id),
                    'fullname'     => $employees->get($row[8])->fullname,
                    'nik'          => $employees->get($row[8])->nik,
                    'department'   => $employees->get($row[8])->department?->name,
                    'position'     => $employees->get($row[8])->position?->nama,
                ] : null
            ];
        });

        $rows = $rows->filter(fn($row) => !empty($row['brand'])); // Filter row yang asset_code-nya kosong

        // --- OPTIMASI DI SINI ---
        // 1. Ambil hanya kolom yang dibutuhkan dari DB (Hemat RAM)
        // 2. Gunakan keyBy agar pencarian data existing menjadi O(1) bukan O(n)
        $itAssets = ITAsset::select('asset_code', 'employee_id', 'status')
            ->whereIn('asset_code', $rows->pluck('asset_code'))
            ->get()->load('employee.department', 'employee.position', 'employee.area')
            ->keyBy('asset_code');

        $existingAssetsCode = $itAssets->keys()->toArray();

        // Pisahkan data baru dan data lama menggunakan collection methods
        $newItAssets = $rows->reject(fn($row) => isset($itAssets[$row['asset_code']]));

        $existingItAssets = $rows->filter(fn($row) => isset($itAssets[$row['asset_code']]))
            ->map(function($row) use ($itAssets) {
                $matchingAsset = $itAssets[$row['asset_code']];
                $row['employee_id'] = $matchingAsset->employee_id;
                $row['employee_fullname'] = $matchingAsset->employee_id ? $matchingAsset->employee->fullname ?? 'N/A' : 'N/A';
                $row['department'] = $matchingAsset->employee_id ? $matchingAsset->employee->department->name ?? 'N/A' : 'N/A';
                $row['position'] = $matchingAsset->employee_id ? $matchingAsset->employee->position->nama ?? 'N/A' : 'N/A';
                $row['area'] = $matchingAsset->employee_id ? $matchingAsset->employee->area->name ?? 'N/A' : 'N/A';
                $row['old_status']  = $matchingAsset->status;
                return $row;
            });

        return view('pages.administrator.it-asset.import-preview', compact('newItAssets', 'existingItAssets', 'assetTypes'));
    }

    public function upsert(Request $request) 
    {
        if ($request->ajax()) {
            try {
                DB::beginTransaction();

                $itAssetsData = collect($request->it_assets);

                // Eager load IT Assets yang sudah ada untuk perbandingan
                $existingITAssets = ITAsset::whereIn('asset_code', $itAssetsData->pluck('asset_code'))
                    ->get()
                    ->keyBy('asset_code');

                $user = Auth::user();

                $employees = [];
                if($request->type == 'insert') $employees = Employee::with('department', 'position', 'area')->whereIn('id', $itAssetsData->pluck('pic')->map(fn($encryptedId) => decrypt($encryptedId)))->get()->keyBy('id');

                $itAssetsData->each(function($itAsset) use ($existingITAssets, $user, $employees) {
                    // Tentukan kolom general yang boleh di-update maupun di-insert
                    $allowedData = [
                        'brand'           => $itAsset['brand'],
                        'year_registered' => $itAsset['year_registered'],
                        'price'           => $itAsset['price'],
                        'specification'   => $itAsset['specification'],
                        'software'        => $itAsset['software'],
                        'status'          => $itAsset['status'],
                        'asset_type_id'   => decrypt($itAsset['asset_type_id']),
                    ];

                    // Cari model berdasarkan asset_code
                    $model = $existingITAssets->get($itAsset['asset_code']) ?? new ITAsset();
                    $isNew = !$model->exists;

                    if ($isNew) {
                        // --- LOGIKA UNTUK DATA BARU ---
                        $model->asset_code = $itAsset['asset_code'];
                        
                        // Ambil data karyawan berdasarkan PIC yang dikirim di request
                        $employeeId = decrypt($itAsset['pic']);
                        $emp = $employees->get($employeeId);

                        if ($emp) {
                            $model->employee_id         = $emp->id;
                            $model->employee_fullname   = $emp->fullname;
                            $model->employee_nik        = $emp->nik;
                            $model->employee_department = $emp->department->name ?? 'N/A';
                            $model->employee_position   = $emp->position->nama ?? 'N/A';
                            $model->employee_area       = $emp->area->name ?? 'N/A';
                        }
                        
                        $model->fill($allowedData);
                    } else {
                        // --- LOGIKA UNTUK UPDATE ---
                        // Hanya isi kolom general, data employee tidak disentuh (untouched)
                        $model->fill($allowedData);
                    }

                    // Proses Audit Log (Hanya kolom dirty)
                    $originalData = $model->getRawOriginal();
                    
                    if ($model->isDirty()) {
                        $allowedForAudit = ['brand', 'year_registered', 'price', 'specification', 'software', 'status'];
                        $changes = $model->getDirty();
                        
                        // Filter agar hanya kolom bisnis yang masuk log (exclude IDs)
                        $filteredChanges = array_intersect_key($changes, array_flip($allowedForAudit));

                        $model->save();

                        // if (!empty($filteredChanges)) {
                        //     $fromParts = [];
                        //     $toParts = [];

                        //     foreach ($filteredChanges as $column => $newValue) {
                        //         $oldValue = $isNew ? 'N/A' : ($originalData[$column] ?? 'N/A');
                        //         $fromParts[] = "$column: $oldValue";
                        //         $toParts[] = "$column: $newValue";
                        //     }

                        //     AssetHistory::create([
                        //         'it_asset_id'    => $model->id,
                        //         'action_type'    => 'bulk_import',
                        //         'description'    => "Bulk import " . ($isNew ? 'INSERT' : 'UPDATE') . ". " . 
                        //                             "Modified: " . implode(', ', array_keys($filteredChanges)) . 
                        //                             " | FROM [" . implode(', ', $fromParts) . "]" . 
                        //                             " | TO [" . implode(', ', $toParts) . "]",
                        //         'user_id'        => $user->id,
                        //     ]);
                        // }
                    }
                });

                // Log aktivitas umum
                Log::create([
                    'user_id'     => $user->id,
                    'ip_address'  => $request->ip(),
                    'action'      => $request->type == 'update' ? 'update' : 'insert',
                    'description' => "Bulk Import IT Asset (General Data Only). Total: " . $itAssetsData->count() . " rows."
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'General data successfully processed',
                    'status'  => 'success',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                $message = $e->getMessage();
                $relevantPart = Str::between($message, '1062 ', 'for key');

                return response()->json([
                    'message' => 'Error: ' . $relevantPart,
                    'status'  => 'error'
                ]);
            }
        }
    }

    public function download() {
        return Excel::download(new ITAssetTemplateExport, 'template_it_asset.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Check if asset_code already exists
            $existingAsset = ITAsset::where('asset_code', $request->asset_code)->first();
            if ($existingAsset) {
                return response()->json([
                    'message' => 'Asset code "' . $request->asset_code . '" already exists.',
                    'status' => 'info'
                ]);
            }

            DB::beginTransaction();

            $employee = Employee::find(decrypt($request->pic));

            $itAsset = ITAsset::create([
                'asset_code' => $request->asset_code,
                'brand' => $request->brand,
                'status' => $request->status,
                'asset_type_id' => $request->asset_type_id,
                'year_registered' => \Carbon\Carbon::createFromFormat('Y-M-d', $request->year_registered)->format('Y-m-d'),
                'price' => $request->price,
                'specification' => $request->specification,
                'software' => $request->software,
                'employee_id' => $employee->id,
                'employee_fullname' => $employee->fullname,
                'employee_nik' => $employee->nik,
                'employee_department' => $employee->department->name,
                'employee_position' => $employee->position?->nama,
                'employee_area' => $employee->area?->name,
                
            ]);

            $user = Auth::user();

            $log = Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => "Create new IT Asset $itAsset->asset_code($itAsset->brand). Assigned to {$itAsset->employee_fullname} from {$itAsset->employee_department} Department"
            ]);

            DB::commit();
            return response()->json([
                'message' => 'IT Asset ' . '"' . $itAsset->asset_code . '"' . ' has been stored successfully.',
                'redirect' => route('it_asset.index'),
                'status' => 'success'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to store IT Asset: ' . $th->getMessage(),
                'code' => $th->getCode(),
                'status' => 'error'
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
        // old code before refactor
        // $asset = ITAsset::findOrFail(decrypt($id))->load('histories');
        // $histories = $asset->histories->sortByDesc('created_at');
        // $histories = AssetHistory::where('it_asset_id', $asset->id)->latest()->get()->load('user.employee.department');
        // end of old code before refactor

        $asset = ITAsset::findOrFail(decrypt($id))->load('histories', 'serviceTickets',);

        $histories = collect($asset->getRelations())->map(function($relations) {
            return $relations->map(function($relation) {
                if ($relation instanceof ServiceTicket) {
                    $ticket = $relation;
                    return [
                        'ticket_number' => $ticket->no_ticket,
                        'created_at' => $ticket->created_at,
                        'action_type' => 'Service Ticket',
                        'description' => "IT Asset assigned to Service Ticket #{$ticket->no_ticket} - {$ticket->subject}",
                        'url' => URL::signedRoute('service-ticket.approve-workspace', ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_CC)])
                    ];
                } if($relation instanceof AssetHistory) {
                    $assetHistory = $relation;
                    return [
                        'created_at' => $assetHistory->created_at,
                        'action_type' => str_replace('_', ' ', ucfirst($assetHistory->action_type)),
                        'description' => $assetHistory->description,
                        'url' => $relation->action_type == AssetHistory::TYPE_DISPOSED? route('asset-disposal.show', [encrypt($assetHistory->reference->id)]) : null
                    ];
                } else {
                    return $relation;
                }
            });
        })->collapse()->sortByDesc('created_at');

        return view('pages.administrator.it-asset.show', compact('asset', 'histories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($itAsset)
    {
        $statuses = [
            [
                'value' => ITAsset::STATUS_ACTIVE,
                'label' => 'Active',
            ], [
                'value' => ITAsset::STATUS_BACKUP,
                'label' => 'Backup',
            ], [
                'value' => ITAsset::STATUS_BROKEN,
                'label' => 'Broken',
            ], 
        ];

        $encryptedId = $itAsset;
        $id = decrypt($itAsset);
        $itAssets = ITAsset::all();
        $itAssetEmpIds = $itAssets->pluck('employee_id')->toArray();
        $itAsset = $itAssets->find($id);
        $assetTypes = AssetType::latest()->get();
        $employees = Employee::with('department')->get();
        return view('pages.administrator.it-asset.edit', compact('id', 'itAsset', 'assetTypes', 'employees', 'encryptedId', 'itAssetEmpIds', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $itAsset)
    {
        if ($request->ajax()) {
            try {
                $request->validate([
                    'asset_code' => 'required|unique:it_assets,asset_code,' . decrypt($itAsset) . ',id',
                    'brand' => 'required',
                    'status' => 'required',
                    'price' => 'required|numeric',
                    'asset_type_id' => 'required|exists:asset_types,id',
                    'year_registered' => 'required',
                ]);

                DB::beginTransaction(); // Bagus untuk integritas data

                $itAsset = ITAsset::findOrFail(decrypt($itAsset));
                
                // Isi data baru ke model (masih di memori, belum masuk DB)
                $itAsset->asset_code = $request->asset_code;
                $itAsset->brand = $request->brand;
                if ($request->status == ITAsset::STATUS_ACTIVE || $request->status == ITAsset::STATUS_BACKUP) {
                    $itAsset->status = $request->status;
                }
                $itAsset->price = $request->price;
                $itAsset->asset_type_id = (int)$request->asset_type_id;
                $itAsset->year_registered = \Carbon\Carbon::parse($request->year_registered)->format('Y-m-d');
                $itAsset->specification = $request->specification;
                $itAsset->software = $request->software;

                $return = [
                    'message' => 'No changes were made.',
                    'status' => 'success',
                ];
                
                if ($itAsset->isDirty()) {
                    // 1. Ambil kolom yang berubah SEBELUM di-save
                    $changes = $itAsset->getDirty();
                    $original = $itAsset->getRawOriginal();

                    // 2. Buat string deskripsi audit
                    $fromParts = [];
                    $toParts = [];

                    foreach ($changes as $column => $newValue) {
                        $oldValue = $original[$column] ?? 'N/A';
                        $fromParts[] = "$column: $oldValue";
                        $toParts[] = "$column: $newValue";
                    }

                    $description = "Updated IT Asset {$itAsset->asset_code} - {$itAsset->brand}. Modified: " . implode(', ', array_keys($changes)) . 
                                " | FROM [" . implode(', ', $fromParts) . "]" . 
                                " | TO [" . implode(', ', $toParts) . "]";

                    // 3. Baru lakukan save ke Database
                    $itAsset->save();

                    // 4. Catat History
                    Log::create([
                        'user_id' => Auth::id(),
                        'ip_address' => $request->ip(),
                        'action' => 'update',
                        'description' => $description
                    ]);

                    $return['message'] = 'Successfully updated the data.';
                }

                DB::commit();
                return response()->json($return);

            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to update IT Asset: ' . $th->getMessage(),
                    'status'  => 'error',
                ]);
            }
        }
    }

    public function updateStatus(Request $request, $id) {
        if ($request->ajax()) {
            try {
                DB::beginTransaction();

                $itAsset = ITAsset::findOrFail(decrypt($id));
                $oldStatus = $itAsset->status;
                $itAsset->status = $request->status;
                $itAsset->save();


                Log::create([
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'action' => 'update',
                    'description' => "Updated status of IT Asset {$itAsset->asset_code} - {$itAsset->brand} from $oldStatus to {$request->status}"
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'Status updated successfully.',
                    'status' => 'success'
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to update status: ' . $th->getMessage(),
                    'status'  => 'error',
                ]);
            }
        }
    }

    public function movement($id, Request $request) {
        $employees = Employee::with('department')->get();
        $itAsset = ITAsset::find(decrypt($id));
        return view('pages.administrator.it-asset.movement', compact('employees', 'itAsset'));
    }

    public function movementUpdate($id, Request $request) {
        if ($request->ajax()) {
            try {
                DB::beginTransaction();

                $id = decrypt($id);

                $itAsset = ITAsset::findOrFail($id);
            
                $request->validate([
                    'to_pic' => 'required|not_in:'.$itAsset->employee_id,
                    'reason' => 'required',
                ]);

                $fromPic = $itAsset->employee;
                $toPic = Employee::find(decrypt($request->to_pic));
                $user = Auth::user();

                if ($fromPic->id !== $toPic->id) {
                    $itAsset->update([
                        'employee_id' => $toPic->id,
                        'employee_fullname' => $toPic->fullname,
                        'employee_nik' => $toPic->nik,
                        'employee_department' => $toPic->department->name,
                        'employee_position' => $toPic->position?->nama,
                        'employee_area' => $toPic->area?->name,
                    ]);
                    $description = "{$user->employee->fullname}-{$user->employee->position?->nama}({$user->employee->department->name}) 
                                        transfer a asset $itAsset->brand from $fromPic->fullname-{$fromPic->position?->nama}({$fromPic->department->name}) 
                                        to $toPic->fullname-{$toPic->position?->nama}({$toPic->department->name}). Reason: {$request->reason}";

                    Log::create([
                        'user_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'action' => 'update',
                        'description' => $description
                    ]);

                    AssetHistory::create([
                        'it_asset_id' => $itAsset->id,
                        'action_type' => 'movement',
                        'from_value' => $fromPic->id,
                        'to_value' => $toPic->id,
                        'user_id' => $user->id,
                        'description' => $description
                    ]);

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Asset movement has been recorded successfully.'
                    ], 200);
                }
                return response()->json([
                    'status' => 'info',
                    'message' => 'No changes were made.'
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to process movement.',
                    'error' => $e->getMessage() // Good for debugging
                ], 500);
            }
        }
    }   

    public function destroy($id, Request $request)
    {
        if ($request->ajax()) {
            try {
                DB::beginTransaction();

                $itAsset = ITAsset::findOrFail(decrypt($id));

                $movements = $itAsset->histories->where('action_type', AssetHistory::TYPE_MOVEMENT);

                if ($movements->isNotEmpty() || $itAsset->serviceTickets->isNotEmpty()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot delete IT Asset with associated movement or service tickets.'
                    ], 422);
                }

                $itAsset->delete();

                Log::create([
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'action' => 'delete',
                    'description' => "Deleted IT Asset {$itAsset->asset_code} - {$itAsset->brand}"
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'IT Asset has been deleted successfully.',
                    'status' => 'success',
                    'redirect' => route('it_asset.index')
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to delete IT Asset: ' . $th->getMessage(),
                    'status'  => 'error',
                ]);
            }
        }
    }

    public function printPreview(Request $request) {
        $itAssets = ITAsset::whereIn('asset_code', collect($request->asset_codes))->get();
        $selectedAssets = collect($itAssets)->map(function($asset) {
            return [
                'asset_code' => $asset->asset_code,

            ];
        });
        $itAssetCount = $itAssets->count();

        return view('pages.administrator.it-asset.print.index', compact('itAssetCount', 'selectedAssets'));
    }

    public function print(Request $request) {
        $itAssets = ITAsset::whereIn('asset_code', collect($request->assets)->flatten()->toArray())->get();

        $selectedAssets = collect($request->assets);
        $itAssetCount = $itAssets->flatten(1)->count();
        $customPaper = [0, 0, 467.71, 581.10];

        $selectedAssets = $selectedAssets->map(function($paper) use ($itAssets) {
            $paper = collect($paper)->map(function($assetCode) use ($itAssets) {
                $asset = $itAssets->where('asset_code', $assetCode)->first();
                return [
                    'asset_code' => $asset->asset_code,
                    // 'qr_code' => QrCode::format('svg')->size(60)->generate(json_encode([
                    //     'asset_code' => $asset->asset_code,
                    //     'brand' => $asset->brand,
                    //     'specification' => $asset->specification,
                    //     'employee' => $asset->employee_area,
                    // ])),
                    'qr_code' => QrCode::format('svg')->size(60)->generate(
                        URL::signedRoute('public.asset.qrcode', ['assetCode' => $asset->asset_code])
                    ),
                ];
            });
            return $paper;
        })->toArray();

        return Pdf::loadView('pages.administrator.it-asset.print.print', compact('itAssetCount', 'selectedAssets'))
            ->setPaper($customPaper, 'portrait')
            ->stream("IT_Asset_Print_" . date('YmdHis') . ".pdf");
    }

    public function assetQRCode(Request $request, $assetCode) {
        $itAsset = ITAsset::firstWhere('asset_code', $assetCode);

        $asset = [
            'asset_code' => $itAsset->asset_code,
            'brand' => $itAsset->brand,
            'employee' => $itAsset->employee->fullname?? 'N/A',
            'asset_type' => $itAsset->assetType->name,
            'area' => $itAsset->employee_area?? 'N/A',
            'hardware' => $itAsset->specification,
            'software' => $itAsset->software,
            'year_registered' => $itAsset->year_registered->format('d-M-Y'),
        ];

        return view('pages.administrator.it-asset.public.detail', compact('asset'));
    }
}