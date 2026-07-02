<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ItsmPriority;
use App\Models\Log;
use App\Models\RiskRegister;
use App\Models\ServiceStatusHistory;
use App\Models\ServiceTicket;
use App\Models\ServiceTicketMessage;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use PHPUnit\Framework\Attributes\Ticket;
use Yajra\DataTables\Facades\DataTables;

class ServiceTicketController extends Controller
{
    public function getRelatedTickets() {
        $user = Auth::user()->load('employee.department', 'employee.position', 'employee.level', 'employee.area');
        $employeeId = $user->employee_id;
        $encryptedApproverId = encrypt($employeeId);

        /*
        |--------------------------------------------------------------------------
        | 3. Related Tickets (single query instead of 3)
        |--------------------------------------------------------------------------
        */
        $relatedTickets = ServiceTicket::with('submitter.department', 'priority')
            ->whereNotIn('current_status', [
                ServiceStatusHistory::STATUS_CLOSED,
                ServiceStatusHistory::STATUS_CANCELLED
            ])
            ->where(function ($q) use ($employeeId) {
                $q->where('supervisor_id', $employeeId)
                ->orWhere('dept_head_id', $employeeId)
                ->orWhereHas('employeeCcs', function ($q2) use ($employeeId) {
                    $q2->where('employee_id', $employeeId);
                });
            })
            
            ->get();
        /*
        |--------------------------------------------------------------------------
        | 4. Assign role + action in ONE loop
        |--------------------------------------------------------------------------
        */
        $myRelatedTickets = $relatedTickets->map(function ($ticket) use ($employeeId, $encryptedApproverId) {

            if ($ticket->supervisor_id == $employeeId) {
                $role = ServiceTicket::ROLE_SUPERVISOR;
                $label = 'Approve as Supervisor';
                $route = "service-ticket.approve-workspace";
                $target = '_blank';

                $params = [
                    'id' => encrypt($ticket->id),
                    'role' => encrypt($role),
                    'approverId' => $encryptedApproverId,
                    'target' => '_blank'
                ];

            } elseif ($ticket->dept_head_id == $employeeId) {
                $role = ServiceTicket::ROLE_DEPT_HEAD;
                $label = 'Approve as Department Head';
                $route = "service-ticket.approve-workspace";
                $target = '_blank';

                $params = [
                    'id' => encrypt($ticket->id),
                    'role' => encrypt($role),
                    'approverId' => $encryptedApproverId,
                ];

            } else {
                $role = ServiceTicket::ROLE_CC;
                $label = 'View as CC';
                $route = Auth::user()->can('emp.menu')? "service-desk.workspace" : 'myservice-desk.workspace';
                $target = null;

                $params = [
                    'id' => encrypt($ticket->id),
                    'role' => encrypt($role)
                ];
            }

            $ticket->role = $role;
            $ticket->history_action = [
                'redirect_url' => URL::signedRoute($route, $params),
                'label' => $label,
                'target' => $target?? ''
            ];

            return $ticket;
        })->sortByDesc('no_ticket');

        return $myRelatedTickets;
    }

