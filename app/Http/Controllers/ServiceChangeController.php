<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Models\PriorityMetric;
use App\Models\ServiceChange;
use App\Models\ServiceTicket;
use App\Models\ServiceTicketMessage;
use App\Models\User;
use App\Notifications\ServiceChangeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use Yajra\DataTables\DataTables;

class ServiceChangeController extends Controller
{
    public function getData(Request $request) {
        $query = ServiceChange::with(['ticket', 'approver']);

        $employeeId = Auth::user()->employee_id;

        return DataTables::of($query)
            ->addColumn('encrypted_id', function($row) {
                return encrypt($row->id);
            })
            ->addColumn('detail_url', function($row) {
                return route('service-change.show', encrypt($row->id));
            })
            ->addColumn('detail_label', function($row) use ($employeeId) {
                if ($row->approver_id == $employeeId) {
                    if ($row->status == ServiceChange::STATUS_PROPOSED) {
                        return "Approve Change";
                    } else {
                        return "Review Change";
                    }
                } else {
                    return "View";
                }
            })
            ->addColumn('created_at_formatted', function($serviceChange) {
                return [
                    'display' => $serviceChange->created_at->diffForHumans(),
                    'timestamp' => $serviceChange->created_at->timestamp
                ];
            })
            ->addColumn('ticket_created_at_formatted', function($serviceChange) {
                return [
                    'display' => $serviceChange->ticket->created_at->diffForHumans(),
                    'timestamp' => $serviceChange->ticket->created_at->timestamp
                ];
            })
            ->make(true);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newServiceChangeCount = ServiceChange::where('status', ServiceChange::STATUS_PROPOSED)->whereDate('created_at', Carbon::today())->count();
        $proposedServiceChangeCount = ServiceChange::where('status', ServiceChange::STATUS_PROPOSED)->count();
        $inProgressServiceChangeCount = ServiceChange::where('status', ServiceChange::STATUS_APPROVED)->orWhere('status', ServiceChange::STATUS_DONE)->count();
        $doneTodayServiceChangeCount = ServiceChange::where('status', ServiceChange::STATUS_DONE)->whereDate('done_at', Carbon::today())->count();
        return view('pages.administrator.service-change.index', compact('newServiceChangeCount', 'proposedServiceChangeCount', 'inProgressServiceChangeCount', 'doneTodayServiceChangeCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());    
        $ticket = ServiceTicket::findOrFail(decrypt($request->ticket_id));

        $proccesedRequest = $request->all();
        $proccesedRequest['approver_id'] = decrypt($request->approver);
        
        $startExecution = Carbon::parse(explode(" to ", $request->execution_plan)[0]);
        $endExecution = Carbon::parse(explode(" to ", $request->execution_plan)[1]);

        $proccesedRequest['planned_start'] = $startExecution->toDateTimeString();
        $proccesedRequest['planned_end'] = $endExecution->toDateTimeString();

        unset($proccesedRequest['execution_plan']);

        // dd($proccesedRequest);

        $validatedData = Validator::make($proccesedRequest, [
            'it_notice' => 'required|string',
            'approver_id' => 'required|exists:employees,id',
            'planned_start' => 'required|date|after_or_equal:today',
            'planned_end' => 'required|date|after:planned_start',
        ])->validate();
    
        $lastChangeNo = ServiceChange::latest()->first();
        $nextIncrement = $lastChangeNo ? (int)substr($lastChangeNo->change_no, -3) + 1 : 1;
        $change_no = 'CM-' . now()->format('ymd') . '-' . str_pad($nextIncrement, 3, '0', STR_PAD_LEFT);
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $validatedData['proposer_id'] = $user->employee->id;
            $validatedData['change_no'] = $change_no;
            $validatedData['change_type'] = $ticket->priority->level;

            $serviceChange = $ticket->serviceChange()->create($validatedData);

            $approver = User::firstWhere('employee_id', [$validatedData['approver_id']]);

            $ticket->messages()->create([
                'employee_id' => $user->employee->id,
                'role' => ServiceTicketMessage::ROLE_SYSTEM,
                'is_internal' => true,
                'message' => "A change management with change number {$serviceChange->change_no} has been proposed by {$user->employee->fullname}. Waiting for {$approver->employee->fullname} approval.",
            ]);
            
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => "User " . $user->employee->fullname . " proposed a change management for ticket Number : {$ticket->no_ticket} with change type {$validatedData['change_type']}. Change number: {$change_no}"
            ]);

            $approver->notify(new ServiceChangeNotification($serviceChange));

            DB::commit();
            return response()->json(['message' => "Service change proposed successfully!. \nChange Number: " . $change_no]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create service change: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $serviceChange = ServiceChange::with([
            'ticket',
            'ticket.priority:id,level',
        ])->findOrFail(decrypt($id));

        /*
        |--------------------------------------------------------------------------
        | COLLECT EMPLOYEE IDS
        |--------------------------------------------------------------------------
        */
        $employeeIds = collect([
            $serviceChange->ticket->submitter_id,
            $serviceChange->proposer_id,
            $serviceChange->approver_id,
        ])
        ->filter()
        ->unique();

        /*
        |--------------------------------------------------------------------------
        | LOAD ALL EMPLOYEES IN SINGLE QUERY
        |--------------------------------------------------------------------------
        */
        $employees = Employee::with([
                'department:id,name',
                'position:id,nama',
            ])
            ->whereIn('id', $employeeIds)
            ->get([
                'id',
                'fullname',
                'nik',
                'department_id',
                'position_id',
            ])
            ->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | MAP RELATIONS MANUALLY
        |--------------------------------------------------------------------------
        */
        $serviceChange->setRelation(
            'proposer',
            $employees->get($serviceChange->proposer_id)
        );

        $serviceChange->setRelation(
            'approver',
            $employees->get($serviceChange->approver_id)
        );

        $serviceChange->ticket->setRelation(
            'submitter',
            $employees->get($serviceChange->ticket->submitter_id)
        );

        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */
        $serviceChangeMessage = ServiceTicketMessage::with([
                'sender:id,fullname,nik',
                'media',
            ])
            ->where([
                'service_ticket_id' => $serviceChange->service_ticket_id,
                'is_internal' => true,
                'role' => ServiceTicketMessage::ROLE_SERVICE_CHANGE,
            ])
            ->latest('id')
            ->first();


        $priorityMetrics = PriorityMetric::get();

        $impactMetric = $priorityMetrics->where('type', PriorityMetric::TYPE_IMPACT)->sortByDesc('score')->values();
        $urgencyMetric = $priorityMetrics->where('type', PriorityMetric::TYPE_URGENCY)->sortByDesc('score')->values();
        $scopeMetric = $priorityMetrics->where('type', PriorityMetric::TYPE_SCOPE)->sortByDesc('score')->values();

        return view('pages.administrator.service-change.show', compact('serviceChange', 'serviceChangeMessage', 'impactMetric', 'urgencyMetric', 'scopeMetric'));   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $serviceChange = ServiceChange::findOrFail(decrypt($id));

        $proccesedRequest = $request->all();
        if($serviceChange->status != ServiceChange::STATUS_APPROVED) {
            $proccesedRequest['approver_id'] = decrypt($request->approver);

            $startExecution = Carbon::parse(explode(" to ", $request->execution_plan)[0]);
            $endExecution = Carbon::parse(explode(" to ", $request->execution_plan)[1]);

            $proccesedRequest['planned_start'] = $startExecution->toDateTimeString();
            $proccesedRequest['planned_end'] = $endExecution->toDateTimeString();

            unset($proccesedRequest['execution_plan']);
        } else {
            $proccesedRequest['actual_start'] = Carbon::parse(explode(" to ", $request->actual_execution)[0])->toDateTimeString();
            $proccesedRequest['actual_end'] = Carbon::parse(explode(" to ", $request->actual_execution)[1])->toDateTimeString();
            unset($proccesedRequest['actual_execution']);
        }

        $validationRules = [];

        if ($serviceChange->status == ServiceChange::STATUS_APPROVED) {
            $validationRules = [
                'actual_start' => 'required|date',
                'actual_end' => 'required|date|after_or_equal:actual_start',
            ];
        } else {
            $validationRules = [
                'it_notice' => 'required|string',
                'approver_id' => 'required|exists:employees,id',
                'planned_start' => 'required|date|after_or_equal:today',
                'planned_end' => 'required|date|after_or_equal:planned_start',
            ];  
        }

        $validation = Validator::make($proccesedRequest, $validationRules);

        $validatedData = $validation->validate();

        if ($serviceChange->status !== ServiceChange::STATUS_APPROVED) {
            $serviceChange->it_notice = $validatedData['it_notice'];
            $serviceChange->approver_id = $validatedData['approver_id'];
            $serviceChange->planned_start = $validatedData['planned_start'];
            $serviceChange->planned_end = $validatedData['planned_end'];
        }
        else {
            $serviceChange->actual_start = $validatedData['actual_start'];
            $serviceChange->actual_end = $validatedData['actual_end'];
            $serviceChange->status = ServiceChange::STATUS_DONE;
            $serviceChange->done_at = now();
        }

        if ($serviceChange->isDirty()) {
            try {
                if($serviceChange->isDirty('approver_id')) {
                    $oldApprover = User::firstWhere('employee_id', $serviceChange->getOriginal('approver_id'));
                    $approver = User::firstWhere('employee_id', [$validatedData['approver_id']]);   

                    $serviceChange->ticket->messages()->create([
                        'employee_id' => $user->employee->id,
                        'role' => ServiceTicketMessage::ROLE_SYSTEM,
                        'is_internal' => true,
                        'message' => "The approver for change management with change number {$serviceChange->change_no} has been changed to {$approver->employee->fullname}. Waiting for approval.",
                    ]);
                    $approver->notify(new ServiceChangeNotification($serviceChange));
                    $oldApprover->notify(new ServiceChangeNotification($serviceChange, ServiceChangeNotification::FOR_OLD_APPROVER));
                }

                $serviceChange->ticket->messages()->create([
                    'employee_id' => $user->employee->id,
                    'role' => ServiceTicketMessage::ROLE_SYSTEM,
                    'is_internal' => true,
                    'message' => "Service change with change number {$serviceChange->change_no} has been updated. Updated fields: " . implode(', ', array_keys($serviceChange->getDirty())),
                ]);

                // log activity
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'update',
                    'description' => "User " . $user->employee->fullname . " updated change management. Updated fields: " . implode(', ', array_keys($serviceChange->getDirty()))
                ]);

                $serviceChange->save();

                return response()->json(['message' => 'Service change updated successfully!']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to update service change: ' . $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'No changes detected. Service change not updated.']);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function changeManagement(Request $request, $id, $approverId) {
        $serviceChange = ServiceChange::findOrFail(decrypt($id));
        $approver = Employee::findOrFail(decrypt($approverId));

        if ($serviceChange->approver_id !== $approver->id) {
            abort(404);
        }

        return view('pages.administrator.service-change.public.index', [
            'serviceChange' => $serviceChange,
        ]);
    }

    public function approve(Request $request, $id) {
        // dd($request->all(), decrypt($id));

        try {
            $serviceChange = ServiceChange::findOrFail(decrypt($id));
            $serviceChange->update([
                'status' => ServiceChange::STATUS_APPROVED,
                'approved_by' => $serviceChange->approver->id,
                'approved_at' => now(),
            ]);

            $serviceChange->ticket->messages()->create([
                'employee_id' => $serviceChange->approver->id,
                'role' => ServiceTicketMessage::ROLE_SYSTEM,
                'is_internal' => true,
                'message' => "Change management with change number {$serviceChange->change_no} has been approved by {$serviceChange->approver->fullname}.",
            ]);

            // log activity
            Log::create([
                'user_id' => $serviceChange->approver->user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "User " . $serviceChange->approver->fullname . " approved change management with change number {$serviceChange->change_no}"
            ]);

            return redirect()->back()->with('success', 'Service change approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve service change: ' . $e->getMessage());
        }
    }
}
