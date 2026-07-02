<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ITAsset;
use App\Models\ItsmPriority;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Models\PriorityMetric;
use App\Models\RiskRegister;
use App\Models\ServiceCatalog;
use App\Models\ServiceChange;
use App\Models\ServiceStatusHistory;
use App\Models\ServiceTicket;
use App\Models\ServiceTicketMedia;
use App\Models\ServiceTicketMessage;
use App\Models\User;
use App\Notifications\TicketNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ServiceManagementController extends Controller
{
    public $analizeTypes = [
        ServiceTicket::TYPE_INCIDENT,
        ServiceTicket::TYPE_REQUEST,
        ServiceTicket::TYPE_IT_INITIATIVE,
    ];

    public $allTypes = [
        ServiceTicket::TYPE_INCIDENT,
        ServiceTicket::TYPE_REQUEST,
        ServiceTicket::TYPE_IT_INITIATIVE,
        ServiceTicket::TYPE_CHANGE,
    ];

    // public static $priorities = [
    //     ServiceTicket::PRIORITY_LOW => 'Low (Routine)',
    //     ServiceTicket::PRIORITY_MEDIUM => 'Medium (Standard)',
    //     ServiceTicket::PRIORITY_HIGH => 'High (Urgent)',
    //     ServiceTicket::PRIORITY_CRITICAL => 'Critical',
    // ];

    // public static function getPriorities() {
    //     return self::$priorities;
    // }

    public function employeeAsset(Request $request, $id)
    {
        if ($request->ajax()) {
            $employee = Employee::find(decrypt($id));
            $assets = $employee->assets;

            foreach ($assets as $key => $asset) {
                $asset->encrypted_id = encrypt($asset->id);
            }

            return response()->json($assets);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $tickets = ServiceTicket::get();
        $newTicketsCount = $tickets->where('created_at', '>=', now()->startOfDay())->count();
        $inProgressTicketsCount = $tickets->where('current_status', ServiceStatusHistory::STATUS_PROCESS)->count();
        $closedTodayTicketsCount = $tickets->where('current_status', ServiceStatusHistory::STATUS_CLOSED)->where('updated_at', '>=', now()->startOfDay())->count();
        $openTicketsCount = $tickets->where('current_status', ServiceStatusHistory::STATUS_OPEN)->count();
        $priorityColorMap = ItsmPriority::getColorMap();

        return view('pages.administrator.service-management.index', compact('newTicketsCount', 'inProgressTicketsCount', 'closedTodayTicketsCount', 'openTicketsCount', 'priorityColorMap'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createInitiative()
    {
        return view('pages.administrator.service-management.form');
    }

    public function analysis(string $id) {
        $ticket = ServiceTicket::find(decrypt($id))->load('submitter', 'ccs');
        $ticket->self_report = $ticket->report_for == ServiceTicket::REPORT_SELF;

        if ($ticket->current_status !== ServiceStatusHistory::STATUS_OPEN) {
            abort('404');
        }

        $itAssets = ITAsset::get();
        
        $employees = Employee::with('department', 'position')->get();
        $types = $this->analizeTypes;

        $categories = [];
        $catalogData = ServiceCatalog::all();


        foreach ($catalogData as $category) {
            $categories[$category->category][] = $category->service_catalog;
        }

        return view('pages.administrator.service-management.analysis', compact('ticket', 'employees', 'itAssets', 'types', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function workspace(Request $request, string $id, $role, $approverId = null)
    {
        try {
            $ticketId = decrypt($id);
            $role = decrypt($role);
            $approverId = $approverId ? decrypt($approverId) : null;
        } catch (DecryptException $e) {
            // dd(1);
            abort(403, 'Invalid parameter format.');
        }

        $ticket = ServiceTicket::findOrFail($ticketId)->load('messages.media', 'messages.sender', 'itAssets', 'priority', 'employeeCcs');

        if ($role == ServiceTicket::ROLE_USER && !(Auth::user()->employee_id == $ticket->submitter_id || $role == ServiceTicket::ROLE_USER && Auth::user()->employee_id == $ticket->report_for_id)) {
            // dd(2);
            abort(403, 'Anda tidak memiliki hak akses untuk tiket ini.');
        }


        $user = Auth::user();
        $employee = $user ? $user->employee : null;


        if (Auth::check()) {
            $isSuperUser = $user->hasRole('Super User');
            $isSubmitter = $employee && $employee->id === $ticket->submitter_id;
            $isCC = ($role === ServiceTicket::ROLE_CC) && 
                    $ticket->ccs->pluck('employee_id')->contains($employee->id);
            $isSupervisorApprover = ($role === ServiceTicket::ROLE_SUPERVISOR) && $employee && $employee->id === $ticket->supervisor_id;
            $isDeptHeadApprover = ($role === ServiceTicket::ROLE_DEPT_HEAD) && $employee && $employee->id === $ticket->dept_head_id;
            $isReportFor = ($role === ServiceTicket::ROLE_USER) && $ticket->report_for_id && $employee && $employee->id === $ticket->report_for_id;

            // Jika bukan Super User, dan bukan pengirim, dan bukan CC, maka tolak
            if (!$isSuperUser && !$isSubmitter && !$isCC && !$isSupervisorApprover && !$isDeptHeadApprover && !$isReportFor) {
                // dd(3);
                abort(403, 'Anda tidak memiliki hak akses untuk tiket ini.');
            }
        } 
        // Jika tidak login DAN bukan approver
        // else if ($role !== ServiceTicket::ROLE_SUPERVISOR && $role !== ServiceTicket::ROLE_DEPT_HEAD && $role !== ServiceTicket::ROLE_CC) {
        //     dd(4);
        //     abort(403, 'Silakan login terlebih dahulu.');
        // }

        if($role == ServiceTicket::ROLE_CC && !Auth::check()) {
            // dd(5);
            abort(403, 'Silakan login terlebih dahulu untuk mengakses tiket ini.');
        }

        // DATA MAPPING (Optimasi Employee dari Memori)
        $employees = Employee::with('department', 'position')->get()->keyBy('id');

        $relations = ['supervisor_id' => 'supervisor', 'dept_head_id' => 'deptHead', 'submitter_id' => 'submitter', 'report_for_id' => 'reportFor'];
        foreach ($relations as $fk => $rel) {
            if ($ticket->$fk) {
                $ticket->setRelation($rel, $employees->get($ticket->$fk));
            }
        }

        foreach (['ccs', 'pics'] as $rel) {
            if ($ticket->$rel) {
                $ticket->$rel->each(fn($item) => $item->setRelation('employee', $employees->get($item->employee_id)));
            }
        }

        $viewData = [
            'ticket' => $ticket,
            'employees' => $employees,
            'employee' => $employee,
            'types' => $this->analizeTypes,
            // 'priorities' => self::$priorities,
            'role' => $role,
            'user' => $user,
        ];
        
        if($request->has('signature')) {
            $viewData['approverId'] = $approverId;
        }

        if($ticket->serviceChange) {
            $ticket->serviceChange->setRelation('approver', $employees->get($ticket->serviceChange->approver_id));
            $ticket->serviceChange->setRelation('proposer', $employees->get($ticket->serviceChange->proposer_id));
        }   

        if($ticket->current_status !== ServiceStatusHistory::STATUS_CLOSED && $ticket->current_status !== ServiceStatusHistory::STATUS_CANCELLED) {
            $viewData['riskRegisters'] = RiskRegister::get();
        }
        $viewData['assets'] = ITAsset::with('employee')->get();
        // $viewData['priorityColor'] = collect(ItsmPriority::getColorMap());
        $priorityColorMap = ItsmPriority::getColorMap();
        $viewData['ticketPriority'] = collect($priorityColorMap)->filter(function($item, $key) use ($ticket, $priorityColorMap) {
            return $ticket->total_score >= $item['min_score'] && $ticket->total_score <= $item['max_score'];
        })->first() ?? null;

        $priorityMetrics = PriorityMetric::get();

        $viewData['impactMetric'] = $priorityMetrics->where('type', PriorityMetric::TYPE_IMPACT)->sortByDesc('score')->values();
        $viewData['urgencyMetric'] = $priorityMetrics->where('type', PriorityMetric::TYPE_URGENCY)->sortByDesc('score')->values();
        $viewData['scopeMetric'] = $priorityMetrics->where('type', PriorityMetric::TYPE_SCOPE)->sortByDesc('score')->values();

        $viewData['isReopen'] = $ticket->histories->where('to_status', ServiceStatusHistory::STATUS_OPEN)->count() > 1;

        return view('pages.administrator.service-management.show', $viewData);
    }

    public function updateSubject(Request $request, $id) {
        $ticket = ServiceTicket::findOrFail(decrypt($id));

        $request->validate([
            'subject' => 'required|min:5'
        ]);

        $oldSubject = $ticket->subject;
        $ticket->update(['subject' => $request->subject]);

        Log::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "User " . auth()->user()->employee->fullname . " updated the subject of ticket {$ticket->no_ticket} from '{$oldSubject}' to '{$ticket->subject}'"
        ]);

        $ticket->messages()->create([
            'sender_id' => Auth::user()->employee->id,
            'role' => ServiceTicket::ROLE_SYSTEM,
            'message' => "Subject of this ticket has been updated from '{$oldSubject}' to '{$ticket->subject}'",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket subject has been updated successfully!'
        ]);
    }

    // Take chat histories
    public function getMessages($id) {
        $ticket = ServiceTicket::with(['messages.sender', 'messages.media'])->findOrFail(decrypt($id));
        return response()->json([
            'messages' => $ticket->messages,
            'current_user_id' => auth()->id()
        ]);
    }

    public function saveMessage(Request $request, $ticketId, $role, $messageId = null)
    {
        // dd($request->all(), $ticketId, decrypt($role), $messageId? decrypt($messageId): null);
        try {
            $role = decrypt($request->role);
        } catch (DecryptException $e) {
            abort(403);
        }

        if (
            $role !== ServiceTicket::ROLE_IT &&
            $role !== ServiceTicket::ROLE_USER &&
            $role !== ServiceTicket::ROLE_CC &&
            $role !== ServiceTicketMessage::ROLE_SERVICE_CHANGE
        ) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|min:5',
        ]);

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | EDIT MODE
            |--------------------------------------------------------------------------
            */
            if ($messageId) {
                $message = ServiceTicketMessage::findOrFail(decrypt($messageId));

                if ($message->sender_id !== Auth::user()->employee_id) {
                    abort(403);
                }

                $ticket = $message->ticket;
                $oldMessage = $message->message;

                $message->update([
                    'message' => $request->message,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE MODE
            |--------------------------------------------------------------------------
            */
            else {
                $ticket = ServiceTicket::findOrFail(decrypt($ticketId));

                $message = $ticket->messages()->create([
                    'sender_id' => Auth::user()->employee->id,
                    'role' => $role,
                    'message' => $request->message,
                    'is_internal' => $request->boolean('is_internal'),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ATTACHMENTS
            |--------------------------------------------------------------------------
            */
            $media = [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    if (!$attachment->isValid()) {
                        continue;
                    }

                    $path = $attachment->store('tickets', 'public');

                    $media[] = [
                        'path' => $path,
                        'name' => $attachment->getClientOriginalName(),
                        'extension' => $attachment->getClientOriginalExtension(),
                    ];
                }
            }

            if (!empty($media)) {
                $message->media()->createMany($media);
            }

            /*
            |--------------------------------------------------------------------------
            | SERVICE CHANGE UPDATE
            |--------------------------------------------------------------------------
            */
            if ($role == ServiceTicketMessage::ROLE_SERVICE_CHANGE && $request->actual_execution) {
                [$start, $end] = explode(' to ', $request->actual_execution);

                $ticket->serviceChange()->update([
                    'status' => ServiceChange::STATUS_DONE,
                    'actual_start' => Carbon::parse($start),
                    'actual_end' => Carbon::parse($end),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */
            if (!$messageId && $role == ServiceTicket::ROLE_IT) {
                $ticket->submitter->user->notify(
                    new TicketNotification(
                        $ticket,
                        "Ticket with subject '{$ticket->subject}' has received a response from IT. Please check your workspace for details.",
                        ServiceTicket::ROLE_USER,
                        $ticket->submitter_id
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | LOG
            |--------------------------------------------------------------------------
            */
            if ($messageId) {
                Log::create([
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'action' => 'update',
                    'description' => "User " . auth()->user()->employee->fullname .
                        " updated a message in ticket {$message->ticket->no_ticket} " .
                        "from '{$oldMessage}' to '{$message->message}'"
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to send message.');
        }
    }

    public function deleteMessageMedia(Request $request, $id) {
        $media = ServiceTicketMedia::findOrFail(decrypt($id));
        $message = $media->message;

        if ($message->sender_id !== Auth::user()->employee_id) {
            abort(403);
        }

        try {
            DB::beginTransaction();
            Storage::disk('public')->delete($media->path);
            $media->delete();

            Log::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => "User " . auth()->user()->employee->fullname . " deleted an attachment from a message in ticket {$message->ticket->no_ticket}"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Attachment has been deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attachment.'
            ], 500);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    // for request approval
    public function approve($id, Request $request) {
        try {
            $ticketId = decrypt($id);
            $approverId = decrypt($request->approver);
            $role = decrypt($request->role);
            $ticket = ServiceTicket::findOrFail($ticketId)->load('supervisor', 'deptHead');
        } catch (DecryptException $e) {
            abort(403);
        }

        if (!$approverId || !in_array($role, [ServiceTicket::ROLE_SUPERVISOR, ServiceTicket::ROLE_DEPT_HEAD])) {
            abort(403);
        }
        $employee = Employee::find($approverId);
        if (!$employee) {
            abort(403);
        }

        if($role == ServiceTicket::ROLE_DEPT_HEAD && $ticket->supervisor_approval == ServiceTicket::APPROVAL_STATUS_PENDING) {
            abort(403, 'Ticket must be approved by Supervisor before Department Head can approve.');
        }

        // }

        try {
            if ($role == ServiceTicket::ROLE_SUPERVISOR && $ticket->supervisor_id == $employee->id) {
                $ticket->update([
                    'supervisor_approval' => ServiceTicket::APPROVAL_STATUS_APPROVED,
                    'supervisor_approval_at' => now(),
                    'supervisor_note' => $request->input('note') // Simpan catatan supervisor jika ada
                ]);

                $ticket->messages()->create([
                    'sender_id' => $employee->id,
                    'role' => ServiceTicket::ROLE_SYSTEM,
                    'message' => 'Direct Supervisor approved this ticket.',
                    // 'is_internal' => false,
                ]);

                Log::create([
                    'user_id' => $employee->user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'approved',
                    'description' => "User " . $employee->fullname . " approved ticket with Ticket Number: {$ticket->no_ticket} as Supervisor(Via Public Link)."
                ]);

                User::firstWhere('employee_id', $ticket->dept_head_id)->notify(new TicketNotification($ticket, "Ticket with subject '{$ticket->subject}' has been approved by the Supervisor. Awaiting your approval as Department Head.", ServiceTicket::ROLE_DEPT_HEAD, $ticket->dept_head_id));
            } else if ($role == ServiceTicket::ROLE_DEPT_HEAD && $ticket->dept_head_id == $employee->id) {
                $ticket->update([
                    'dept_head_approval' => ServiceTicket::APPROVAL_STATUS_APPROVED,
                    'dept_head_approval_at' => now(),
                    'dept_head_note' => $request->input('note') // Simpan catatan department head jika ada
                ]);
                $ticket->messages()->create([
                    'sender_id' => $employee->id,
                    'role' => ServiceTicket::ROLE_SYSTEM,
                    'message' => 'Department Head approved this ticket.',
                    // 'is_internal' => false,
                ]);
                Log::create([
                    'user_id' => $employee->user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'approved',
                    'description' => "User " . $employee->fullname . " approved ticket with Ticket Number: {$ticket->no_ticket} as Department Head(Via Public Link)."
                ]);
            } else {
                abort(403);
            }
        } catch (Exception $e) {
            abort(403);
        }

        return redirect()->back();
    }

    public function addAsset(Request $request, $id) {
        if (!$request->asset_id) {
            return response()->json([
                'success' => false,
                'message' => 'Asset ID is required.'
            ], 400);
        }

        $ticket = ServiceTicket::findOrFail(decrypt($id));

        $asset = ITAsset::findOrFail(decrypt($request->asset_id));

        if ($ticket->itAssets()->where('it_asset_id', $asset->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Asset already added to this ticket.'
            ], 400);
        }

        $ticket->itAssets()->attach($asset->id, [
            'employee_id' => $asset->employee_id
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "User " . auth()->user()->employee->fullname . " added asset {$asset->asset_code} to ticket with Ticket Number: {$ticket->no_ticket}."
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset has been added to the ticket successfully!'
        ]);
    }

    public function removeAsset(Request $request, $id) {
        if (!$request->asset_id) {
            return response()->json([
                'success' => false,
                'message' => 'Asset ID is required.'
            ], 400);
        }

        $ticket = ServiceTicket::findOrFail(decrypt($id));

        $asset = ITAsset::findOrFail(decrypt($request->asset_id));

        if (!$ticket->itAssets()->where('it_asset_id', $asset->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found in this ticket.'
            ], 400);
        }

        $ticket->itAssets()->detach($asset->id);

        Log::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "User " . auth()->user()->employee->fullname . " removed asset {$asset->asset_code} from ticket with Ticket Number: {$ticket->no_ticket}."
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset has been removed from the ticket successfully!'
        ]);
    }

    public function resendNotification(Request $request, $id, $role) {
        try {
            $ticketId = decrypt($id);
            $ticket = ServiceTicket::findOrFail($ticketId)->load('submitter', 'supervisor', 'deptHead');
        } catch (DecryptException $e) {
            abort(403);
        }

        if ($role == ServiceTicket::ROLE_SUPERVISOR) {
            $recipient = $ticket->supervisor;
            $message = "Reminder: Ticket with subject '{$ticket->subject}' is awaiting your approval as Supervisor.";
        } else if ($role == ServiceTicket::ROLE_DEPT_HEAD) {
            if ($ticket->supervisor_approval !== ServiceTicket::APPROVAL_STATUS_APPROVED) {
                abort(403, 'Ticket must be approved by Supervisor before sending reminder to Department Head.');
            }
            $recipient = $ticket->deptHead;
            $message = "Reminder: Ticket with subject '{$ticket->subject}' is awaiting your approval as Department Head.";
        } else {
            abort(403);
        }

        if (!$recipient || !$recipient->user) {
            abort(403);
        }

        try {
            $recipient->user->notify(new TicketNotification($ticket, $message, $role, $recipient->id));
            return response()->json(['message' => 'Reminder notification sent successfully!']);
            // return redirect()->back()->with('success', 'Reminder notification sent successfully!');
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to send reminder notification.'], 500);
        }
    }

    public function requestApprovalReport(Request $request, $id) {
        // dd($request->all(), $id);

        try {
            $ticketId = decrypt($id);
            $ticket = ServiceTicket::findOrFail($ticketId)->load('submitter', 'supervisor', 'deptHead', 'serviceChange', 'messages.sender');
        } catch (DecryptException $e) {
            abort(403);
        }

        // if (!$ticket->serviceChange) {
        //     abort(404, 'Service Change data not found for this ticket.');
        // }

        $reportData = [
            'ticket' => $ticket,
            'serviceChange' => $ticket->serviceChange,
        ];

        $submitter = $ticket->submitter;
        $submitter->qrcode = base64_encode(QrCode::format('svg')->size(50)->generate(
            // URL::signedRoute('service-desk.request-approval.report', ['id' => encrypt($submitter->id)])
            "signature - {$submitter->fullname}. Signed at {$ticket->created_at->format('Y-m-d H:i:s')}"
        ));
        $submitter->qrcode = base64_encode(QrCode::format('svg')->size(50)->generate(
            // URL::signedRoute('service-desk.request-approval.report', ['id' => encrypt($submitter->id)])
            "signature - {$submitter->fullname}. Signed at {$ticket->created_at->format('Y-m-d H:i:s')}"
        ));

        $supervisor = $ticket->supervisor;
        if($ticket->supervisor_approval == ServiceTicket::APPROVAL_STATUS_APPROVED) {
            $supervisor->qrcode = base64_encode(QrCode::format('svg')->size(50)->generate(
                // URL::signedRoute('service-desk.request-approval.report', ['id' => encrypt($supervisor->id)])
                "signature - {$supervisor->fullname}. Signed at {$ticket->supervisor_approval_at->format('Y-m-d H:i:s')}"
            ));
        }

        $deptHead = $ticket->deptHead;
        if($ticket->dept_head_approval == ServiceTicket::APPROVAL_STATUS_APPROVED) {
            $deptHead->qrcode = base64_encode(QrCode::format('svg')->size(50)->generate(
                // URL::signedRoute('service-desk.request-approval.report', ['id' => encrypt($deptHead->id)])
                "signature - {$deptHead->fullname}. Signed at {$ticket->dept_head_approval_at->format('Y-m-d H:i:s')}"
            ));
        }

        $itHandler = $ticket->itHandler;
        if ($itHandler) {
            $itHandler->qrcode = base64_encode(QrCode::format('svg')->size(50)->generate(
                // URL::signedRoute('service-desk.request-approval.report', ['id' => encrypt($itHandler->id)])
                "signature - {$itHandler->fullname}. Signed at {$ticket->submitted_for_approval_at->format('Y-m-d H:i:s')}"
            ));
        }

        $reportData['submitter'] = $submitter;
        $reportData['supervisor'] = $supervisor;
        $reportData['deptHead'] = $deptHead;
        $reportData['itHandler'] = $itHandler;
        // $reportData['messages'] = $ticket->messages()->whereIn('role', [ServiceTicketMessage::ROLE_USER, ServiceTicketMessage::ROLE_IT])->get()->load('sender');
        $reportData['message'] = $ticket->messages()->whereIn('role', [ServiceTicketMessage::ROLE_USER, ServiceTicketMessage::ROLE_IT])->first()->load('sender');
        
        return Pdf::loadView('pages.administrator.service-management.public.report', $reportData)
            ->setPaper('xa4', 'portrait')
            ->stream("Service_Change_Request_Report_{$ticket->no_ticket}.pdf");
    }

    function closedTickets() {
        $tickets = ServiceTicket::where('current_status', ServiceStatusHistory::STATUS_CLOSED)->get();
        $priorityColorMap = ItsmPriority::getColorMap();
        return view('pages.administrator.service-management.closed', compact('tickets', 'priorityColorMap'));
    }

    function getPublicLink(Request $request, $ticketId, $role) {
        $ticketId = decrypt($ticketId);
        $role = decrypt($role);

        if (!in_array($role, [ServiceTicket::ROLE_SUPERVISOR, ServiceTicket::ROLE_DEPT_HEAD])) {
            abort(403);
        }

        $ticket = ServiceTicket::findOrFail($ticketId);
        if($ticket->current_status == ServiceStatusHistory::STATUS_CLOSED) {
            abort(403, 'Cannot generate link for closed ticket.');
        } else if ($role == ServiceTicket::ROLE_SUPERVISOR && $ticket->supervisor_approval == ServiceTicket::APPROVAL_STATUS_APPROVED) {
            abort(403, 'Ticket has already been approved by Supervisor.');
        } else if ($role == ServiceTicket::ROLE_DEPT_HEAD && $ticket->dept_head_approval == ServiceTicket::APPROVAL_STATUS_APPROVED && $ticket->supervisor_approval == ServiceTicket::APPROVAL_STATUS_APPROVED) {
            abort(403, 'Ticket has already been approved by Department Head.');
        }

        Log::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "User " . auth()->user()->employee->fullname . " generated a public approval link for ticket with Ticket Number: {$ticket->no_ticket} for " . ($role == ServiceTicket::ROLE_SUPERVISOR ? 'Supervisor' : 'Department Head') . "."
        ]);

        if ($role == ServiceTicket::ROLE_SUPERVISOR) {
            return response()->json([
                'link' => URL::signedRoute('service-ticket.approve-workspace', [
                    'id' => encrypt($ticket->id),
                    'role' => encrypt(ServiceTicket::ROLE_SUPERVISOR),
                    'approverId' => encrypt($ticket->supervisor_id)
                ])
            ]);
        } else if ($role == ServiceTicket::ROLE_DEPT_HEAD) {
            return response()->json([
                'link' => URL::signedRoute('service-ticket.approve-workspace', [
                    'id' => encrypt($ticket->id),
                    'role' => encrypt(ServiceTicket::ROLE_DEPT_HEAD),
                    'approverId' => encrypt($ticket->dept_head_id)
                ])
            ]);
        }

        return response()->json([
            'link' => null
        ]);
    }
}