    public function getData(Request $request) {
        $user = Auth::user()->load('employee.department', 'employee.position', 'employee.level', 'employee.area');

        if ($request->ajax()) {

            $query = ServiceTicket::with(['submitter.department', 'submitter.position', 'priority', 'employeeCcs', 'serviceChange'])->latest();

            $user = auth()->user();
            $employee = $user->employee;

            if (!$request->has('all')) {
                if($request->has('my')) $query->where(fn($q) => 
                    $q->where('submitter_id', $employee->id)
                    ->orWhere('report_for_id', $employee->id)
                );
                
                else $query->where(fn($q) => 
                    $q->where('submitter_id', $employee->id)
                    ->orWhere('report_for_id', $employee->id)
                    ->orWhereHas('employeeCcs', function ($q2) use ($employee) {
                        $q2->where('employee_id', $employee->id);
                    })
                    ->orWhere('supervisor_id', $employee->id)
                    ->orWhere('dept_head_id', $employee->id)
                    // ->orWhereHas('serviceChange', function ($q3) use ($employee) {
                    //     $q3->where('approver_id', $employee->id);
                    // })
                );
            }

            if ($request->filter == 'open') {
                $query->where(fn($q) => 
                    $q->where('current_status', '!=', ServiceStatusHistory::STATUS_CLOSED)
                    ->where('current_status', '!=', ServiceStatusHistory::STATUS_CANCELLED)
                );
            } else if ($request->filter == 'closed' || $request->filter == 'history') {
                $query->where(fn($q) => 
                    $q->where('current_status', ServiceStatusHistory::STATUS_CANCELLED)->orWhere('current_status', ServiceStatusHistory::STATUS_CLOSED)
                );
            }

            $serviceTickets = $query->get();
            
            if ($request->filter == 'history') {
                $supervisors = $serviceTickets
                ->where("supervisor_id", $employee->id)
                ->where('supervisor_approval', '!=', ServiceTicket::APPROVAL_STATUS_PENDING)
                ->where('submitter_id', '!=', $employee->id)
                ->where('report_for_id', '!=', $employee->id);

                $deptHeads = $serviceTickets
                ->where("dept_head_id", $employee->id)
                ->where('dept_head_approval', '!=', ServiceTicket::APPROVAL_STATUS_PENDING)
                ->where('submitter_id', '!=', $employee->id)
                ->where('report_for_id', '!=', $employee->id);
                
                $serviceChange = $serviceTickets->filter(function($serviceChange) use ($employee) {
                    return $serviceChange->serviceChange && $serviceChange->serviceChange->approver_id === $employee->id;
                });
                $ccs = $serviceTickets->filter(function($ticket) use ($employee) {
                    return $ticket->employeeCcs->contains(function($cc) use ($employee) {
                        return $cc->id === $employee->id;
                    });
                });

                $supervisors->each(function($ticket) use ($employee) {
                    if ($ticket->submitter_id == $employee->id || $ticket->report_for_id == $employee->id) return;
                    $ticket->role = ServiceTicket::ROLE_SUPERVISOR;
                    $ticket->history_action = [
                        'redirect_url' => URL::signedRoute("service-ticket.approve-workspace", ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_SUPERVISOR), 'approverId' => encrypt(Auth::user()->employee_id)]),
                        'label' => 'View as Supervisor',
                    ];
                });
                $deptHeads->each(function($ticket) use ($employee) {
                    if ($ticket->submitter_id == $employee->id || $ticket->report_for_id == $employee->id) return;
                    $ticket->role = ServiceTicket::ROLE_DEPT_HEAD;
                    $ticket->history_action = [
                        'redirect_url' => URL::signedRoute("service-ticket.approve-workspace", ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_DEPT_HEAD), 'approverId' => encrypt(Auth::user()->employee_id)]),
                        'label' => 'View as Department Head',
                    ];
                });
                $serviceChange->each(function($ticket) use ($employee) {
                    if ($ticket->submitter_id == $employee->id || $ticket->report_for_id == $employee->id) return;
                    $ticket->role = ServiceTicket::ROLE_SERVICE_CHANGE;
                    $ticket->history_action = [
                        'redirect_url' => URL::signedRoute("service-change.show", ['id' => encrypt($ticket->serviceChange->id), 'approverId' => encrypt($ticket->serviceChange->approver_id)]),
                        'label' => 'View as Service Change Approver',
                    ];
                });
                $ccs->each(function($ticket) use ($employee) {
                    if ($ticket->submitter_id == $employee->id || $ticket->report_for_id == $employee->id) return;
                    $ticket->role = ServiceTicket::ROLE_CC;
                    $ticket->history_action = [
                        'redirect_url' => URL::signedRoute("service-desk.workspace", ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_CC)]),
                        'label' => 'View as CC',
                    ];
                });
                $serviceTickets = $serviceTickets->merge($supervisors)->merge($deptHeads)->merge($serviceChange)->merge($ccs);  
            }

            elseif($request->has('related')) {
                $serviceTickets = $serviceTickets->merge($this->getRelatedTickets());
                $serviceTickets = $serviceTickets->sortByDesc('no_ticket');
            }

            return DataTables::of($serviceTickets)
                // Tambahkan kolom enkripsi ID untuk keamanan URL
                ->addColumn('encrypted_id', function($ticket) {
                    return encrypt($ticket->id);
                })
                ->addColumn('analyze_url', function($ticket) {
                    return route('service-management.analysis', encrypt($ticket->id));
                })
                // Format waktu untuk frontend
                ->addColumn('created_at_formatted', function($ticket) {
                    return [
                        'display' => $ticket->created_at->diffForHumans(),
                        'timestamp' => $ticket->created_at->timestamp
                    ];
                })
                // Gabungkan info Asset agar JS tidak terlalu berat
                // ->addColumn('asset_info', function($ticket) {
                //     return $ticket->itAsset 
                //         ? $ticket->itAsset->brand . " (" . $ticket->itAsset->asset_code . ")"
                //         : 'No Asset';
                // })
                ->addColumn('main_action', function($ticket) {
                    $id = encrypt($ticket->id);
                    
                    return match ($ticket->current_status) {
                        ServiceStatusHistory::STATUS_OPEN    => [
                            'label' => 'Analyze',
                            'icon'  => 'ri-shield-check-line',
                            'class' => 'btn-outline-primary',
                        ],
                        ServiceStatusHistory::STATUS_PROCESS, ServiceStatusHistory::STATUS_HOLD => [
                            'label' => 'Workspace',
                            'icon'  => 'ri-folder-open-line',
                            'class' => 'btn-outline-primary'
                        ],
                        ServiceStatusHistory::STATUS_CLOSED => [
                            'label' => 'History',
                            'icon'  => 'ri-history-line',
                            'class' => 'btn-outline-secondary'
                        ],
                        ServiceStatusHistory::STATUS_CANCELLED => [
                            'label' => 'History',
                            'icon'  => 'ri-history-line',
                            'class' => 'btn-outline-secondary'
                        ],
                        default => null,
                    };
                })
                ->rawColumns(['no_ticket', 'status']) // Jika Anda ingin kirim HTML langsung (opsional)
                ->make(true);
        }
    }

    public function uploads(Request $request){
        $path = storage_path('app/public/tickets');

        !file_exists($path) && mkdir($path, 0777, true);

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension();

        $path = $file->store('tickets', 'public');

        return response()->json([
            'path'          => $path,
            'original_name' => $originalName,
            'extension'     => $extension,
        ]);
    }

    public function deleteUpload(Request $request) {
        $filePath = storage_path('app/public/' . $request->path);

        if (file_exists($filePath)) {
            unlink($filePath);
            return response()->json([
                'status' => 'success',
                'message' => 'File removed successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File not found.'
            ], 404);
        }
    } 

    public function store(Request $request, $it_initiative = null)
    {
        // dd($request->all(), $it_initiative);
        $it_initiative = $it_initiative? decrypt($it_initiative) : null;

        $processedRequest = $request->all();

        if (request()->has('report_for_id')) {
            try {
                $processedRequest['report_for_id'] = decrypt($request->report_for_id);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'report_for_id' => ['Invalid Report For value.']
                    ]
                ], 422);
            }
        }

        $validator = Validator::make($processedRequest, [
            'subject'       => 'required|min:5',
            'description'   => 'required',
            'pics'          => 'required_if:report_type,other',

            // Validasi File Multi-upload
            'attachments'   => 'nullable|array',
            'attachments.*.extension' => 'in:jpg,jpeg,png,pdf,zip,docx,doc,xlsx,xls,pptx,ppt,csv,txt', // Max 5MB per file
        ], [
            'asset_id_self.required_if' => 'Please select your IT asset.',
            'pics.required_if'          => 'Please select which employee has the issue.',
            'reported_dept.required_if' => 'Department is required for manual reporting.',
            'attachments.*.mimes'       => 'Only Images, PDF, and ZIP files are allowed.',
            'attachments.*.max'         => 'Each file must not exceed 5MB.',
        ]);

        if($it_initiative) $validator->addRules([
            'category' => 'required',
            'catalog' => 'required',
            'report_for_id' => 'exists:employees,id',
        ]); 
        else $validator->addRules([
            'category' => 'required',
        ]);

        $validated = $validator->validate();

        try {
            DB::beginTransaction();

            // 2. Generate Nomor Tiket (Contoh: TK-260305-001)
            $date = date('ymd');
            $latestTicket = ServiceTicket::where('no_ticket', 'like', 'TK' . $date . '-%')->latest()->first();
            $sequence = $latestTicket ? intval(substr($latestTicket->no_ticket, -3)) + 1 : 1;
            $noTicket = 'TK' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $employee = auth()->user()->employee;

            $ticketData = [
                'no_ticket'      => $noTicket,
                'subject'        => $validated['subject'],
                'description'    => $validated['description'],
                'submitter_id'   => $employee->id,
                'category'       => $validated['category'],
                'catalog'        => $validated['catalog']?? null,
                'current_status' => ServiceStatusHistory::STATUS_OPEN,

                'employee_nik'        => $employee->nik,
                'employee_department' => $employee->department?->name,
                'employee_position'   => $employee->position?->nama,
                'employee_area'       => $employee->area?->name,
            ];

            if ($it_initiative) {
                $reportFor = Employee::findOrFail($validated['report_for_id']);
                $ticketData['type'] = ServiceTicket::TYPE_IT_INITIATIVE;
                $ticketData['report_for_id'] = $reportFor->id;
            }
            
            $ticket = ServiceTicket::create($ticketData);

            if (isset($request->ccs)) {
                $ccData = collect($request->ccs)->map(function($cc) {
                    return [
                        'employee_id' => decrypt($cc),
                    ];
                })->toArray();
                $result = $ticket->employeeCcs()->sync($ccData);
            }

            $history = $ticket->histories()->create([
                'from_status' => ServiceStatusHistory::STATUS_OPEN,
                'to_status' => ServiceStatusHistory::STATUS_OPEN,
                'employee_id' => Auth::user()->employee->id,
                'note' => 'Initial proccess',
                'started_at' => now(),
            ]);

            $messages = $ticket->messages()->createMany([
                [
                    'sender_id' => $ticket->submitter_id,
                    'role' => $it_initiative ? ServiceTicket::ROLE_IT : ServiceTicket::ROLE_USER,
                    'message' => $ticket->description,
                    'is_internal' => false,
                ], [
                    // 'sender_id' => $ticket->submitter_id,
                    'role' => ServiceTicket::ROLE_SYSTEM,
                    'message' => 'Ticket created with subject: ' . $ticket->subject. '. Waiting for IT Team response',
                    'is_internal' => false,
                ]
            ]);

            $medias = [];
            if (isset($request->attachments)) {
                foreach ($request->attachments as $path => $file) {
                    $medias[] = $messages->first()->media()->create([
                        'path'      => $path,
                        'name'      => $file['original_name'],
                        'extension' => $file['extension'],
                    ]);
                }
            }

            Log::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => "User " . auth()->user()->employee->fullname . " created a service ticket with Ticket Number: {$ticket->no_ticket} and subject '{$ticket->subject}'"
            ]);

            DB::commit();
            
            // $ticket->employeeCcs->load('user')->each(function($cc) use ($ticket) {
            //     $cc->user->notify(new TicketNotification($ticket, "Anda ditambahkan sebagai CC pada tiket ini.", ServiceTicket::ROLE_CC));
            // });

            // if ($ticket->report_for_id) {
            //     $reportFor = User::firstWhere('employee_id', $ticket->report_for_id);
            //     if ($reportFor) {
            //         $reportFor->notify(new TicketNotification($ticket, "Tiket baru dilaporkan untuk Anda.", ServiceTicket::ROLE_USER));
            //     }
            // }

            $redVal = $request->has('red')? decrypt($request->red) : null;

            return response()->json([
                'success' => true,
                'message' => 'Ticket ' . $noTicket . ' has been created successfully!',
                'redirect_url' => $it_initiative? 
                    route('service-management.workspace', ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_IT)]) : (
                        $redVal == 'myservice-desk' ? 
                            route('myservice-desk.workspace', ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_USER)]) :
                        route('service-desk.workspace', ['id' => encrypt($ticket->id), 'role' => encrypt(ServiceTicket::ROLE_USER)])
                    )
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verify($id, Request $request) 
    {
        // 1. Pre-processing Data
        $processedAssets = collect($request->assets)->map(function($asset) {
            if (!$asset) return null;
            $ids = explode('|', decrypt($asset));
            return [
                'it_asset_id' => $ids[0],
                'employee_id' => $ids[1] 
            ];
        })->filter()->toArray();

        $processedRequest = $request->all();
        $processedRequest['assets'] = $processedAssets;
        $processedRequest['risk_register_id'] = $request->risk_register_id ? decrypt($request->risk_register_id) : null;
        
        if($request->filled('report_for_id')) {
            $processedRequest['report_for_id'] = decrypt($request->report_for_id);
        }

        // 2. Validation Logic
        $validator = Validator::make($processedRequest, [
            'category' => 'required',
            'catalog'  => 'required',
            'type'     => 'required',
            'status'   => 'required',
            'impact'   => 'required|integer',
            'urgency'  => 'required|integer',
            'scope'    => 'required|integer',
            'risk_register_id' => 'nullable|exists:risk_registers,id',
            'assets.*.it_asset_id' => 'required|exists:it_assets,id',
            'assets.*.employee_id' => 'required|exists:employees,id',
        ]);

        $ticket = ServiceTicket::findOrFail(decrypt($id));

        $validator->sometimes('report_for_id', 'exists:employees,id', function() use ($ticket) {
            return $ticket->type == ServiceTicket::TYPE_IT_INITIATIVE;
        });

        $validatedData = $validator->validate();

        try {
            DB::beginTransaction();

            $currentUser = Auth::user();
            $employee = $currentUser->employee;

            // 3. Update Histories (Atomic operation)
            $ticket->latestHistory()->whereNull('resolved_at')->update(['resolved_at' => now()]);

            if ($ticket->current_status == ServiceStatusHistory::STATUS_CLOSED || $ticket->current_status == ServiceStatusHistory::STATUS_CANCELLED) {
                throw new \Exception("Cannot verify a ticket that is already closed or cancelled.");
            }

            if($validatedData['risk_register_id']) {
                $riskRegister = RiskRegister::find($validatedData['risk_register_id']);
            }

            // 4. Calculate Priority Score
            $riskScore = isset($riskRegister) ? $riskRegister->score : 0; // Sesuaikan jika ini ID atau Nilai
            $itsmScore = ($validatedData['impact'] * $validatedData['urgency']) + $validatedData['scope'] + $riskScore;
            
            $itsmPriority = ItsmPriority::where('min_score', '<=', $itsmScore)
                ->where('max_score', '>=', $itsmScore)
                ->first();

            // 5. Build Update Payload
            $ticketUpdateData = [
                'category'         => $validatedData['category'],
                'catalog'          => $validatedData['catalog'],
                'impact'           => $validatedData['impact'],
                'urgency'          => $validatedData['urgency'],
                'scope'            => $validatedData['scope'],
                'current_status'   => $validatedData['status'],
                'type'             => $validatedData['type'],
                'risk_register_id' => $validatedData['risk_register_id'] ?? null,
                'risk_register_score' => $riskScore,
                'itsm_priority_id' => $itsmPriority?->id,
            ];

            // 6. Sync Relationships (CCs & Assets)
            if ($request->has('ccs')) {
                $ccData = collect($request->ccs)->mapWithKeys(fn($cc) => [decrypt($cc) => ['service_ticket_id' => $ticket->id]]);
                $result = $ticket->employeeCcs()->sync($ccData);
                $ticket->employeeCcs->load('user')->each(function($cc) use ($ticket) {
                    $cc->user->notify(new TicketNotification($ticket, "You have been added as a CC for this ticket.", ServiceTicket::ROLE_CC));
                });
                // $newCcs = User::whereIn('employee_id', $result['attached'])->get();
                // foreach ($newCcs as $ccEmployee) {
                //     $ccEmployee->notify(new TicketNotification($ticket, "Anda ditambahkan sebagai CC pada tiket ini.", ServiceTicket::ROLE_CC));
                // }
            }

            if ($ticket->report_for_id) {
                $reportFor = User::firstWhere('employee_id', $ticket->report_for_id);
                if ($reportFor) {
                    $reportFor->notify(new TicketNotification($ticket, "A new ticket has been reported to you.", ServiceTicket::ROLE_USER));
                }
            }

            $ticket->submitter->user->notify(new TicketNotification($ticket, "Your ticket has been verified by the IT team. Status: {$validatedData['status']}", ServiceTicket::ROLE_USER));

            $ticket->itAssets()->sync(
                collect($processedAssets)->mapWithKeys(fn($as) => [$as['it_asset_id'] => [
                    'employee_id' => $as['employee_id'],
                    'service_ticket_id' => $ticket->id
                ]])
            );

            if ($ticket->current_status == ServiceStatusHistory::STATUS_OPEN) {
                $ticket->messages()->create([
                    'role'      => ServiceTicket::ROLE_SYSTEM,
                    'sender_id' => $employee->id,
                    'message'   => "Ticket is " . ($validatedData['status'] == ServiceStatusHistory::STATUS_PROCESS ? 'being processed' : 'on hold'),
                ]);
            } else if ($ticket->current_status !== ServiceStatusHistory::STATUS_OPEN) {
                $ticket->messages()->create([
                    'role'      => ServiceTicket::ROLE_SYSTEM,
                    'sender_id' => $employee->id,
                    'message'   => "Ticket is reanalyzed ",
                ]);
            }

            // 7. Finalize Ticket & Logs
            $ticket->update($ticketUpdateData);

            $ticket->histories()->create([
                'from_status' => $ticket->getOriginal('current_status'),
                'to_status'   => $validatedData['status'],
                'note'        => "Ticket verified and moved to status: {$validatedData['status']}",
                'employee_id' => $employee->id,
                'started_at'  => now(),
            ]);

            Log::create([
                'user_id'     => $currentUser->id,
                'ip_address'  => $request->ip(),
                'action'      => 'update',
                'description' => "Ticket {$ticket->no_ticket} verified. Status: {$ticket->current_status}"
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Ticket verified successfully', 'data' => $ticket]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Verification failed: ' . $e->getMessage()], 500);
        }
    }

    public function requestApproval(Request $request, $id) {
        $processedRequest = [];
        $processedRequest['supervisor_id'] = decrypt($request->supervisor);
        $processedRequest['dept_head_id'] = decrypt($request->dept_head);
        $processedRequest['it_note'] = $request->it_note;

        $validator = Validator::make($processedRequest, [
            'supervisor_id' => 'required|exists:employees,id',
            'dept_head_id' => 'required|exists:employees,id',
            'it_note' => 'required|string|max:255',
        ], [
            'supervisor_id.required' => 'Please select a supervisor for approval.',
            'supervisor_id.exists' => 'Selected supervisor does not exist.',
            'dept_head_id.required' => 'Please select a department head for approval.',
            'dept_head_id.exists' => 'Selected department head does not exist.',
            'it_note.required' => 'Please add a note for the approver.',
            'it_note.string' => 'Note must be a string.',
            'it_note.max' => 'Note cannot exceed 255 characters.',
        ]);

        $validated = $validator->validate();

        $ticket = ServiceTicket::findOrFail(decrypt($id));

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $ticket->update([
                'supervisor_id' => $validated['supervisor_id'],
                'dept_head_id' => $validated['dept_head_id'],
                'supervisor_approval' => ServiceTicket::APPROVAL_STATUS_PENDING,
                'supervisor_position' => Employee::find($validated['supervisor_id'])->position->nama,
                'supervisor_department' => Employee::find($validated['supervisor_id'])->department->name,
                'supervisor_area' => Employee::find($validated['supervisor_id'])->area->name,
                'dept_head_approval' => ServiceTicket::APPROVAL_STATUS_PENDING,
                'dept_head_position' => Employee::find($validated['dept_head_id'])->position->nama,
                'dept_head_area' => Employee::find($validated['dept_head_id'])->area->name,
                'dept_head_department' => Employee::find($validated['dept_head_id'])->department->name,
                'it_handler_id' => $user->employee_id,
                'it_handler_position' => $user->employee->position->nama,
                'it_handler_department' => $user->employee->department->name,
                'it_handler_area' => $user->employee->area->name,
                'it_note' => $validated['it_note'],
                'submitted_for_approval_at' => now(),
            ]);

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "User " . $user->employee->fullname . " requested requisition for ticket with Ticket Number: {$ticket->no_ticket}"
            ]);

            DB::commit();

            $targetSupervisor = Employee::find($validated['supervisor_id']);
            $targetSupervisor->user->notify(new TicketNotification($ticket, "New approval request.", ServiceTicket::ROLE_SUPERVISOR, $targetSupervisor->id));

            return response()->json([
                'status' => 'success',
                'message' => 'Requisition request has been sent successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to request requisition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id) {
        // dd($request->all(), $id);
        $ticket = ServiceTicket::findOrFail(decrypt($id));

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $employee = $user->employee;
            
            if ($ticket->current_status == ServiceStatusHistory::STATUS_PROCESS) {
                $changeStatus = ServiceStatusHistory::STATUS_HOLD;
                $note = "Ticket put on hold by {$employee->fullname}({$employee->nik}) - Reason: " . ($request->reason ?? 'No reason provided');
            } else {
                $changeStatus = ServiceStatusHistory::STATUS_PROCESS;
                $note = "Ticket resumed from hold by {$employee->fullname}({$employee->nik})";
            }

            // create new history(transition from process or hold to closed)
            $ticket->histories()->create([
                'from_status' => $ticket->current_status,
                'to_status' => $changeStatus,
                'note' => $note,
                'employee_id' => $employee->id,
                'started_at' => now(),
                'resolved_at' => now(),
            ]);

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "User " . $user->employee->fullname . " updated ticket status with Ticket Number: {$ticket->no_ticket} from {$ticket->current_status} to {$changeStatus}"
            ]);

            $ticket->messages()->create([
                'role' => ServiceTicket::ROLE_SYSTEM,
                'sender_id' => $employee->id,
                'message' => "Ticket status changed to {$changeStatus}. " . ($request->reason ? "Reason: {$request->reason}" : ""),
            ]);

            $ticket->update([
                'current_status' => $changeStatus,
                'time_release' => now(),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Ticket status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to close ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function close($id)
    {
        try {
            $ticket = ServiceTicket::findOrFail(decrypt($id));

            // Pastikan tiket belum tertutup
            if ($ticket->status === 'closed') {
                return response()->json(['message' => 'Ticket is already closed.'], 422);
            }

            $employee = Auth::user()->employee;

            $ticket->messages()->create([
                'role' => ServiceTicket::ROLE_SYSTEM,
                'message' => "Ticket resolved by {$employee->fullname}.",
            ]);

            $ticket->histories()->create([
                'from_status' => $ticket->current_status,
                'to_status' => ServiceStatusHistory::STATUS_CLOSED,
                'note' => "Ticket closed",
                'employee_id' => $employee->id,
                'started_at' => now(),
            ]);

            $ticket->update([
                'current_status' => ServiceStatusHistory::STATUS_CLOSED,
                'time_release' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket successfully closed.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to close ticket.'], 500);
        }
    }

    public function cancel(Request $request, $id, $role = null) {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $ticket = ServiceTicket::find(decrypt($id));

            if (!$ticket) {
                return response()->json(['message' => 'Ticket not found.'], 404);
            }

            if (in_array($ticket->current_status, [ServiceStatusHistory::STATUS_PROCESS, ServiceStatusHistory::STATUS_CLOSED, ServiceStatusHistory::STATUS_CANCELLED])) {
                return response()->json(['message' => 'Only open tickets can be cancelled.'], 422);
            }

            $user = Auth::user();

            $ticket->messages()->create([
                'role' => ServiceTicket::ROLE_SYSTEM,
                'sender_id' => $user->employee_id,
                'message' => "Ticket cancelled by {$user->employee->fullname}. Reason: {$request->reason}",
            ]);

            $ticket->histories()->create([
                'from_status' => $ticket->current_status,
                'to_status' => ServiceStatusHistory::STATUS_CANCELLED,
                'note' => $request->reason,
                'employee_id' => $user->employee_id,
                'started_at' => now(),
            ]);

            $ticket->update([
                'current_status' => ServiceStatusHistory::STATUS_CANCELLED,
            ]);

            if(decrypt($role) == ServiceTicket::ROLE_IT) {
                $ticket->submitter->user->notify(new TicketNotification($ticket, "Your ticket has been cancelled by the IT team. Reason: {$request->reason}", ServiceTicket::ROLE_USER));
            }

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "User " . $user->employee->fullname . " cancelled the ticket with Ticket Number: {$ticket->no_ticket}. Reason: {$request->reason}"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket successfully cancelled.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel ticket.',
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    public function reopenTicket(Request $request, $id) {
        $ticket = ServiceTicket::findOrFail(decrypt($id));

        if (!$request->has('note') || empty($request->note)) {
            return response()->json([
                'success' => false,
                'message' => 'Reopening a ticket requires a note explaining the reason for reopening.'
            ], 404);
        }

        if (!Auth::user()->hasRole('Super User')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        }

        if ($ticket->current_status !== ServiceStatusHistory::STATUS_CLOSED) {
            return response()->json([
                'success' => false,
                'message' => 'Only closed tickets can be reopened.'
            ], 400);
        }

        $ticket->update([
            'current_status' => ServiceStatusHistory::STATUS_OPEN,
        ]);

        $ticket->histories()->create([
            'from_status' => ServiceStatusHistory::STATUS_CLOSED,
            'to_status' => ServiceStatusHistory::STATUS_OPEN,
            'note' => 'Ticket reopened',
            'employee_id' => Auth::user()->employee->id,
        ]);

        $ticket->messages()->create([
            'sender_id' => Auth::user()->employee->id,
            'role' => ServiceTicket::ROLE_SYSTEM,
            'message' => 'This ticket has been reopened. Note: ' . ($request->note),
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "User " . auth()->user()->employee->fullname . " reopened ticket with Ticket Number: {$ticket->no_ticket}. Note: {$request->note}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket has been reopened successfully!'
        ]);
    }
}
