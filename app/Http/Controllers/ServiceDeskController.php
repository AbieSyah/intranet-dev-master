<?php

namespace App\Http\Controllers;

use App\Models\AssetDisposal;
use App\Models\Employee;
use App\Models\ITAsset;
use App\Models\ItsmPriority;
use App\Models\KnowledgeBase;
use App\Models\ServiceCatalog;
use App\Models\ServiceChange;
use App\Models\ServiceStatusHistory;
use App\Models\ServiceTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceDeskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user()->load('employee.department', 'employee.position', 'employee.level', 'employee.area');
        $employeeId = $user->employee_id;
        $encryptedApproverId = encrypt($employeeId);

        /*
        |--------------------------------------------------------------------------
        | 1. Approval Tickets (merged query)
        |--------------------------------------------------------------------------
        */
        // $approvalTickets = ServiceTicket::with('submitter.department')
        //     ->whereNotIn('current_status', [ServiceStatusHistory::STATUS_CLOSED, ServiceStatusHistory::STATUS_CANCELLED])
        //     ->where(function ($q) use ($employeeId) {
        //         $q->where(function ($q2) use ($employeeId) {
        //             $q2->where('supervisor_id', $employeeId)
        //             ->where('supervisor_approval', ServiceTicket::APPROVAL_STATUS_PENDING);
        //         })->orWhere(function ($q2) use ($employeeId) {
        //             $q2->where('dept_head_id', $employeeId)
        //             ->where('supervisor_approval', ServiceTicket::APPROVAL_STATUS_APPROVED)
        //             ->where('dept_head_approval', ServiceTicket::APPROVAL_STATUS_PENDING);
        //         });
        //     })
        //     ->get();

        // $approvalTickets->each(function ($ticket) use ($employeeId, $encryptedApproverId) {
        //     if ($ticket->supervisor_id == $employeeId) {
        //         $role = ServiceTicket::ROLE_SUPERVISOR;
        //         $ticket->custom_text = "Approve As Supervisor";
        //     } else {
        //         $role = ServiceTicket::ROLE_DEPT_HEAD;
        //         $ticket->custom_text = "Approve As Department Head";
        //     }

        //     $ticket->redirectUrl = URL::signedRoute('service-ticket.approve-workspace', [
        //         'id' => encrypt($ticket->id),
        //         'role' => encrypt($role),
        //         'approverId' => $encryptedApproverId
        //     ]);
        // });

        // $myTicketApprovalList = $approvalTickets->sortByDesc('created_at');

        /*
        |--------------------------------------------------------------------------
        | 2. Service Changes (unchanged, just optimized)
        |--------------------------------------------------------------------------
        */
        // $serviceChanges = ServiceChange::with(['ticket', 'proposer.department'])
        //     ->where('approver_id', $employeeId)
        //     ->where('status', ServiceChange::STATUS_PROPOSED)
        //     ->get();

        // $serviceChanges->each(function ($item) use ($employeeId) {
        //     $item->custom_text = "Approve Service Change";
        //     $item->redirectUrl = URL::signedRoute('service-change.public.index', [
        //         'id' => encrypt($item->id),
        //         'approverId' => encrypt($employeeId)
        //     ]);
        // });

        // $myChangeApprovalList = $serviceChanges->sortByDesc('created_at');

        $priorityColorMap = ItsmPriority::getColorMap();

        $knowledgeBases = KnowledgeBase::where('status', KnowledgeBase::STATUS_PUBLISHED)
            ->with('author')
            ->canView($user)
            ->latest('published_at')
            ->paginate();

        return view('pages.profile.service-desk.index', compact(
            'user',
            // 'myTicketApprovalList',
            // 'myChangeApprovalList',
            'knowledgeBases',
            'priorityColorMap'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->can('emp.menu') && request()->route()->getName() !== 'service-desk.create') {
            return redirect()->route('service-desk.create');
        }
        $employees = Employee::with('department', 'position')->get();
        // $itAssets = ITAsset::get();
        $user = Auth::user();
        $catalogData = ServiceCatalog::get();

        $categories = [];

        foreach ($catalogData as $catalog) {
            $categories[$catalog->category][] = $catalog->service_catalog;
        }

        // dd($categories);
        
        return view('pages.profile.service-desk.form', [
            'user' => $user,
            'employees' => $employees,
            'myAssets' => $user->employee->assets,
            'categories' => $categories
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
