<?php

namespace App\Http\Controllers\HRD;

use App\Exports\EvaluationProcessExport;
use App\Exports\EvaluationDoneExport;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\EvaluationAttachment;
use App\Models\EvaluationHistory;
use App\Models\Log;
use App\Models\Master\Appraisal;
use App\Models\Master\Building;
use App\Models\Master\LineApprovalEmployee;
use App\Models\Position;
use App\Models\Section;
use App\Models\User;
use App\Notifications\EvaluationNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Evaluation::with(['employee', 'appraisal.department'])
                ->where('evaluations.status', '!=', 'DONE');

            $formStatus = $request->get('form_status', 'ALL');
            if ($formStatus !== 'ALL') {
                $query->where('evaluations.status', $formStatus);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->editColumn('id', fn($data) => encrypt($data->id))
                ->editColumn('release_id', fn($data) => $data->release_id ?? '-')
                ->addColumn('nik', fn($data) => $data->employee->nik ?? '-')
                ->addColumn('name', fn($data) => $data->employee->fullname ?? '-')
                ->addColumn('department', fn($data) => $data->employee->department->name ?? '-')
                ->addColumn('section', fn($data) => $data->employee->section->nama ?? '-')
                ->addColumn('position', fn($data) => $data->employee->position->nama ?? '-')
                ->addColumn('building', fn($data) => $data->employee->building->nama ?? '-')
                ->addColumn('start', function ($data) {
                    if (!$data->eval_start) return '-';
                    return [
                        'display' => $data->eval_start->format('d M Y'),
                        'timestamp' => $data->eval_start->getTimestamp()
                    ];
                })
                ->addColumn('end', function ($data) {
                    if (!$data->eval_end) return '-';
                    return [
                        'display' => $data->eval_end->format('d M Y'),
                        'timestamp' => $data->eval_end->getTimestamp()
                    ];
                })
                ->editColumn('purpose', fn($data) => $data->purpose)
                ->editColumn('total_score', fn($data) => $data->total_score ?? '-')
                ->editColumn('grade', fn($data) => $data->grade ?? '-')
                ->addColumn('decision', fn($data) => $data->decision_employment ?? '-')
                ->addColumn('status', function ($data) {
                    $badges = [
                        'RELEASE' => 'success',
                        'DRAFT' => 'secondary',
                        'REVISE' => 'danger',
                        'REJECT' => 'dark',
                        '1st Evaluator' => 'success',
                        '2nd Evaluator' => 'success',
                        '3rd Evaluator' => 'success',
                        'HRD Approved' => 'success',
                        'Prodir' => 'success',
                        'Presdir' => 'success',
                        'DONE' => 'success',
                    ];
                    $status = $data->status;
                    $displayText = ($status === 'RELEASE') ? 'HRD' : $status;
                    return isset($badges[$status]) 
                        ? "<span class=\"badge text-bg-{$badges[$status]}\">{$displayText}</span>" 
                        : '-';
                })
                ->addColumn('action', function ($data) {
                    $btn = '';
                    if (Auth::user()->can('hrd.evaluation.read') && ($data->status === 'DRAFT' || $data->status === 'REVISE')) {
                        $btn .= '<a href="' . route('evaluation.form', encrypt($data->id)) . '" title="Edit" class="btn btn-warning btn-sm"><i class="ri-quill-pen-line"></i></a>';
                    }
                    if (Auth::user()->can('hrd.evaluation.delete')) {
                        $btn .= '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></a>';
                    }
                    if (Auth::user()->can('hrd.evaluation.read')) {
                        $btn .= '&nbsp;<a href="' . route('evaluation.detail', ['id' => encrypt($data->id)]) . '" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                        $excluded_statuses = ['DRAFT', 'REVISE', 'REJECT'];
                        if (!in_array($data->status, $excluded_statuses)) {
                            $btn .= '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></a>';
                        }
                    }
                    if (!empty($data->decision_reason)) {
                        $btn .= "&nbsp;<a href=\"#\" data-id=\"" . encrypt($data->id) . "\" title=\"Decision Reason\" class=\"btn btn-secondary btn-sm me-1 btn-reason\"><i class=\"ri-information-line\"></i></a>";
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.evaluation.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function form(string $id = null)
    {
        if ($id) {
            $id = decrypt($id);
            $evaluation = Evaluation::with('attachments')->find($id);
            if ($evaluation && $evaluation->status === 'RELEASE' || $evaluation && $evaluation->status === 'REJECT' || $evaluation && $evaluation->status === 'DONE') {
                return redirect()->route('evaluation.index')
                    ->with('swal_warning', 'Cannot Edit Evaluation');
            }
        } else {
            $evaluation = null;
        }
        $employee = LineApprovalEmployee::with('employee')
            ->whereHas('lineApproval', function ($query) {
                $query->where('approval_type', 'Evaluation');
            })
            ->get();
        return view('pages.hrd.evaluation.form', compact('evaluation', 'employee'));
    }

    public function detail($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }
        $evaluation = Evaluation::with([
            'evaluationHistories', 
            'attachments',
            'approval1', 'approval2', 'approval3', 'approval4', 'approval5', 'approval6'
        ])->findOrFail($id);
        return view('pages.hrd.evaluation.detail', compact('evaluation'));
    }

    public function getAppraisals($employee_id)
    {
        $employee = Employee::find($employee_id);
        if (!$employee) {
            return response()->json([]);
        }
        $appraisals = Appraisal::with(['position', 'department', 'section'])
            ->where('position_id', $employee->position_id)
            ->whereRaw('LOWER(status) = ?', [strtolower($employee->status)])
            ->get();
        return response()->json($appraisals);
    }

    public function getEvaluators($employeeId)
    {
        $lineApprovalEmployee = LineApprovalEmployee::with([
            'lineApproval.approve1.position',
            'lineApproval.approve1.user',
            'lineApproval.approve2.position',
            'lineApproval.approve2.user',
            'lineApproval.approve3.position',
            'lineApproval.approve3.user',
            'lineApproval.approve4.position',
            'lineApproval.approve4.user',
            'lineApproval.approve5.position',
            'lineApproval.approve5.user',
            'lineApproval.approve6.position',
            'lineApproval.approve6.user',
            'lineApproval.draft.position',
            'lineApproval.draft.user'
        ])
            ->where('employee_id', $employeeId)
            ->whereHas('lineApproval', function ($query) {
                $query->where('approval_type', 'Evaluation');
            })->first();
        if (!$lineApprovalEmployee || !$lineApprovalEmployee->lineApproval) {
            return response()->json([
                'approval1' => null,
                'approval2' => null,
                'approval3' => null,
                'approval4' => null,
                'approval5' => null,
                'approval6' => null,
                'drafter'   => null,
            ]);
        }
        $lineApproval = $lineApprovalEmployee->lineApproval;
        $responseData = [
            'drafter'   => $lineApproval->draft ?? null,
            'approval1' => $lineApproval->approve1 ?? null,
            'approval2' => $lineApproval->approve2 ?? null,
            'approval3' => $lineApproval->approve3 ?? null,
            'approval4' => $lineApproval->approve4 ?? null,
            'approval5' => $lineApproval->approve5 ?? null,
            'approval6' => $lineApproval->approve6 ?? null,
        ];
        for ($i = 1; $i <= 6; $i++) {
            $key = 'approval' . $i;
            if (isset($responseData[$key]) && $responseData[$key] !== null) {
                $positionName = $responseData[$key]->position->nama ?? null;
                $responseData[$key]->default_role = Evaluation::getDefaultApprovals($positionName);
            }
        }
        return response()->json($responseData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $evaluationData = $request->except(['_token', 'status', 'id', 'new_attachments', 'new_attachment_names', 'existing_attachment_names', 'deleted_attachments']);
            $evaluationData['status'] = $request->input('status');

            if (isset($evaluationData['eval_start'])) {
                $evaluationData['eval_start'] = Carbon::createFromFormat('d/m/Y', $evaluationData['eval_start'])->toDateString();
            }
            if (isset($evaluationData['eval_end'])) {
                $evaluationData['eval_end'] = Carbon::createFromFormat('d/m/Y', $evaluationData['eval_end'])->toDateString();
            }

            // START CHECK DUPLICATE
            $evaluationId = $request->input('id');
            $duplicateCheck = Evaluation::where('employee_id', $evaluationData['employee_id'] ?? null)
                ->where('eval_start', $evaluationData['eval_start'] ?? null)
                ->where('eval_end', $evaluationData['eval_end'] ?? null)
                ->where('purpose', $evaluationData['purpose'] ?? null);
            if (!empty($evaluationId)) {
                $duplicateCheck->where('id', '!=', $evaluationId);
            }
            if ($duplicateCheck->exists()) {
                DB::rollback();
                return response()->json([
                    'message' => "Evaluation for this employee already exists.",
                    'redirect' => route('evaluation.form')
                ], 422);
            }
            // END CHECK DUPLICATE

            $evaluation = Evaluation::updateOrCreate(['id' => $request->input('id') ?? null], $evaluationData);

            if ($evaluation->status === 'RELEASE') {
                if (empty($evaluation->release_id)) {
                    $prefix = 'EV' . now()->format('ym');
                    $lastId = Evaluation::whereNotNull('release_id')
                        ->where('release_id', 'like', $prefix . '%')
                        ->orderBy('release_id', 'desc')
                        ->value('release_id');
                    $nextNumber = 1;
                    if ($lastId) {
                        $lastNumber = (int)substr($lastId, -4);
                        $nextNumber = $lastNumber + 1;
                    }
                    $releaseId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    $evaluation->release_id = $releaseId;
                }
                $evaluation->release_date = now();

                $recipients = [
                    'approval1' => $evaluation->approval1_id,
                    'approval2' => $evaluation->approval2_id,
                    'approval3' => $evaluation->approval3_id,
                    'approval4' => $evaluation->approval4_id,
                    'approval5' => $evaluation->approval5_id,
                    'approval6' => $evaluation->approval6_id,
                ];

                $recipientId = null;
                $recipientRole = null;
                foreach ($recipients as $role => $id) {
                    if (!empty($id)) {
                        $recipientId = $id;
                        $recipientRole = $role;
                        break;
                    }
                }

                if (!empty($evaluation->drafter_id)) {
                    $drafterUser = User::where('employee_id', $evaluation->drafter_id)->first();
                    if ($drafterUser && !empty($drafterUser->email)) {
                        $actionURL = $drafterUser->hasPermissionTo('emp.menu')
                            ? route('evaluation.emp.index')
                            : route('profile.evaluation');
                        $details = [
                            'greeting' => 'Hi ' . optional($drafterUser->employee)->fullname ?? 'Drafter',
                            'subject' => 'Evaluation Notification',
                            'body' => 'We would like to inform you that an evaluation for "' . optional($evaluation->employee)->fullname . '" has been released and requires your attention.',
                            'actionText' => 'Please Login',
                            'actionURL' => $actionURL,
                            'thanks' => 'Thank you for your attention!!'
                        ];
                        $drafterUser->notify(new EvaluationNotification($details));
                    }
                }

                if ($recipientId) {
                    $recipientUser = User::where('employee_id', $recipientId)->first();
                    if ($recipientUser && !empty($recipientUser->email)) {
                        $actionURL = $recipientUser->hasPermissionTo('emp.menu')
                            ? route('evaluation.emp.index')
                            : route('profile.evaluation');
                        $details = [
                            'greeting' => 'Hi ' . optional($recipientUser->employee)->fullname ?? $recipientRole,
                            'subject' => 'Evaluation Notification',
                            'body' => 'We would like to inform you that an evaluation for "' . optional($evaluation->employee)->fullname . '" has been released and requires your attention.',
                            'actionText' => 'Please Login',
                            'actionURL' => $actionURL,
                            'thanks' => 'Thank you for your attention!!'
                        ];
                        $recipientUser->notify(new EvaluationNotification($details));
                    }
                }
            }
            $evaluation->save();
            $user = auth()->user();

            if ($request->has('deleted_attachments')) {
                $deletedAttachmentIds = $request->input('deleted_attachments');
                foreach ($deletedAttachmentIds as $attachmentId) {
                    $attachment = EvaluationAttachment::find($attachmentId);
                    if ($attachment) {
                        $attachmentName = $attachment->name;
                        if ($attachment->evaluations()->count() <= 1) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
                        }
                        $evaluation->attachments()->detach($attachmentId);
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => strtoupper($evaluation->status),
                            'description' => 'Deleted attachment "' . $attachmentName . '".',
                        ]);
                    }
                }
            }

            if ($request->has('existing_attachment_names')) {
                foreach ($request->input('existing_attachment_names') as $attachmentId => $newName) {
                    $attachment = EvaluationAttachment::find($attachmentId);
                    if ($attachment && $attachment->name !== $newName) {
                        $oldName = $attachment->name;
                        $attachment->update(['name' => $newName]);
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => strtoupper($evaluation->status),
                            'description' => 'Updated attachment name from "' . $oldName . '" to "' . $newName . '".',
                        ]);
                    }
                }
            }

            if ($request->hasFile('new_attachments')) {
                $newAttachmentNames = $request->input('new_attachment_names') ?? [];
                foreach ($request->file('new_attachments') as $key => $file) {
                    $path = $file->store('evaluation/attachment', 'public');
                    $attachmentName = $newAttachmentNames[$key] ?? 'No Name';
                    $attachment = EvaluationAttachment::create([
                        'name' => $attachmentName,
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                    $evaluation->attachments()->attach($attachment->id);
                    EvaluationHistory::create([
                        'evaluation_id' => $evaluation->id,
                        'user_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'action' => strtoupper($evaluation->status),
                        'description' => 'Added new attachment "' . $attachmentName . '".',
                    ]);
                }
            }

            $employeeName = optional($evaluation->employee)->fullname ?? 'N/A';
            $employeeNik = optional($evaluation->employee)->nik ?? 'N/A';
            $logAction = $evaluation->wasRecentlyCreated ? 'insert' : 'update';
            $logDescription = ($evaluation->wasRecentlyCreated ? 'Create New' : 'Modify')
                . ' Evaluation for ' . $employeeName . ' (' . $employeeNik . ') with status: ' . ($evaluation->status ?? 'N/A');

            EvaluationHistory::create([
                'evaluation_id' => $evaluation->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => strtoupper($evaluation->status),
                'description' => $logDescription,
            ]);

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $logAction,
                'description' => $logDescription,
            ]);

            DB::commit();

            return response()->json([
                'message' => "Evaluation for \"$employeeName\" has been saved.",
                'redirect' => $evaluation->wasRecentlyCreated
                    ? route('evaluation.form')
                    : route('evaluation.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('ids');
            $isMultiple = is_array($ids);

            if (!$isMultiple) {
                $ids = [$request->input('id')];
            }

            if (empty($ids)) {
                return redirect()->route('evaluation.index')->with('error', 'No evaluation(s) were selected.');
            }

            $deletedCount = 0;
            $user = auth()->user();

            foreach ($ids as $id) {
                $evaluation = Evaluation::with('attachments')->findOrFail(decrypt($id));
                foreach ($evaluation->attachments as $attachment) {
                    if ($attachment->evaluations()->count() <= 1) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }

                $employeeName = $evaluation->employee->fullname ?? 'N/A';
                $employeeNik = $evaluation->employee->nik ?? 'N/A';
                $evalPeriod = ($evaluation->eval_start && $evaluation->eval_end) ?
                    "{$evaluation->eval_start->format('d M Y')} - {$evaluation->eval_end->format('d M Y')}" :
                    '-';
                $evalPurpose = $evaluation->purpose ?? 'N/A';
                $evalStatus = $evaluation->status ?? 'N/A';

                $logDescription = "Deleted Evaluation for: {$employeeName} ({$employeeNik}) period {$evalPeriod} ({$evalPurpose}) with status ({$evalStatus})";
                if (!empty($evaluation->release_id)) {
                    $logDescription .= " and Release ID: {$evaluation->release_id}";
                }

                $evaluation->delete();

                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'delete',
                    'description' => $logDescription,
                ]);

                $deletedCount++;
            }

            return redirect()->back()->with('success', "$deletedCount evaluation(s) have been successfully deleted.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete evaluation(s): ' . $e->getMessage());
        }
    }

    public function releaseMultiple(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('ids');
            $releasedCount = 0;
            $failedNames = [];
            $user = auth()->user();
            $releasedNames = [];
            $evaluationsByEvaluator = [];
            $evaluationsByDrafter = [];
            foreach ($ids as $id) {
                $evaluation = Evaluation::find(decrypt($id));
                if ($evaluation) {
                    if ($evaluation->status === 'DRAFT') {
                        if (empty($evaluation->release_id)) {
                            $prefix = 'EV' . now()->format('ym');
                            $lastId = Evaluation::whereNotNull('release_id')
                                ->where('release_id', 'like', $prefix . '%')
                                ->orderBy('release_id', 'desc')
                                ->value('release_id');
                            $nextNumber = 1;
                            if ($lastId) {
                                $lastNumber = (int)substr($lastId, -4);
                                $nextNumber = $lastNumber + 1;
                            }
                            $releaseId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                            $evaluation->release_id = $releaseId;
                        }
                        $evaluation->status = 'RELEASE';
                        $evaluation->release_date = now();
                        $evaluation->save();
                        $employeeName = optional($evaluation->employee)->fullname ?? 'N/A';
                        $releasedNames[] = $employeeName;
                        $logDescription = 'Released evaluation for "' . $employeeName . '".';
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => 'RELEASE',
                            'description' => $logDescription,
                        ]);
                        Log::create([
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => 'update',
                            'description' => $logDescription,
                        ]);
                        $releasedCount++;
                        if (!empty($evaluation->approval1_id)) {
                            $evaluationsByEvaluator[$evaluation->approval1_id][] = $evaluation;
                        }
                        if (!empty($evaluation->drafter_id)) {
                            $evaluationsByDrafter[$evaluation->drafter_id][] = $evaluation;
                        }
                    } else {
                        $failedNames[] = optional($evaluation->employee)->fullname ?? 'N/A';
                    }
                }
            }
            // Mulai bagian pengiriman email massal
            // Evaluator
            foreach ($evaluationsByEvaluator as $evaluatorId => $evaluations) {
                $evaluator = User::where('employee_id', $evaluatorId)->first();
                if ($evaluator && !empty($evaluator->email)) {
                    $evaluationsCount = count($evaluations);
                    $bodyMessage = '';
                    $employeeNames = [];
                    if ($evaluationsCount > 10) {
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations have been released and require your attention.';
                    } else {
                        $employeeNames = collect($evaluations)->map(function ($eval) {
                            return optional($eval->employee)->fullname;
                        })->filter()->values()->toArray();
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations for the following employees have been released and require your attention:';
                    }
                    $actionURL = $evaluator->hasPermissionTo('emp.menu')
                        ? route('evaluation.emp.index')
                        : route('profile.evaluation');
                    $details = [
                        'greeting' => 'Hi ' . optional($evaluator->employee)->fullname ?? 'Evaluator',
                        'subject' => 'Evaluation Notification',
                        'body' => $bodyMessage,
                        'employeeNames' => $employeeNames,
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $evaluator->notify(new EvaluationNotification($details));
                }
            }
            // Drafter
            foreach ($evaluationsByDrafter as $drafterId => $evaluations) {
                $drafterUser = User::where('employee_id', $drafterId)->first();
                if ($drafterUser && !empty($drafterUser->email)) {
                    $evaluationsCount = count($evaluations);
                    $bodyMessage = '';
                    $employeeNames = [];
                    if ($evaluationsCount > 10) {
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations have been released and require your attention.';
                    } else {
                        $employeeNames = collect($evaluations)->map(function ($eval) {
                            return optional($eval->employee)->fullname;
                        })->filter()->values()->toArray();
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations for the following employees have been released and require your attention:';
                    }
                    $actionURL = $drafterUser->hasPermissionTo('emp.menu')
                        ? route('evaluation.emp.index')
                        : route('profile.evaluation');
                    $details = [
                        'greeting' => 'Hi ' . optional($drafterUser->employee)->fullname ?? 'Drafter',
                        'subject' => 'Evaluation Notification',
                        'body' => $bodyMessage,
                        'employeeNames' => $employeeNames,
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $drafterUser->notify(new EvaluationNotification($details));
                }
            }
            // Akhir bagian pengiriman email massal

            DB::commit();
            if ($releasedCount > 0) {
                $message = "Successfully released $releasedCount evaluation(s).";
                if (!empty($failedNames)) {
                    $message .= " Failed to release: " . implode(', ', $failedNames);
                }
                return redirect()->route('evaluation.index')->with('success', $message);
            } else {
                $message = 'No DRAFT evaluation(s) were selected or available for release.';
                if (!empty($failedNames)) {
                    $message .= " Failed to release: " . implode(', ', $failedNames);
                }
                return redirect()->route('evaluation.index')->with('error', $message);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('evaluation.index')->with('error', 'Failed to release evaluations: ' . $e->getMessage());
        }
    }

    public function createMultiple()
    {
        $departments = Department::all();
        $areas = Area::all();
        $buildings = Building::all();
        $positions = Position::all();
        $sections = Section::all();
        $approveds = Employee::whereNot('status', 'TERMINATED')->get();
        return view('pages.hrd.evaluation.create-multiple', compact('departments', 'areas', 'buildings', 'positions', 'sections', 'approveds'));
    }

    public function createMultiple_getEmployees(Request $request)
    {
        $query = LineApprovalEmployee::with([
            'employee.position',
            'employee.section',
            'employee.department',
            'lineApproval.draft.position',
            'lineApproval.draft.user',
            'lineApproval.approve1.position',
            'lineApproval.approve1.user',
            'lineApproval.approve2.position',
            'lineApproval.approve2.user',
            'lineApproval.approve3.position',
            'lineApproval.approve3.user',
            'lineApproval.approve4.position',
            'lineApproval.approve4.user',
            'lineApproval.approve5.position',
            'lineApproval.approve5.user',
            'lineApproval.approve6.position',
            'lineApproval.approve6.user'
        ]);
        $query->whereHas('lineApproval', function ($subQuery) {
            $subQuery->where('approval_type', 'Evaluation');
        });
        $query->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereExists(function ($appraisalExistsQuery) {
                $appraisalExistsQuery->from('master_appraisal as appraisals')
                    ->whereColumn('appraisals.position_id', 'employees.position_id')
                    ->whereRaw('LOWER(appraisals.status) = LOWER(employees.status)');
            });
        });
        if ($request->filled('department_id') && $request->department_id != 'ALL') {
            $query->whereHas('employee', function ($subQuery) use ($request) {
                $subQuery->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('area_id') && $request->area_id != 'ALL') {
            $query->whereHas('employee', function ($subQuery) use ($request) {
                $subQuery->where('area_id', $request->area_id);
            });
        }
        if ($request->filled('building_id') && $request->building_id != 'ALL') {
            $query->whereHas('employee', function ($subQuery) use ($request) {
                $subQuery->where('building_id', $request->building_id);
            });
        }
        if ($request->filled('position_id') && $request->position_id != 'ALL') {
            $query->whereHas('employee', function ($subQuery) use ($request) {
                $subQuery->where('position_id', $request->position_id);
            });
        }
        if ($request->filled('section_id') && $request->section_id != 'ALL') {
            $query->whereHas('employee', function ($subQuery) use ($request) {
                $subQuery->where('section_id', $request->section_id);
            });
        }
        $lineApprovalEmployees = $query->get();
        $employees = $lineApprovalEmployees->map(function ($lineApprovalEmployee) {
            $employee = $lineApprovalEmployee->employee;
            $statusBadge = '';
            if ($employee->status == 'PERMANENT') {
                $statusBadge = '<span class="badge text-bg-success">'.$employee->status.'</span>';
            } elseif ($employee->status == 'PROBATION') {
                $statusBadge = '<span class="badge text-bg-secondary">'.$employee->status.'</span>';
            } elseif ($employee->status == 'CONTRACT') {
                $statusBadge = '<span class="badge text-bg-primary">'.$employee->status.'</span>';
            } elseif ($employee->status == 'OUTSOURCING') {
                $statusBadge = '<span class="badge text-bg-info">'.$employee->status.'</span>';
            }
            $lineApproval = $lineApprovalEmployee->lineApproval;
            if ($lineApproval) {
                for ($i = 1; $i <= 6; $i++) {
                    $approveKey = 'approve' . $i;
                    if (isset($lineApproval->$approveKey) && $lineApproval->$approveKey !== null) {
                        $positionName = $lineApproval->$approveKey->position->nama ?? null;
                        $lineApproval->$approveKey->default_role = Evaluation::getDefaultApprovals($positionName);
                    }
                }
            }
            return [
                'id' => $employee->id,
                'nik' => $employee->nik,
                'fullname' => $employee->fullname,
                'position' => $employee->position->nama ?? '-',
                'section' => $employee->section->nama ?? '-',
                'status' => $statusBadge,
                'action' => '<button type="button" class="btn btn-danger btn-sm remove-employee" data-id="' . $employee->id . '"><i class="ri-delete-bin-line"></i></button>',
                'line_approval' => [
                    'id' => $lineApproval->id ?? null,
                    'drafter' => $lineApproval->draft ?? null,
                    'approval1' => $lineApproval->approve1 ?? null,
                    'approval2' => $lineApproval->approve2 ?? null,
                    'approval3' => $lineApproval->approve3 ?? null,
                    'approval4' => $lineApproval->approve4 ?? null,
                    'approval5' => $lineApproval->approve5 ?? null,
                    'approval6' => $lineApproval->approve6 ?? null,
                ]
            ];
        });
        return response()->json($employees);
    }

    public function createMultiple_store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validationRules = [
                'employee_ids' => 'required|string',
                'eval_start' => 'required|date_format:d/m/Y',
                'eval_end' => 'required|date_format:d/m/Y',
                'purpose' => 'required|string',
                'status' => 'required|string',
                'new_attachment_names.*' => 'nullable|string',
                'new_attachments.*' => 'nullable|file|max:10240',
            ];

            for ($i = 1; $i <= 6; $i++) {
                $validationRules["approval{$i}_as"] = 'nullable|string';
            }

            $request->validate($validationRules);

            $employeeIds = json_decode($request->input('employee_ids'), true);

            $eval_start_string = $request->input('eval_start');
            $eval_end_string = $request->input('eval_end');
            $eval_start = Carbon::createFromFormat('d/m/Y', $eval_start_string)->toDateString();
            $eval_end = Carbon::createFromFormat('d/m/Y', $eval_end_string)->toDateString();

            $purpose = $request->input('purpose');
            $status = $request->input('status');

            $user = auth()->user();
            
            $newAttachmentIds = [];
            $attachmentDetails = []; 
            if ($request->hasFile('new_attachments')) {
                $newAttachmentNames = $request->input('new_attachment_names') ?? [];
                foreach ($request->file('new_attachments') as $key => $file) {
                    $path = $file->store('evaluation/attachment', 'public');
                    $attachmentName = $newAttachmentNames[$key] ?? 'No Name';        
                    $newAttachment = EvaluationAttachment::create([
                        'name' => $attachmentName,
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                    $newAttachmentIds[] = $newAttachment->id;
                    $attachmentDetails[] = $attachmentName;
                }
            }

            $evaluationsByEvaluator = [];
            $evaluationsByDrafter = [];
            foreach ($employeeIds as $employeeId) {
                $lineApprovalEmployee = LineApprovalEmployee::where('employee_id', $employeeId)->firstOrFail();
                $lineApproval = $lineApprovalEmployee->lineApproval;
                $employee = $lineApprovalEmployee->employee;
                $appraisal = Appraisal::where('position_id', $employee->position_id)
                    ->whereRaw('LOWER(status) = LOWER(?)', [$employee->status])
                    ->first();

                if (!$appraisal) {
                    throw new Exception("No matching appraisal found for employee {$employee->fullname} with position {$employee->position->nama} and status {$employee->status}.");
                }

                // START CHECK DUPLICATE
                $duplicateCheck = Evaluation::where('employee_id', $employeeId)
                    ->where('eval_start', $eval_start)
                    ->where('eval_end', $eval_end)
                    ->where('purpose', $purpose);
                if ($duplicateCheck->exists()) {
                    throw new Exception("Evaluation for employee \"{$employee->fullname}\" already exists.");
                }
                // END CHECK DUPLICATE

                $evaluationData = [
                    'employee_id' => $employeeId,
                    'appraisal_id' => $appraisal->id,
                    'appraisal_position_id' => $appraisal->position_id,
                    'appraisal_status' => $appraisal->status,
                    'eval_start' => $eval_start,
                    'eval_end' => $eval_end,
                    'purpose' => $purpose,
                    'status' => $status,

                    'approval1_id' => optional($lineApproval->approve1)->id,
                    'approval2_id' => optional($lineApproval->approve2)->id,
                    'approval3_id' => optional($lineApproval->approve3)->id,
                    'approval4_id' => optional($lineApproval->approve4)->id,
                    'approval5_id' => optional($lineApproval->approve5)->id,
                    'approval6_id' => optional($lineApproval->approve6)->id,
                    'drafter_id' => optional($lineApproval->draft)->id,

                    'approval1_as' => $request->input('approval1_as'),
                    'approval2_as' => $request->input('approval2_as'),
                    'approval3_as' => $request->input('approval3_as'),
                    'approval4_as' => $request->input('approval4_as'),
                    'approval5_as' => $request->input('approval5_as'),
                    'approval6_as' => $request->input('approval6_as'),
                ];

                if ($status === 'RELEASE') {
                    $prefix = 'EV' . now()->format('ym');
                    $lastId = Evaluation::whereNotNull('release_id')
                        ->where('release_id', 'like', $prefix . '%')
                        ->orderBy('release_id', 'desc')
                        ->value('release_id');

                    $nextNumber = 1;
                    if ($lastId) {
                        $lastNumber = (int)substr($lastId, -4);
                        $nextNumber = $lastNumber + 1;
                    }
                    $evaluationData['release_id'] = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    $evaluationData['release_date'] = now();
                }

                $evaluation = Evaluation::create($evaluationData);

                if (!empty($newAttachmentIds)) {
                    $evaluation->attachments()->attach($newAttachmentIds);
                    foreach ($attachmentDetails as $attName) {
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => strtoupper($evaluation->status),
                            'description' => 'Added new attachment "' . $attName . '".',
                        ]);
                    }
                }

                if ($status === 'RELEASE') {
                    $recipients = [
                        'approval1' => $evaluation->approval1_id,
                        'approval2' => $evaluation->approval2_id,
                        'approval3' => $evaluation->approval3_id,
                        'approval4' => $evaluation->approval4_id,
                        'approval5' => $evaluation->approval5_id,
                        'approval6' => $evaluation->approval6_id,
                    ];

                    $recipientId = null;
                    foreach ($recipients as $id) {
                        if (!empty($id)) {
                            $recipientId = $id;
                            break;
                        }
                    }

                    if ($recipientId) {
                        if (!isset($evaluationsByEvaluator[$recipientId])) {
                            $evaluationsByEvaluator[$recipientId] = [];
                        }
                        $evaluationsByEvaluator[$recipientId][] = $evaluation;
                    }

                    if (!empty($evaluation->drafter_id)) {
                        $evaluationsByDrafter[$evaluation->drafter_id][] = $evaluation;
                    }
                }

                $logDescription = "Create New Evaluation for {$employee->fullname} ({$employee->nik}) with status: {$evaluation->status}";
                EvaluationHistory::create([
                    'evaluation_id' => $evaluation->id,
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => strtoupper($evaluation->status),
                    'description' => $logDescription,
                ]);
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'insert',
                    'description' => $logDescription,
                ]);
            }

            // Multiple Email
            // Evaluator
            foreach ($evaluationsByEvaluator as $evaluatorId => $evaluations) {
                $evaluator = User::where('employee_id', $evaluatorId)->first();
                if ($evaluator && !empty($evaluator->email)) {
                    $evaluationsCount = count($evaluations);
                    $bodyMessage = '';
                    $employeeNames = [];
                    if ($evaluationsCount > 10) {
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations have been released and require your attention.';
                    } else {
                        $employeeNames = collect($evaluations)->map(function ($eval) {
                            return optional($eval->employee)->fullname;
                        })->filter()->values()->toArray();
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations for the following employees have been released and require your attention:';
                    }
                    $actionURL = $evaluator->hasPermissionTo('emp.menu')
                        ? route('evaluation.emp.index')
                        : route('profile.evaluation');
                    $details = [
                        'greeting' => 'Hi ' . optional($evaluator->employee)->fullname ?? 'Evaluator',
                        'subject' => 'Evaluation Notification',
                        'body' => $bodyMessage,
                        'employeeNames' => $employeeNames,
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $evaluator->notify(new EvaluationNotification($details));
                }
            }
            // Drafter
            foreach ($evaluationsByDrafter as $drafterId => $evaluations) {
                $drafterUser = User::where('employee_id', $drafterId)->first();
                if ($drafterUser && !empty($drafterUser->email)) {
                    $evaluationsCount = count($evaluations);
                    $bodyMessage = '';
                    $employeeNames = [];
                    if ($evaluationsCount > 10) {
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations have been released and require your attention.';
                    } else {
                        $employeeNames = collect($evaluations)->map(function ($eval) {
                            return optional($eval->employee)->fullname;
                        })->filter()->values()->toArray();
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations for the following employees have been released and require your attention:';
                    }
                    $actionURL = $drafterUser->hasPermissionTo('emp.menu')
                        ? route('evaluation.emp.index')
                        : route('profile.evaluation');
                    $details = [
                        'greeting' => 'Hi ' . optional($drafterUser->employee)->fullname ?? 'Drafter',
                        'subject' => 'Evaluation Notification',
                        'body' => $bodyMessage,
                        'employeeNames' => $employeeNames,
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    
                    $drafterUser->notify(new EvaluationNotification($details));
                }
            }
            // End Multiple Email

            DB::commit();
            return response()->json([
                'message' => "Successfully created " . count($employeeIds) . " evaluations.",
                'redirect' => route('evaluation.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function index_done(Request $request)
    {
        if (Auth::user()->can('hrd.evaluation.read')) {
            $evaluationsToFix = Evaluation::where('status', 'DONE')
                ->where('decision_employment', 'Contract Extend')
                ->where('month_extend', '>', 0)
                ->whereNull('date_extend')
                ->with('employee')
                ->get();
            foreach ($evaluationsToFix as $evaluation) {
                DB::beginTransaction();
                if ($evaluation->employee && $evaluation->employee->enddate) {
                    try {
                        $employeeEnddate = Carbon::parse($evaluation->employee->enddate);
                        $monthsToExtend = (int) $evaluation->month_extend;
                        $newContractStart = $employeeEnddate->copy()->addDay();
                        $newEnddate = $newContractStart->addMonths($monthsToExtend)->subDay();
                        $evaluation->date_extend = $newEnddate;
                        $evaluation->save();
                        if (!empty($evaluation->date_extend) && $evaluation->employee->status == 'CONTRACT') {
                            $employeeModel = $evaluation->employee;
                            $employeeModel->enddate = $evaluation->date_extend;
                            $employeeModel->save();
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                    }
                }
            }
        }

        if ($request->ajax()) {
            $tahun = $request->get('tahun');
            $query = Evaluation::with(['employee', 'appraisal.department'])
                ->where('evaluations.status', 'DONE');
            if ($tahun) {
                $query->whereYear('evaluations.release_date', $tahun);
            }
            $data = $query->get();
            $formattedData = [];
            foreach ($data as $row) {
                $badges = [
                    'DONE' => 'success',
                ];
                $status = $row->status;
                $badgeClass = $badges[$status] ?? 'secondary';
                $formattedStatus = "<span class=\"badge text-bg-{$badgeClass}\">{$status}</span>";
                $start = optional($row->eval_start)->format('d M Y') ?? '-';
                $end = optional($row->eval_end)->format('d M Y') ?? '-';
                $period = "{$start} - {$end}";
                $btn = '';
                if (Auth::user()->can('hrd.evaluation.read')) {
                    $encryptedId = encrypt($row->id);
                    $btn .= "<a href=\"" . route('evaluation.done.detail', ['id' => $encryptedId]) . "\" title=\"Detail\" class=\"btn btn-info btn-sm me-1\"><i class=\"ri-eye-2-line\"></i></a>";
                    $excluded_statuses = ['DRAFT', 'REVISE', 'REJECT'];
                    if (!in_array($row->status, $excluded_statuses)) {
                        $btn .= "<a href=\"#\" data-id=\"" . $encryptedId . "\" title=\"Steps\" class=\"btn btn-primary btn-sm me-1 btn-view-steps\"><i class=\"ri-list-check\"></i></a>";
                    }
                    if ($row->status === 'DONE') {
                        $btn .= "<a href=\"" . route('evaluation.done.print', ['evaluation' => $encryptedId]) . "\" target=\"_blank\" title=\"Print\" class=\"btn btn-success btn-sm me-1\"><i class=\"ri-printer-fill\"></i></a>";
                    }
                    if (!empty($row->decision_reason)) {
                        $btn .= "<a href=\"#\" data-id=\"" . $encryptedId . "\" title=\"Decision Reason\" class=\"btn btn-secondary btn-sm me-1 btn-reason\"><i class=\"ri-information-line\"></i></a>";
                    }
                    if (Auth::user()->can('hrd.evaluation.delete')) {
                        $btn .= "<a href=\"#\" data-id=\"" . $encryptedId . "\" data-toggle=\"tooltip\" title=\"Delete\" class=\"btn btn-danger btn-sm me-1 delete-btn\"><i class=\"ri-delete-bin-line\"></i></a>";
                    }
                }
                $startRaw = $row->eval_start;
                $endRaw = $row->eval_end;
                $formattedData[] = [
                    'id' => encrypt($row->id),
                    'release_id' => $row->release_id ?? '-',
                    'nik' => $row->employee->nik ?? '-',
                    'name' => $row->employee->fullname ?? '-',
                    'department' => $row->employee->department->name ?? '-',
                    'section' => $row->employee->section->nama ?? '-',
                    'position' => $row->employee->position->nama ?? '-',
                    'building' => $row->employee->building->nama ?? '-',
                    'start' => [
                        'display' => $startRaw ? $startRaw->format('d M Y') : '-',
                        'timestamp' => $startRaw ? $startRaw->getTimestamp() : 0
                    ],
                    'end' => [
                        'display' => $endRaw ? $endRaw->format('d M Y') : '-',
                        'timestamp' => $endRaw ? $endRaw->getTimestamp() : 0
                    ],
                    'purpose' => $row->purpose,
                    'total_score' => $row->total_score ?? '-',
                    'grade' => $row->grade ?? '-',
                    'decision' => $row->decision_employment ?? '-',
                    'status' => $formattedStatus,
                    'action' => $btn,
                    'created_at' => $row->created_at,
                ];
            }
            return response()->json(['data' => $formattedData]);
        }

        $years = Evaluation::whereNotNull('release_date')
            ->where('evaluations.status', 'DONE')
            ->selectRaw('YEAR(release_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4);
        }
        return view('pages.hrd.evaluation.done', compact('years'));
    }

    private function getBaseEvaluationQuery($type, $year = null)
    {
        $loggedInEmployeeId = Auth::user()->employee_id;

        $query = Evaluation::query()
            ->leftJoin('master_appraisal as ma', 'ma.id', '=', 'evaluations.appraisal_id')
            ->leftJoin('departments as dept', 'dept.id', '=', 'ma.department_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'evaluations.employee_id')
            ->select('evaluations.*', 'dept.name as dept_name', 'emp.fullname as emp_name', 'ma.form_type', 'ma.kpi_weight', 'ma.attendance as attendance');
        $query->whereNotIn('evaluations.status', ['DRAFT', 'REJECT', 'REVISE']);
        if ($type === 'process') {
            $query->where(function ($q) use ($loggedInEmployeeId) {
                // Drafter
                $q->where(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.drafter_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.drafter_date');
                });

                // Approval 2
                $q->orWhere(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.approval1_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.approval1_date');
                });

                // Approval 2
                $q->orWhere(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.approval2_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.approval2_date')
                        ->whereNotNull('evaluations.approval1_date');
                });

                // Approval 3
                $q->orWhere(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.approval3_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.approval3_date')
                        ->where(function($prev) {
                            $prev->whereNotNull('evaluations.approval2_date')
                                ->orWhereNull('evaluations.approval2_id');
                        });
                });

                // Approval 4
                $q->orWhere(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.approval4_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.approval4_date')
                        ->where(function($prev) {
                            $prev->whereNotNull('evaluations.approval3_date')
                                ->orWhereNull('evaluations.approval3_id');
                        });
                });

                // Approval 5
                $q->orWhere(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.approval5_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.approval5_date')
                        ->where(function($prev) {
                            $prev->whereNotNull('evaluations.approval4_date')
                                ->orWhereNull('evaluations.approval4_id');
                        });
                });

                // Approval 6
                $q->orWhere(function($sub) use ($loggedInEmployeeId) {
                    $sub->where('evaluations.approval6_id', $loggedInEmployeeId)
                        ->whereNull('evaluations.approval6_date')
                        ->where(function($prev) {
                            $prev->whereNotNull('evaluations.approval5_date')
                                ->orWhereNull('evaluations.approval5_id');
                        });
                });
            });
        } elseif ($type === 'done') {
            $query->where(function ($q) use ($loggedInEmployeeId) {
                $q->where('evaluations.approval1_id', $loggedInEmployeeId)->whereNotNull('evaluations.approval1_date')
                    ->orWhere(fn($subQ) => $subQ->where('evaluations.approval2_id', $loggedInEmployeeId)->whereNotNull('evaluations.approval2_date'))
                    ->orWhere(fn($subQ) => $subQ->where('evaluations.approval3_id', $loggedInEmployeeId)->whereNotNull('evaluations.approval3_date'))
                    ->orWhere(fn($subQ) => $subQ->where('evaluations.approval4_id', $loggedInEmployeeId)->whereNotNull('evaluations.approval4_date'))
                    ->orWhere(fn($subQ) => $subQ->where('evaluations.approval5_id', $loggedInEmployeeId)->whereNotNull('evaluations.approval5_date'))
                    ->orWhere(fn($subQ) => $subQ->where('evaluations.approval6_id', $loggedInEmployeeId)->whereNotNull('evaluations.approval6_date'));
            });
            if ($year) {
                $query->whereYear('evaluations.release_date', $year);
            }
        }
        return $query;
    }

    private function getActionRole($data, $loggedInEmployeeId)
    {
        $isApproval1Done = !is_null($data->approval1_date);
        $isApproval2Done = !is_null($data->approval2_date);
        $isApproval3Done = !is_null($data->approval3_date);
        $isApproval4Done = !is_null($data->approval4_date);
        $isApproval5Done = !is_null($data->approval5_date);

        if (!empty($data->drafter_id)) {
            if ($data->drafter_id == $loggedInEmployeeId && is_null($data->drafter_date)) {
                return 'drafter';
            }
        }
        if ($data->approval1_id == $loggedInEmployeeId && is_null($data->approval1_date)) {
            return 'approval1';
        } elseif ($data->approval2_id == $loggedInEmployeeId && is_null($data->approval2_date) && $isApproval1Done) {
            return 'approval2';
        } elseif ($data->approval3_id == $loggedInEmployeeId && is_null($data->approval3_date)) {
            if ($isApproval2Done || (!is_null($data->approval1_id) && is_null($data->approval2_id) && $isApproval1Done)) {
                return 'approval3';
            }
        } elseif ($data->approval4_id == $loggedInEmployeeId && is_null($data->approval4_date)) {
            if ($isApproval3Done || (!is_null($data->approval2_id) && is_null($data->approval3_id) && $isApproval2Done) || (!is_null($data->approval1_id) && is_null($data->approval2_id) && is_null($data->approval3_id) && $isApproval1Done)) {
                return 'approval4';
            }
        } elseif ($data->approval5_id == $loggedInEmployeeId && is_null($data->approval5_date)) {
            if ($isApproval4Done || ($isApproval3Done && is_null($data->approval4_id)) || ($isApproval2Done && is_null($data->approval4_id) && is_null($data->approval3_id)) || ($isApproval1Done && is_null($data->approval4_id) && is_null($data->approval3_id) && is_null($data->approval2_id))) {
                return 'approval5';
            }
        } elseif ($data->approval6_id == $loggedInEmployeeId && is_null($data->approval6_date)) {
            if ($isApproval5Done || ($isApproval4Done && is_null($data->approval5_id)) || ($isApproval3Done && is_null($data->approval5_id) && is_null($data->approval4_id)) || ($isApproval2Done && is_null($data->approval5_id) && is_null($data->approval4_id) && is_null($data->approval3_id)) || ($isApproval1Done && is_null($data->approval5_id) && is_null($data->approval4_id) && is_null($data->approval3_id) && is_null($data->approval2_id))) {
                return 'approval6';
            }
        }
        return '';
    }

    private function getDataTablesInstance($query, $type)
    {
        $loggedInEmployeeId = Auth::user()->employee_id;
        $user = auth()->user();
        $dataTable = DataTables::of($query)
            ->editColumn('id', fn($data) => encrypt($data->id))
            ->addColumn('name', fn($data) => $data->emp_name ?? '-')
            ->addColumn('department', fn($data) => $data->dept_name ?? '-')
            ->addColumn('period', function ($data) {
                $start = optional(Carbon::parse($data->eval_start))->format('d M Y') ?? '-';
                $end = optional(Carbon::parse($data->eval_end))->format('d M Y') ?? '-';
                return "{$start} - {$end}";
            })
            ->editColumn('nik', fn($data) => $data->employee->nik ?? '-')
            ->editColumn('purpose', fn($data) => $data->purpose)
            ->addColumn('status', function ($data) {
                $badges = [
                    'RELEASE' => 'success',
                    'DRAFT' => 'secondary',
                    'REVISE' => 'danger',
                    'REJECT' => 'dark',
                    '1st Evaluator' => 'success',
                    '2nd Evaluator' => 'success',
                    '3rd Evaluator' => 'success',
                    'HRD Approved' => 'success',
                    'Prodir' => 'success',
                    'Presdir' => 'success',
                    'DONE' => 'success',
                ];
                $status = $data->status;
                $displayText = ($status === 'RELEASE') ? 'HRD' : $status;
                return isset($badges[$status]) 
                    ? "<span class=\"badge text-bg-{$badges[$status]}\">{$displayText}</span>" 
                    : '-';
            })
            ->addColumn('kpi_score', fn($data) => $data->kpi_sc ?? '-')
            ->addColumn('attendance_score', fn($data) => $data->attendance_sc ?? '-')
            ->editColumn('ap_s', fn($data) => $data->ap_s ?? '-')
            ->editColumn('total_score', fn($data) => $data->total_score ?? '-')
            ->editColumn('grade', fn($data) => $data->grade ?? '-')
            ->addColumn('decision', fn($data) => $data->decision_employment ?? '-')
            ->addIndexColumn()
            ->rawColumns(['action', 'status']);
        if ($type === 'process') {
            $dataTable->addColumn('action', function ($data) use ($loggedInEmployeeId, $user) {
                $role = $this->getActionRole($data, $loggedInEmployeeId);
                if ($role) {
                    $token = encrypt($data->id . '|' . $role);
                    $restrictedStatuses = ['RELEASE', 'REVISE', 'DRAFT', 'REJECT'];
                    if ($user->hasPermissionTo('emp.menu')) {
                        return '<a href="' . route('evaluate.emp.public', $token) . '" title="Evaluate" class="btn btn-success btn-sm"><i class="ri-quill-pen-line"></i></a>'
                            . '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></a>'
                            . (!in_array($data->status, $restrictedStatuses)
                                ? '&nbsp;<a href="' . route('evaluation.emp.print', ['evaluation' => encrypt($data->id)]) . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>'
                                : ''
                            );
                    } else {
                        return '<a href="' . route('profile.evaluate.public', $token) . '" title="Evaluate" class="btn btn-success btn-sm"><i class="ri-quill-pen-line"></i></a>'
                            . '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></a>'
                            . (!in_array($data->status, $restrictedStatuses)
                                ? '&nbsp;<a href="' . route('profile.evaluation.print', ['evaluation' => encrypt($data->id)]) . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>'
                                : ''
                            );
                    }
                }
                return '-';
            })
                ->addColumn('has_action', function ($data) use ($loggedInEmployeeId) {
                    $role = $this->getActionRole($data, $loggedInEmployeeId);
                    return !empty($role);
                })
                ->addColumn('role', function ($data) use ($loggedInEmployeeId) {
                    return $this->getActionRole($data, $loggedInEmployeeId);
                })
                ->rawColumns(['action', 'status']);
        } elseif ($type === 'done') {
            $dataTable->addColumn('action', function ($data) use ($loggedInEmployeeId, $user) {
                $restrictedStatuses = ['RELEASE', 'REVISE', 'DRAFT', 'REJECT'];
                if ($user->hasPermissionTo('emp.menu')) {
                    return '<a href="#" data-id="' . encrypt($data->id) . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></a>'
                        . (!in_array($data->status, $restrictedStatuses)
                            ? '&nbsp;<a href="' . route('evaluation.emp.print', ['evaluation' => encrypt($data->id)]) . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>'
                            : ''
                        );
                } else {
                    return '<a href="#" data-id="' . encrypt($data->id) . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></a>'
                        . (!in_array($data->status, $restrictedStatuses)
                            ? '&nbsp;<a href="' . route('profile.evaluation.print', ['evaluation' => encrypt($data->id)]) . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>'
                            : ''
                        );
                }
            })
                ->addColumn('has_action', function ($data) use ($loggedInEmployeeId) {
                    $role = $this->getActionRole($data, $loggedInEmployeeId);
                    return !empty($role);
                })
                ->addColumn('role', function ($data) use ($loggedInEmployeeId) {
                    return $this->getActionRole($data, $loggedInEmployeeId);
                })
                ->rawColumns(['action', 'status']);
        }
        return $dataTable->make(true);
    }

    // --- Public Functions for 'employee' routes ---

    public function emp_index(Request $request)
    {
        $user = auth()->user();
        $query = $this->getBaseEvaluationQuery('process');
        $jml_process = $query->count();
        $years = Evaluation::whereNotNull('release_date')
            ->selectRaw('YEAR(release_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4);
        }
        return view('pages.employee.evaluation.index', compact('user', 'jml_process', 'years'));
    }

    public function emp_detail($id)
    {
        $user = auth()->user();
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }
        $evaluation = Evaluation::with('evaluationHistories', 'attachments')
            ->findOrFail($id);
        return view('pages.employee.evaluation.detail', compact('user', 'evaluation'));
    }

    public function countProcess()
    {
        $query = $this->getBaseEvaluationQuery('process');
        $jml_process = $query->count();
        return response()->json(['jml_process' => $jml_process]);
    }

    public function getProcess(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->getBaseEvaluationQuery('process');
            return $this->getDataTablesInstance($query, 'process');
        }
    }

    public function getDone(Request $request)
    {
        if ($request->ajax()) {
            $year = $request->input('year');
            $query = $this->getBaseEvaluationQuery('done', $year);
            return $this->getDataTablesInstance($query, 'done');
        }
    }

    // --- Public Functions for 'profile' routes ---

    public function profile_index(Request $request)
    {
        $user = auth()->user();
        $query = $this->getBaseEvaluationQuery('process');
        $jml_process = $query->count();
        $years = Evaluation::whereNotNull('release_date')
            ->selectRaw('YEAR(release_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4);
        }
        return view('pages.profile.evaluation.index', compact('user', 'jml_process', 'years'));
    }

    public function profile_countProcess()
    {
        $query = $this->getBaseEvaluationQuery('process');
        $jml_process = $query->count();
        return response()->json(['jml_process' => $jml_process]);
    }

    public function profile_getProcess(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->getBaseEvaluationQuery('process');
            return $this->getDataTablesInstance($query, 'process');
        }
    }

    public function profile_getDone(Request $request)
    {
        if ($request->ajax()) {
            $year = $request->input('year');
            $query = $this->getBaseEvaluationQuery('done', $year);
            return $this->getDataTablesInstance($query, 'done');
        }
    }

    public function profile_detail($id)
    {
        $user = auth()->user();
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }
        $evaluation = Evaluation::with('evaluationHistories', 'attachments')
            ->findOrFail($id);
        return view('pages.profile.evaluation.detail', compact('user', 'evaluation'));
    }

    public function evaluate($token)
    {
        try {
            [$id, $role] = explode('|', decrypt($token));
        } catch (\Exception $e) {
            abort(404, 'Invalid or expired evaluation link.');
        }
        $user = auth()->user();
        $evaluation = Evaluation::findOrFail($id);
        $loggedInEmployeeId = Auth::user()->employee_id;
        if (in_array($evaluation->status, ['DRAFT', 'REJECT', 'REVISE', 'DONE'])) {
            abort(403, 'This evaluation is not available for review.');
        }
        $approvalSteps = [
            'drafter'   => ['id_field' => 'drafter_id',   'date_field' => 'drafter_date'  ],
            'approval1' => ['id_field' => 'approval1_id', 'date_field' => 'approval1_date'],
            'approval2' => ['id_field' => 'approval2_id', 'date_field' => 'approval2_date'],
            'approval3' => ['id_field' => 'approval3_id', 'date_field' => 'approval3_date'],
            'approval4' => ['id_field' => 'approval4_id', 'date_field' => 'approval4_date'],
            'approval5' => ['id_field' => 'approval5_id', 'date_field' => 'approval5_date'],
            'approval6' => ['id_field' => 'approval6_id', 'date_field' => 'approval6_date'],
        ];
        $currentStep = $approvalSteps[$role] ?? null;
        if (!$currentStep) {
            abort(403, 'Invalid role for evaluation.');
        }
        if ($loggedInEmployeeId != $evaluation->{$currentStep['id_field']}) {
            abort(403, 'You are not authorized to view this evaluation.');
        }
        $isPreviousStepCompleted = true;
        $failedStepDetail = null; // debug
        foreach ($approvalSteps as $stepRole => $step) {
            if ($stepRole === $role) {
                break;
            }
            // if ($role === 'approval1' && $stepRole === 'drafter') {
            //     continue;
            // }
            if ($stepRole === 'drafter') {
                continue;
            }
            if (!empty($evaluation->{$step['id_field']}) && empty($evaluation->{$step['date_field']})) {
                $isPreviousStepCompleted = false;
                // Debug
                $failedStepDetail = [
                    'step'       => $stepRole,
                    'id_field'   => $step['id_field'],
                    'id_value'   => $evaluation->{$step['id_field']},
                    'date_field' => $step['date_field'],
                    'date_value' => $evaluation->{$step['date_field']},
                ];
                break;
            }
        }
        if (!$isPreviousStepCompleted) {
            abort(403, 'Previous approval steps have not been completed. '
                . 'Blocked at step: [' . $failedStepDetail['step'] . '] '
                . $failedStepDetail['id_field'] . '=' . $failedStepDetail['id_value'] . ' | '
                . $failedStepDetail['date_field'] . '=' . ($failedStepDetail['date_value'] ?? 'NULL (belum diisi)')
            );
        }
        $evaluator = Employee::find($evaluation->{$currentStep['id_field']});
        $viewPath = ($user->hasPermissionTo('emp.menu'))
            ? 'pages.employee.evaluation.eval'
            : 'pages.profile.evaluation.eval';
        return view($viewPath, compact(
            'evaluation',
            'role',
            'token',
            'evaluator',
            'user'
        ));
    }

    public function evaluate_store(Request $request, $token)
    {
        try {
            [$id, $role] = explode('|', decrypt($token));
        } catch (\Exception $e) {
            abort(404, 'Invalid token.');
        }

        $user = auth()->user();
        $loggedInEmployeeId = Auth::user()->employee_id;
        $evaluation = Evaluation::with(['approval1', 'approval2', 'approval3', 'approval4', 'approval5', 'approval6', 'employee'])
            ->findOrFail($id);

        if (in_array($evaluation->status, ['DRAFT', 'REJECT', 'DONE'])) {
            return redirect()->route('evaluation.index')->with('swal_error', 'Evaluation is not available for review.');
        }

        $approvalSteps = [
            'drafter'   => ['id_field' => 'drafter_id',   'date_field' => 'drafter_date',   'as_field' => null],
            'approval1' => ['id_field' => 'approval1_id', 'date_field' => 'approval1_date', 'as_field' => 'approval1_as'],
            'approval2' => ['id_field' => 'approval2_id', 'date_field' => 'approval2_date', 'as_field' => 'approval2_as'],
            'approval3' => ['id_field' => 'approval3_id', 'date_field' => 'approval3_date', 'as_field' => 'approval3_as'],
            'approval4' => ['id_field' => 'approval4_id', 'date_field' => 'approval4_date', 'as_field' => 'approval4_as'],
            'approval5' => ['id_field' => 'approval5_id', 'date_field' => 'approval5_date', 'as_field' => 'approval5_as'],
            'approval6' => ['id_field' => 'approval6_id', 'date_field' => 'approval6_date', 'as_field' => 'approval6_as'],
        ];

        $currentStep = $approvalSteps[$role] ?? null;

        if (!$currentStep || $loggedInEmployeeId != $evaluation->{$currentStep['id_field']}) {
            abort(403, 'You are not authorized to fill this form.');
        }

        $asField = $currentStep['as_field'] ?? null;
        $isHrd = $asField && $evaluation->$asField === 'HRD Approval';

        $rules = [
            'kpi_w' => 'nullable|integer|min:0|max:100',
            'kpi_s' => 'nullable|numeric|min:0|max:100',
            'kpi_sc' => 'nullable|numeric|min:0|max:100',
            'kpi_c' => 'nullable|string',

            'ap_managerial_w' => 'nullable|integer|min:0|max:100',
            'ap_managerial_s' => 'nullable|integer|min:10|max:100',
            'ap_managerial_sc' => 'nullable|numeric|min:0|max:100',
            'ap_managerial_c' => 'nullable|string',

            'ap_ability_response_w' => 'nullable|integer|min:0|max:100',
            'ap_ability_response_s' => 'nullable|integer|min:10|max:100',
            'ap_ability_response_sc' => 'nullable|numeric|min:0|max:100',
            'ap_ability_response_c' => 'nullable|string',

            'ap_leadership_w' => 'nullable|integer|min:0|max:100',
            'ap_leadership_s' => 'nullable|integer|min:10|max:100',
            'ap_leadership_sc' => 'nullable|numeric|min:0|max:100',
            'ap_leadership_c' => 'nullable|string',

            'ap_accuracy_w' => 'nullable|integer|min:0|max:100',
            'ap_accuracy_s' => 'nullable|integer|min:10|max:100',
            'ap_accuracy_sc' => 'nullable|numeric|min:0|max:100',
            'ap_accuracy_c' => 'nullable|string',

            'ap_capability_w' => 'nullable|integer|min:0|max:100',
            'ap_capability_s' => 'nullable|integer|min:10|max:100',
            'ap_capability_sc' => 'nullable|numeric|min:0|max:100',
            'ap_capability_c' => 'nullable|string',

            'ap_initiative_w' => 'nullable|integer|min:0|max:100',
            'ap_initiative_s' => 'nullable|integer|min:10|max:100',
            'ap_initiative_sc' => 'nullable|numeric|min:0|max:100',
            'ap_initiative_c' => 'nullable|string',

            'ap_kaizen_w' => 'nullable|integer|min:0|max:100',
            'ap_kaizen_s' => 'nullable|integer|min:10|max:100',
            'ap_kaizen_sc' => 'nullable|numeric|min:0|max:100',
            'ap_kaizen_c' => 'nullable|string',

            'ap_responsibility_w' => 'nullable|integer|min:0|max:100',
            'ap_responsibility_s' => 'nullable|integer|min:10|max:100',
            'ap_responsibility_sc' => 'nullable|numeric|min:0|max:100',
            'ap_responsibility_c' => 'nullable|string',

            'ap_discipline_w' => 'nullable|integer|min:0|max:100',
            'ap_discipline_s' => 'nullable|integer|min:10|max:100',
            'ap_discipline_sc' => 'nullable|numeric|min:0|max:100',
            'ap_discipline_c' => 'nullable|string',

            'ap_cooperation_w' => 'nullable|integer|min:0|max:100',
            'ap_cooperation_s' => 'nullable|integer|min:10|max:100',
            'ap_cooperation_sc' => 'nullable|numeric|min:0|max:100',
            'ap_cooperation_c' => 'nullable|string',

            'ap_w' => 'nullable|integer|min:0|max:100',
            'ap_s' => 'nullable|numeric|min:0|max:100',
            'ap_sc' => 'nullable|numeric|min:0|max:100',

            'attendance_w' => 'nullable|integer|min:0|max:100',
            'attendance_s' => 'nullable|numeric|min:0|max:100',
            'attendance_sc' => 'nullable|numeric|min:0|max:100',
            'attendance_c' => 'nullable|string',

            'positive' => 'nullable|string|max:189',
            'weakness' => 'nullable|string|max:189',
            'decision_employment' => 'nullable|string|in:Contract extend,Assign as permanent employee,Terminated',
            'month_extend' => 'nullable|string',
            'decision_reason' => 'nullable|string',

            'action_type' => 'required|string|in:draft,submit',

            'minus_poin'  => $isHrd ? 'nullable|integer|in:0,2,5,10,25,40' : 'required|integer|in:0,2,5,10,25,40',
            'total_score' => $isHrd ? 'nullable|numeric' : 'required|numeric',
            'grade'       => $isHrd ? 'nullable|string|max:1' : 'required|string|max:1',
            'note_hrd' => 'nullable|string',
            'approval_reason' => 'nullable|string|max:255',
        ];

        $actionType = $request->input('action_type');

        if ($actionType === 'submit' && !$isHrd) {
            $rules['minus_poin']  = 'required|integer|in:0,2,5,10,25,40';
            $rules['total_score'] = 'required|numeric';
            $rules['grade']       = 'required|string|max:1';
        }

        $validatedData = $request->validate($rules);

        // Trim String Value
        foreach ($validatedData as $key => $value) {
            if (is_string($value)) {
                $validatedData[$key] = trim($value);
            }
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $originalData = $evaluation->getOriginal();
            if ($isHrd) {
                $evaluation->note_hrd              = $validatedData['note_hrd'] ?? $evaluation->note_hrd;
                $evaluation->decision_employment   = $validatedData['decision_employment'] ?? $evaluation->decision_employment;
                $evaluation->month_extend          = $validatedData['month_extend'] ?? $evaluation->month_extend;
                $evaluation->decision_reason       = $validatedData['decision_reason'] ?? $evaluation->decision_reason;
            } else {
                $evaluation->fill($validatedData);
            }
            if (in_array($role, ['approval1', 'approval2', 'approval3', 'approval4', 'approval5', 'approval6'])) {
                $reasonColumn = $role . '_reason'; 
                $evaluation->$reasonColumn = $validatedData['approval_reason'] ?? null;
            }
            if ($actionType === 'submit') {
                if (!in_array($role, ['approval1', 'drafter'])) {
                    $labels = [
                        'kpi_w' => 'KPI Weight',
                        'kpi_s' => 'KPI Achievement',
                        'kpi_sc' => 'KPI Score',
                        'kpi_c' => 'KPI Comment',

                        'ap_managerial_w' => 'Managerial Weight',
                        'ap_managerial_s' => 'Managerial Achievement',
                        'ap_managerial_sc' => 'Managerial Score',
                        'ap_managerial_c' => 'Managerial Comment',

                        'ap_ability_response_w' => 'Ability Response Weight',
                        'ap_ability_response_s' => 'Ability Response Achievement',
                        'ap_ability_response_sc' => 'Ability Response Score',
                        'ap_ability_response_c' => 'Ability Response Comment',

                        'ap_leadership_w' => 'Leadership Weight',
                        'ap_leadership_s' => 'Leadership Achievement',
                        'ap_leadership_sc' => 'Leadership Score',
                        'ap_leadership_c' => 'Leadership Comment',

                        'ap_accuracy_w' => 'Accuracy Weight',
                        'ap_accuracy_s' => 'Accuracy Achievement',
                        'ap_accuracy_sc' => 'Accuracy Score',
                        'ap_accuracy_c' => 'Accuracy Comment',

                        'ap_capability_w' => 'Capability Weight',
                        'ap_capability_s' => 'Capability Achievement',
                        'ap_capability_sc' => 'Capability Score',
                        'ap_capability_c' => 'Capability Comment',

                        'ap_initiative_w' => 'Initiative Weight',
                        'ap_initiative_s' => 'Initiative Achievement',
                        'ap_initiative_sc' => 'Initiative Score',
                        'ap_initiative_c' => 'Initiative Comment',

                        'ap_kaizen_w' => 'Kaizen Weight',
                        'ap_kaizen_s' => 'Kaizen Achievement',
                        'ap_kaizen_sc' => 'Kaizen Score',
                        'ap_kaizen_c' => 'Kaizen Comment',

                        'ap_responsibility_w' => 'Responsibility Weight',
                        'ap_responsibility_s' => 'Responsibility Achievement',
                        'ap_responsibility_sc' => 'Responsibility Score',
                        'ap_responsibility_c' => 'Responsibility Comment',

                        'ap_discipline_w' => 'Discipline Weight',
                        'ap_discipline_s' => 'Discipline Achievement',
                        'ap_discipline_sc' => 'Discipline Score',
                        'ap_discipline_c' => 'Discipline Comment',

                        'ap_cooperation_w' => 'Cooperation Weight',
                        'ap_cooperation_s' => 'Cooperation Achievement',
                        'ap_cooperation_sc' => 'Cooperation Score',
                        'ap_cooperation_c' => 'Cooperation Comment',

                        'ap_w' => 'Attitude & Performance Weight',
                        'ap_sc' => 'Attitude & Performance Sum Score',
                        'ap_s' => 'Attitude & Performance Score',

                        'attendance_w' => 'Attendance Weight',
                        'attendance_s' => 'Attendance Achievement',
                        'attendance_sc' => 'Attendance Score',
                        'attendance_c' => 'Attendance Comment',

                        'minus_poin' => 'Minus Point',
                        'total_score' => 'Total Score',
                        'grade' => 'Grade',
                        'positive' => 'Positive Matters',
                        'weakness' => 'Weakness Matters',
                        'decision_employment' => 'Employment Decision',
                        'month_extend' => 'Month Extend',
                        'decision_reason' => 'Reason Decision',
                    ];
                    $changes = [];
                    foreach ($validatedData as $field => $value) {
                        $oldValue = $originalData[$field] ?? null;
                        if ($oldValue != $value) {
                            $label = $labels[$field] ?? $field;
                            $changes[] = "$label: '{$oldValue}' -> '{$value}'";
                        }
                    }
                    $changeText = count($changes) ? ' | Changes: ' . implode(', ', $changes) : '';
                } else {
                    $changeText = '';
                }

                if ($role === 'drafter') {
                    $evaluation->drafter_date = $now;
                    $nextRole = 'approval1';
                    $nextEvaluatorId = $evaluation->approval1_id;
                    $logDescription = "Evaluation Submitted by Drafter for Evaluation {$evaluation->release_id}";
                    $logAction = 'update';
                } else {
                    $evaluation->{$currentStep['date_field']} = $now; 
                    $currentApprovalAs = $evaluation->{$currentStep['as_field']};
                    switch ($currentApprovalAs) {
                        case '1st Evaluator': $evaluation->status = '1st Evaluator'; break;
                        case '2nd Evaluator': $evaluation->status = '2nd Evaluator'; break;
                        case '3rd Evaluator': $evaluation->status = '3rd Evaluator'; break;
                        case 'HRD Approval':  $evaluation->status = 'HRD Approved';  break;
                        case 'Director':      $evaluation->status = 'Prodir';        break;
                        case 'President Director': $evaluation->status = 'Presdir';  break;
                        default: $evaluation->status = ucfirst(str_replace('approval', 'approval ', $role)); break;
                    }
                    $logDescription = "Evaluation Approved by ({$role}) for Evaluation {$evaluation->release_id} {$changeText}";
                    $logAction = 'approved';
                }

                $nextRole = null;
                $nextEvaluatorId = null;
                $foundCurrent = false;
                foreach ($approvalSteps as $stepRole => $step) {
                    if ($stepRole === $role) {
                        $foundCurrent = true;
                        continue;
                    }
                    if ($foundCurrent && !empty($evaluation->{$step['id_field']})) {
                        $nextRole = $stepRole;
                        $nextEvaluatorId = $evaluation->{$step['id_field']};
                        break;
                    }
                }

                if (!$nextRole) {
                    $evaluation->status = 'DONE';
                    if ($evaluation->employee && $evaluation->employee->enddate && $evaluation->month_extend > 0) {
                        $employeeEnddate = Carbon::parse($evaluation->employee->enddate);
                        $monthsToExtend = (int) $evaluation->month_extend;
                        $newContractStart = $employeeEnddate->copy()->addDay();
                        $newEnddate = $newContractStart->addMonths($monthsToExtend)->subDay();
                        $evaluation->date_extend = $newEnddate;
                        $evaluation->save();
                        if (!empty($evaluation->date_extend) && $evaluation->employee->status == 'CONTRACT') {
                            $employeeModel = $evaluation->employee;
                            $currentEmpEnd = Carbon::parse($employeeModel->enddate)->format('Y-m-d');
                            $evalEndTarget = Carbon::parse($evaluation->eval_end)->format('Y-m-d');
                            if ($currentEmpEnd === $evalEndTarget) {
                                $employeeModel->enddate = $evaluation->date_extend;
                                $employeeModel->save();
                            }
                        }
                    }
                }
            } else {
                $logDescription = "Evaluation Saved as Draft by ({$role}) for Evaluation {$evaluation->release_id}";
                $logAction = 'update';
                if ($role === 'drafter') {
                    $evaluation->drafter_date = null;
                }
                if ($role === 'approval1') {
                    $evaluation->approval1_date = null;
                }
                $nextEvaluatorId = null;
                $nextRole = null;
            }

            $evaluation->updated_at = $now;
            $evaluation->save();

            // Start Attachment
            if ($request->has('deleted_attachments')) {
                $deletedAttachmentIds = $request->input('deleted_attachments');
                foreach ($deletedAttachmentIds as $attachmentId) {
                    $attachment = EvaluationAttachment::find($attachmentId);
                    if ($attachment) {
                        $attachmentName = $attachment->name;
                        if ($attachment->evaluations()->count() <= 1) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
                        }
                        $evaluation->attachments()->detach($attachmentId);
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => $evaluation->status,
                            'description' => 'Deleted attachment "' . $attachmentName . '".',
                        ]);
                    }
                }
            }

            if ($request->has('existing_attachment_names')) {
                foreach ($request->input('existing_attachment_names') as $attachmentId => $newName) {
                    $attachment = EvaluationAttachment::find($attachmentId);
                    if ($attachment && $attachment->name !== $newName) {
                        $oldName = $attachment->name;
                        $attachment->update(['name' => $newName]);
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => $evaluation->status,
                            'description' => 'Updated attachment name from "' . $oldName . '" to "' . $newName . '".',
                        ]);
                    }
                }
            }

            if ($request->hasFile('new_attachments')) {
                $newAttachmentNames = $request->input('new_attachment_names') ?? [];
                $newFiles = $request->file('new_attachments');
                foreach ($newFiles as $key => $file) {
                    if ($file && $file->isValid()) {
                        $attachmentName = $newAttachmentNames[$key] ?? '';
                        if (!empty($attachmentName)) {
                            $path = $file->store('evaluation/attachment', 'public');
                            $attachment = EvaluationAttachment::create([
                                'name' => $attachmentName,
                                'file_path' => $path,
                                'mime_type' => $file->getClientMimeType(),
                                'file_size' => $file->getSize(),
                            ]);
                            $evaluation->attachments()->attach($attachment->id);
                            EvaluationHistory::create([
                                'evaluation_id' => $evaluation->id,
                                'user_id' => $user->id,
                                'ip_address' => $request->ip(),
                                'action' => $evaluation->status,
                                'description' => 'Added new attachment "' . $attachmentName . '".',
                            ]);
                        }
                    }
                }
            }
            // End attachment

            EvaluationHistory::create([
                'evaluation_id' => $evaluation->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $evaluation->status,
                'description' => $logDescription,
            ]);

            $log = '';
            $message = '';
            $tab = '';
            if ($actionType === 'submit') {
                $message = 'Evaluation Submitted successfully!';
                if ($role == 'drafter') {
                    $log = "Submitted for Evaluation {$evaluation->release_id} [{$role}]";
                    $tab = 'tab_process';
                } else {
                    $log = "Approved for Evaluation {$evaluation->release_id} with status ({$evaluation->status})[{$role}]";
                    $tab = 'tab_done';
                }
            } else {
                $log = "Drafted from Approval for Evaluation {$evaluation->release_id} with status ({$evaluation->status})[{$role}]";
                $message = 'Evaluation Drafted successfully!';
                $tab = 'tab_process';
            }

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $logAction,
                'description' => $log,
            ]);

            if ($actionType === 'submit' && $nextEvaluatorId) {
                $nextEvaluatorUser = User::where('employee_id', $nextEvaluatorId)->first();
                if ($nextEvaluatorUser && !empty($nextEvaluatorUser->email)) {
                    $actionURL = $nextEvaluatorUser->hasPermissionTo('emp.menu')
                        ? route('evaluation.emp.index')
                        : route('profile.evaluation');
                    $details = [
                        'greeting' => 'Hi ' . optional($nextEvaluatorUser->employee)->fullname ?? ucfirst($nextRole),
                        'subject' => 'Evaluation Notification',
                        'body' => 'We would like to inform you that an evaluation for "' . optional($evaluation->employee)->fullname . '" require your attention.',
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $nextEvaluatorUser->notify(new EvaluationNotification($details));
                }
            }
            DB::commit();
            $redirectRoute = ($user->hasPermissionTo('emp.menu')) ? 'evaluation.emp.index' : 'profile.evaluation';
            return redirect()->route($redirectRoute)
                ->with('status', $message)
                ->with($tab, true);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('swal_error', 'Failed to update evaluation: ' . $e->getMessage());
        }
    }

    public function revice(Request $request, $token)
    {
        try {
            [$evaluationId, $role] = explode('|', decrypt($token));
            $user = auth()->user();
            $evaluation = Evaluation::with([
                'approval1.position',
                'approval2.position',
                'approval3.position',
                'approval4.position',
                'approval5.position',
                'approval6.position',
                'drafter.position'
            ])->findOrFail($evaluationId);

            $evaluatorName = 'N/A';
            $evaluatorPosition = 'N/A';
            $evaluatorRole = 'Unknown Role';
            if ($role === 'drafter') {
                $evaluator = $evaluation->drafter;
                $evaluatorName = optional($evaluator)->fullname ?? 'N/A';
                $evaluatorPosition = optional($evaluator->position)->nama ?? 'N/A';
                $evaluatorRole = 'Drafter';
            } else {
                $approvalFields = [
                    'approval1' => ['id' => 'approval1_id', 'as' => 'approval1_as', 'relation' => 'approval1'],
                    'approval2' => ['id' => 'approval2_id', 'as' => 'approval2_as', 'relation' => 'approval2'],
                    'approval3' => ['id' => 'approval3_id', 'as' => 'approval3_as', 'relation' => 'approval3'],
                    'approval4' => ['id' => 'approval4_id', 'as' => 'approval4_as', 'relation' => 'approval4'],
                    'approval5' => ['id' => 'approval5_id', 'as' => 'approval5_as', 'relation' => 'approval5'],
                    'approval6' => ['id' => 'approval6_id', 'as' => 'approval6_as', 'relation' => 'approval6'],
                ];
                if (isset($approvalFields[$role])) {
                    $currentApproval = $approvalFields[$role];
                    $evaluator = $evaluation->{$currentApproval['relation']};
                    $evaluatorName = optional($evaluator)->fullname ?? 'N/A';
                    $evaluatorPosition = optional($evaluator->position)->nama ?? 'N/A';
                    $evaluatorRole = $evaluation->{$currentApproval['as']} ?? 'Unknown Role';
                }
            }

            $evaluation->status = 'REVISE';
            $evaluation->drafter_date = null;
            $evaluation->approval1_date = null;
            $evaluation->approval2_date = null;
            $evaluation->approval3_date = null;
            $evaluation->approval4_date = null;
            $evaluation->approval5_date = null;
            $evaluation->approval6_date = null;
            $evaluation->save();
            EvaluationHistory::create([
                'evaluation_id' => $evaluation->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'REVISE',
                'description' => "Evaluation Revised by {$evaluatorName} ({$evaluatorRole}) [{$evaluatorPosition}] with reason: " . $request->input('revice_reason'),
            ]);
            $logDescription = "Evaluation Revised by ({$evaluatorRole}) for Evaluation {$evaluation->release_id} with reason: " . $request->input('revice_reason');
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'revised',
                'description' => $logDescription,
            ]);
            $redirectRoute = $user->hasPermissionTo('emp.menu') ? 'evaluation.emp.index' : 'profile.evaluation';
            return redirect()->route($redirectRoute)
                ->with('status', 'Evaluation successfully revice!');
        } catch (DecryptException $e) {
            return back()->with('error', 'Failed to revice evaluation: Invalid token.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to revice evaluation: ' . $e->getMessage());
        }
    }

    public function approveMultiple(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $loggedInEmployeeId = Auth::user()->employee_id;

            $approvedCount = 0;
            $skippedCount = 0;
            $approvedNames = [];
            $failedNames = [];
            $nextEvaluators = collect();

            $evaluationIds = $request->input('ids');

            if (empty($evaluationIds)) {
                return back()->with('error', 'No evaluations were selected.');
            }

            $approvalSteps = [
                'approval1' => ['id_field' => 'approval1_id', 'date_field' => 'approval1_date', 'as_field' => 'approval1_as'],
                'approval2' => ['id_field' => 'approval2_id', 'date_field' => 'approval2_date', 'as_field' => 'approval2_as'],
                'approval3' => ['id_field' => 'approval3_id', 'date_field' => 'approval3_date', 'as_field' => 'approval3_as'],
                'approval4' => ['id_field' => 'approval4_id', 'date_field' => 'approval4_date', 'as_field' => 'approval4_as'],
                'approval5' => ['id_field' => 'approval5_id', 'date_field' => 'approval5_date', 'as_field' => 'approval5_as'],
                'approval6' => ['id_field' => 'approval6_id', 'date_field' => 'approval6_date', 'as_field' => 'approval6_as'],
            ];

            $evaluations = Evaluation::with(['employee', 'approval1', 'approval2', 'approval3', 'approval4', 'approval5', 'approval6'])
                ->whereIn('id', array_map('decrypt', $evaluationIds))
                ->get();

            $statusMap = [
                '1st Evaluator' => '1st Evaluator',
                '2nd Evaluator' => '2nd Evaluator',
                '3rd Evaluator' => '3rd Evaluator',
                'HRD Approval' => 'HRD Approved',
                'Director' => 'Prodir',
                'President Director' => 'Presdir',
            ];

            foreach ($evaluations as $evaluation) {
                $releaseId = optional($evaluation)->release_id ?? 'N/A';
                $skipReason = '';
                $currentRole = null;
                $currentStep = null;

                foreach ($approvalSteps as $stepName => $stepData) {
                    if ($loggedInEmployeeId == $evaluation->{$stepData['id_field']} && is_null($evaluation->{$stepData['date_field']})) {
                        $currentRole = $stepName;
                        $currentStep = $stepData;
                        break;
                    }
                }

                if (is_null($currentRole)) {
                    $skippedCount++;
                    $failedNames[] = "{$releaseId} (Not the designated approver or already approved.)";
                    continue;
                }

                $isPreviousStepCompleted = true;
                foreach ($approvalSteps as $stepRole => $step) {
                    if ($stepRole === $currentRole) break;
                    if (!is_null($evaluation->{$step['id_field']}) && is_null($evaluation->{$step['date_field']})) {
                        $isPreviousStepCompleted = false;
                        $skipReason = 'Previous approval step (' . $stepRole . ') is not yet completed.';
                        break;
                    }
                }

                if ($isPreviousStepCompleted) {
                    $currentApprovalAs = $evaluation->{$currentStep['as_field']};
                    $newStatus = $statusMap[$currentApprovalAs] ?? 'Unknown Status';
                    $evaluation->status = $newStatus;

                    $evaluation->{$currentStep['date_field']} = now();

                    $isLastStep = true;
                    $foundCurrent = false;
                    foreach ($approvalSteps as $stepRole => $step) {
                        if ($stepRole === $currentRole) {
                            $foundCurrent = true;
                            continue;
                        }
                        if ($foundCurrent && !empty($evaluation->{$step['id_field']})) {
                            $isLastStep = false;
                            $nextEvaluators->push($evaluation->{$step['id_field']});
                            break;
                        }
                    }

                    if ($isLastStep) {
                        $evaluation->status = 'DONE';
                        if ($evaluation->employee && $evaluation->employee->enddate && $evaluation->month_extend > 0) {
                            $employeeEnddate = Carbon::parse($evaluation->employee->enddate);
                            $monthsToExtend = (int) $evaluation->month_extend;
                            $newContractStart = $employeeEnddate->copy()->addDay();
                            $newEnddate = $newContractStart->addMonths($monthsToExtend)->subDay();
                            $evaluation->date_extend = $newEnddate;
                            $evaluation->save();
                            if (!empty($evaluation->date_extend) && $evaluation->employee->status == 'CONTRACT') {
                                $employeeModel = $evaluation->employee;
                                $currentEmpEnd = Carbon::parse($employeeModel->enddate)->format('Y-m-d');
                                $evalEndTarget = Carbon::parse($evaluation->eval_end)->format('Y-m-d');
                                if ($currentEmpEnd === $evalEndTarget) {
                                    $employeeModel->enddate = $evaluation->date_extend;
                                    $employeeModel->save();
                                }
                            }
                        }
                    }

                    $evaluation->save();

                    $logDescription = "Approved for Evaluation {$releaseId} with status ({$evaluation->status})[{$currentRole}]";
                    EvaluationHistory::create([
                        'evaluation_id' => $evaluation->id,
                        'user_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'action' => $evaluation->status,
                        'description' => $logDescription,
                    ]);

                    Log::create([
                        'user_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'action' => 'approved',
                        'description' => $logDescription,
                    ]);

                    $approvedCount++;
                    $approvedNames[] = optional($evaluation)->release_id ?? 'N/A';
                } else {
                    $skippedCount++;
                    $failedNames[] = optional($evaluation)->release_id ?? 'N/A';
                }
            }

            // Email Notif
            $uniqueNextEvaluatorIds = $nextEvaluators->unique();
            foreach ($uniqueNextEvaluatorIds as $evaluatorId) {
                $nextEvaluatorUser = User::where('employee_id', $evaluatorId)->first();
                if ($nextEvaluatorUser && !empty($nextEvaluatorUser->email)) {
                    $evaluationsForUser = $evaluations->where($currentStep['id_field'], $loggedInEmployeeId);
                    $evaluationsCount = $evaluationsForUser->count();
                    $bodyMessage = '';
                    $employeeNames = [];
                    $actionURL = $nextEvaluatorUser->hasPermissionTo('emp.menu') ? route('evaluation.emp.index') : route('profile.evaluation');
                    if ($evaluationsCount > 10) {
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations require your attention.';
                    } else {
                        $employeeNames = $evaluationsForUser->map(fn($e) => optional($e->employee)->fullname)->filter()->values()->toArray();
                        $bodyMessage = 'We would like to inform you that ' . $evaluationsCount . ' evaluations for the following employees require your attention:';
                    }
                    $details = [
                        'greeting' => 'Hi ' . optional($nextEvaluatorUser->employee)->fullname ?? 'Evaluator',
                        'subject' => 'Evaluation Notification',
                        'body' => $bodyMessage,
                        'employeeNames' => $employeeNames,
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $nextEvaluatorUser->notify(new EvaluationNotification($details));
                }
            }

            DB::commit();
            $message = "Successfully approved {$approvedCount} evaluation(s).";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} evaluation(s) due to invalid status or authorization: " . implode(', ', $failedNames);
            }
            $redirectRoute = ($user->hasPermissionTo('emp.menu')) ? 'evaluation.emp.index' : 'profile.evaluation';
            return redirect()->route($redirectRoute)->with('status', $message)->with('tab_done', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve evaluations: ' . $e->getMessage());
        }
    }

    public function approveMultiple_token(Request $request)
    {
        try {
            $encryptedIds = $request->input('ids');
            if (empty($encryptedIds)) {
                return response()->json(['message' => 'No IDs provided.'], 400);
            }
            $decryptedIds = array_map(function ($id) {
                return Crypt::decrypt($id);
            }, $encryptedIds);
            $singleEncryptedToken = Crypt::encrypt($decryptedIds);
            return response()->json(['token' => $singleEncryptedToken]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['message' => 'Failed to decrypt one or more IDs.', 'error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate token.', 'error' => $e->getMessage()], 500);
        }
    }

    public function approveMultiple_print($token)
    {
        try {
            $user = auth()->user();
            $evaluationIds = Crypt::decrypt($token);
            if (empty($evaluationIds)) {
                return redirect()->back()->with('error', 'Token is empty or invalid.');
            }
            $evaluations = Evaluation::with([
                'employee',
                'approval1',
                'approval2',
                'approval3',
                'approval4',
                'approval5',
                'approval6',
                'appraisal_position'
            ])
                ->whereIn('id', $evaluationIds)
                ->get();

            $data = ['evaluations' => $evaluations];
            $viewpdf = 'partials.evaluation.profile.view_pdf';

            $pdf = PDF::loadView($viewpdf, $data)
                ->setPaper('a4', 'landscape')
                ->setOption('is_svg_enabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true);

            return $pdf->stream('Evaluations_Approve_List.pdf');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid token provided.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to print document: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        try {
            $eval_id = decrypt($id);
            $evaluation = Evaluation::findOrFail($eval_id);
            $evaluation->load([
                'employee',
                'approval1',
                'approval2',
                'approval3',
                'approval4',
                'approval5',
                'approval6',
                'appraisal_position'
            ]);
            $data = ['evaluation' => $evaluation];
            $pdf = Pdf::loadView('pages.hrd.evaluation.print', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('is_svg_enabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true);
            return $pdf->stream(($evaluation->release_id ?? 'Evaluation') . ' - ' . ($evaluation->employee->fullname ?? 'Employee') . '.pdf');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid ID.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Evaluation record not found.');
        }
    }

    public function getEvalHistory(Request $request)
    {
        $employeeId = $request->input('employee');
        $evaluations = collect([]);
        if ($employeeId) {
            $evaluations = Evaluation::where('employee_id', $employeeId)
                ->where('status', 'DONE')
                ->get()
                ->map(function ($item) {
                    $start = optional($item->eval_start)->format('d M Y') ?? '-';
                    $end = optional($item->eval_end)->format('d M Y') ?? '-';
                    $item->period = "{$start} - {$end}";
                    $btn = '-';
                    if (Auth::user()->can('hrd.evaluation.read')) {
                        $encryptedId = encrypt($item->id);
                        if ($item->status === 'DONE') {
                            $btn = '<a href="' . route('evaluation.done.print', ['evaluation' => $encryptedId]) . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>';
                        }
                    }
                    $item->action = $btn;
                    return $item;
                });
        }
        return response()->json(['data' => $evaluations]);
    }

    public function qr_code_approval($token)
    {
        try {
            [$id, $role] = explode('|', decrypt($token));
        } catch (\Exception $e) {
            abort(404, 'Invalid evaluation approval data link.');
        }
        $evaluation = Evaluation::findOrFail($id);
        $evalId = $evaluation->release_id;
        $approvalName = optional($evaluation->{$role})->fullname;
        $approvalAs   = $evaluation->{$role . '_as'};
        $approvalOn   = $evaluation->{$role . '_date'};
        return view('pages.hrd.evaluation.codeqr-approval', compact('evalId', 'approvalName', 'approvalAs', 'approvalOn'));
    }

    public function getEvaluationSteps($id)
    {
        try {
            $decryptedId = decrypt($id);
            $evaluation = Evaluation::find($decryptedId);
            if (!$evaluation) {
                return response()->json(['error' => 'Evaluation not found.'], 404);
            }
            $steps = [];
            $steps[] = [
                'name' => 'HRD',
                'approval' => '',
                'date' => $evaluation->release_date ? Carbon::parse($evaluation->release_date)->format('d M Y, H:i') : null,
                'completed' => $evaluation->release_id != null && $evaluation->release_date != null,
            ];
            for ($i = 1; $i <= 6; $i++) {
                $approvalIdKey = "approval{$i}_id";
                $approvalRelKey = "approval{$i}";
                $approvalAsKey = "approval{$i}_as";
                $approvalDateKey = "approval{$i}_date";
                $approvalReasonKey = "approval{$i}_reason";
                if ($evaluation->$approvalIdKey !== null) {
                    $isCompleted = $evaluation->$approvalDateKey != null;
                    $formattedDate = null;
                    if ($evaluation->$approvalDateKey) {
                        $formattedDate = Carbon::parse($evaluation->$approvalDateKey)->format('d M Y, H:i');
                        if (!empty($evaluation->$approvalReasonKey)) {
                            $formattedDate .= " (" . $evaluation->$approvalReasonKey . ")";
                        }
                    }
                    $steps[] = [
                        'name' => $evaluation->$approvalAsKey,
                        'approval' => ' by ' . ($evaluation->$approvalRelKey->fullname ?? ''),
                        'date' => $formattedDate,
                        'completed' => $isCompleted,
                    ];
                }
            }
            return response()->json(['steps' => $steps]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    public function detailAttachStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $evaluation = Evaluation::findOrFail($id);
            $user = auth()->user();
            if ($request->has('deleted_attachments')) {
                $deletedAttachmentIds = $request->input('deleted_attachments');
                foreach ($deletedAttachmentIds as $attachmentId) {
                    $attachment = EvaluationAttachment::find($attachmentId);
                    if ($attachment) {
                        $attachmentName = $attachment->name;
                        if ($attachment->evaluations()->count() <= 1) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
                        }
                        $evaluation->attachments()->detach($attachmentId);
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => $evaluation->status,
                            'description' => 'Deleted attachment "' . $attachmentName . '".',
                        ]);
                    }
                }
            }
            if ($request->has('existing_attachment_names')) {
                foreach ($request->input('existing_attachment_names') as $attachmentId => $newName) {
                    $attachment = EvaluationAttachment::find($attachmentId);
                    if ($attachment && $attachment->name !== $newName) {
                        $oldName = $attachment->name;
                        $attachment->update(['name' => $newName]);
                        EvaluationHistory::create([
                            'evaluation_id' => $evaluation->id,
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => $evaluation->status,
                            'description' => 'Updated attachment name from "' . $oldName . '" to "' . $newName . '".',
                        ]);
                    }
                }
            }

            if ($request->hasFile('new_attachments')) {
                $newAttachmentNames = $request->input('new_attachment_names') ?? [];
                foreach ($request->file('new_attachments') as $key => $file) {
                    $path = $file->store('evaluation/attachment', 'public');
                    $attachmentName = $newAttachmentNames[$key] ?? 'No Name';
                    $attachment = EvaluationAttachment::create([
                        'name' => $attachmentName,
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                    $evaluation->attachments()->attach($attachment->id);
                    EvaluationHistory::create([
                        'evaluation_id' => $evaluation->id,
                        'user_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'action' => $evaluation->status,
                        'description' => 'Added new attachment "' . $attachmentName . '".',
                    ]);
                }
            }

            if (!empty($evaluation->release_id)) {
                $logMessage = 'Modified Attachment for Evaluation ' . $evaluation->release_id . ' with status (' . ($evaluation->status ?? 'N/A') . ')';
            } else {
                $logMessage = 'Modified Attachment for Evaluation ' . ($evaluation->employee->fullname ?? 'N/A') . ' (' . ($evaluation->employee->nik ?? 'N/A') . ') with status (' . ($evaluation->status ?? 'N/A') . ')';
            }

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => $logMessage,
            ]);
            DB::commit();
            return response()->json([
                'message' => "Attachment has been saved.",
                'redirect' => route('evaluation.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function detailNotesHRDStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer|exists:evaluations,id',
                'note_hrd' => 'nullable|string|max:100',
            ]);
            $evaluation = Evaluation::findOrFail($validatedData['id']);
            $user = auth()->user();
            $oldNoteHrd = $evaluation->note_hrd;
            $newNoteHrd = trim($validatedData['note_hrd']);
            if ($oldNoteHrd !== $newNoteHrd) {
                $evaluation->note_hrd = $newNoteHrd;
                $evaluation->save();
                $baseDescription = "Update Note HRD. Changes: \"{$oldNoteHrd}\" -> \"{$newNoteHrd}\"";
                EvaluationHistory::create([
                    'evaluation_id' => $evaluation->id,
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => $evaluation->status,
                    'description' => $baseDescription,
                ]);
                $changeDescription = $baseDescription;
                $employeeDetail = " for Employee {$evaluation->employee->fullname} ({$evaluation->employee->nik})";
                $changeDescription .= $employeeDetail;
                if (!empty($evaluation->release_id)) {
                    $changeDescription .= " (Release ID: {$evaluation->release_id})";
                }
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'update',
                    'description' => $changeDescription,
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => "Note HRD has been saved.",
                'redirect' => route('evaluation.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function editMultiple($token)
    {
        try {
            $user = auth()->user();
            $evaluationIds = Crypt::decrypt($token);

            if (empty($evaluationIds)) {
                return redirect()->back()->with('error', 'Token is empty or invalid.');
            }

            $evaluations = Evaluation::with(['employee.department', 'attachments'])
                ->whereIn('id', $evaluationIds)
                ->get();

            if ($evaluations->isEmpty()) {
                return redirect()->back()->with('error', 'No evaluations found for the provided token.');
            }

            $noteHrd = '';
            $attachments = collect();

            $firstEvaluation = $evaluations->first();
            $firstNoteHrd = $firstEvaluation->note_hrd ?? '';

            $allNotesAreSame = $evaluations->every(fn($eval) => ($eval->note_hrd ?? '') === $firstNoteHrd);

            if ($allNotesAreSame) {
                $noteHrd = $firstNoteHrd;
            }

            if ($evaluations->count() > 0) {
                $commonAttachmentIds = $evaluations->first()->attachments->pluck('id');
                $evaluations->slice(1)->each(function ($eval) use (&$commonAttachmentIds) {
                    $currentAttachmentIds = $eval->attachments->pluck('id');
                    $commonAttachmentIds = $commonAttachmentIds->intersect($currentAttachmentIds);
                });
                $attachments = EvaluationAttachment::whereIn('id', $commonAttachmentIds)->get();
            }

            $formattedEvaluations = $evaluations->map(function ($eval) {
                return [
                    'id' => $eval->id,
                    'release_id' => $eval->release_id ?? '-',
                    'fullname' => $eval->employee->fullname ?? '-',
                    'department' => $eval->employee->department->name ?? '-',
                    'period' => Carbon::parse($eval->start_period)->format('d M Y') . ' - ' . Carbon::parse($eval->end_period)->format('d M Y'),
                    'purpose' => $eval->purpose ?? '-',
                    'status' => $eval->status,
                    'action' => '',
                ];
            })->toArray();

            return view('pages.hrd.evaluation.edit-multiple', compact('user', 'formattedEvaluations', 'noteHrd', 'attachments'));
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid token provided.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load document: ' . $e->getMessage());
        }
    }

    public function editMultiple_token(Request $request)
    {
        try {
            $encryptedIds = $request->input('ids');
            if (empty($encryptedIds)) {
                return response()->json(['message' => 'No IDs provided.'], 400);
            }
            $decryptedIds = array_map(function ($id) {
                return Crypt::decrypt($id);
            }, $encryptedIds);
            $singleEncryptedToken = Crypt::encrypt($decryptedIds);
            return response()->json(['token' => $singleEncryptedToken]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['message' => 'Failed to decrypt one or more IDs.', 'error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate token.', 'error' => $e->getMessage()], 500);
        }
    }

    public function editMultiple_store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'evaluation_ids' => 'required|json',
                'note_hrd' => 'nullable|string|max:100',
                'existing_attachment_names.*' => 'nullable|string',
                'new_attachment_names.*' => 'nullable|string',
                'new_attachments.*' => 'nullable|file',
                'deleted_attachments.*' => 'nullable|integer',
            ]);

            $evaluationIds = json_decode($validatedData['evaluation_ids'], true);
            $noteHrd = trim($validatedData['note_hrd'] ?? '');
            $user = auth()->user();
            $ipAddress = $request->ip();
            $totalEvaluationsUpdated = 0;

            $attachmentsToDeletePhysically = collect();
            $deletedAttachmentIds = collect($request->input('deleted_attachments', []));

            if ($deletedAttachmentIds->isNotEmpty()) {
                $attachmentsToDelete = EvaluationAttachment::whereIn('id', $deletedAttachmentIds)->get();
                foreach ($attachmentsToDelete as $attachment) {
                    if ($attachment->evaluations()->whereNotIn('evaluations.id', $evaluationIds)->doesntExist()) {
                        $attachmentsToDeletePhysically->push($attachment);
                    }
                }
            }

            $newAttachmentIds = collect();
            if ($request->hasFile('new_attachments')) {
                $newAttachmentNames = $request->input('new_attachment_names') ?? [];
                foreach ($request->file('new_attachments') as $key => $file) {
                    $path = $file->store('evaluation/attachment', 'public');
                    $attachmentName = $newAttachmentNames[$key] ?? 'No Name';
                    $newAttachment = EvaluationAttachment::create([
                        'name' => $attachmentName,
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                    $newAttachmentIds->push($newAttachment->id);
                }
            }

            foreach ($evaluationIds as $id) {
                $evaluation = Evaluation::with('employee', 'attachments')->findOrFail($id);
                $isUpdated = false;

                $initialAttachmentIds = $evaluation->attachments->pluck('id');
                $initialAttachmentNames = $evaluation->attachments->pluck('name', 'id');

                if (($evaluation->note_hrd ?? '') !== $noteHrd) {
                    $oldNote = $evaluation->note_hrd;
                    $evaluation->note_hrd = $noteHrd;
                    $evaluation->save();
                    EvaluationHistory::create([
                        'evaluation_id' => $evaluation->id,
                        'user_id' => $user->id,
                        'ip_address' => $ipAddress,
                        'action' => $evaluation->status,
                        'description' => "Updated HRD note from '" . substr($oldNote, 0, 20) . "' to '" . substr($noteHrd, 0, 20) . "'.",
                    ]);
                    $isUpdated = true;
                }

                $attachmentsToDetach = $initialAttachmentIds->intersect($deletedAttachmentIds);
                if ($attachmentsToDetach->isNotEmpty()) {
                    $evaluation->attachments()->detach($attachmentsToDetach);
                    foreach ($attachmentsToDetach as $attachId) {
                        $attachment = EvaluationAttachment::find($attachId);
                        if ($attachment) {
                            EvaluationHistory::create([
                                'evaluation_id' => $evaluation->id,
                                'user_id' => $user->id,
                                'ip_address' => $ipAddress,
                                'action' => $evaluation->status,
                                'description' => 'Deleted attachment "' . $attachment->name . '".',
                            ]);
                        }
                    }
                    $isUpdated = true;
                }

                if ($newAttachmentIds->isNotEmpty()) {
                    $evaluation->attachments()->attach($newAttachmentIds);
                    foreach ($newAttachmentIds as $newId) {
                        $attachment = EvaluationAttachment::find($newId);
                        if ($attachment) {
                            EvaluationHistory::create([
                                'evaluation_id' => $evaluation->id,
                                'user_id' => $user->id,
                                'ip_address' => $ipAddress,
                                'action' => $evaluation->status,
                                'description' => 'Added new attachment "' . $attachment->name . '".',
                            ]);
                        }
                    }
                    $isUpdated = true;
                }

                if ($request->has('existing_attachment_names')) {
                    foreach ($request->input('existing_attachment_names') as $attachmentId => $newName) {
                        if ($initialAttachmentNames->has($attachmentId) && $initialAttachmentNames[$attachmentId] !== $newName) {
                            $attachment = EvaluationAttachment::find($attachmentId);
                            if ($attachment) {
                                $oldName = $initialAttachmentNames[$attachmentId];
                                $attachment->update(['name' => $newName]);
                                EvaluationHistory::create([
                                    'evaluation_id' => $evaluation->id,
                                    'user_id' => $user->id,
                                    'ip_address' => $ipAddress,
                                    'action' => $evaluation->status,
                                    'description' => 'Updated attachment name from "' . $oldName . '" to "' . $newName . '".',
                                ]);
                                $isUpdated = true;
                            }
                        }
                    }
                }

                if ($isUpdated) {
                    if (!empty($evaluation->release_id)) {
                        $logMessage = 'Modified Note HRD and Attachment for ' . $evaluation->release_id . ' with status (' . ($evaluation->status ?? 'N/A') . ')';
                    } else {
                        $logMessage = 'Modified Note HRD and Attachment for ' . ($evaluation->employee->fullname ?? 'N/A') . ' (' . ($evaluation->employee->nik ?? 'N/A') . ') with status (' . ($evaluation->status ?? 'N/A') . ')';
                    }
                    Log::create([
                        'user_id' => $user->id,
                        'ip_address' => $ipAddress,
                        'action' => 'update',
                        'description' => $logMessage,
                    ]);
                    $totalEvaluationsUpdated++;
                }
            }

            foreach ($attachmentsToDeletePhysically as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            DB::commit();
            return response()->json([
                'message' => "Successfully updated " . $totalEvaluationsUpdated . " evaluations.",
                'redirect' => route('evaluation.index')
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update evaluations: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function getDecisionReason($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $evaluation = Evaluation::find($id);
            if (!$evaluation) {
                return response()->json(['message' => 'Data not found.'], 404);
            }
            return response()->json([
                'reason' => $evaluation->decision_reason
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['message' => 'Invalid ID.'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function resume_token(Request $request)
    {
        try {
            $encryptedIds = $request->input('ids');
            if (empty($encryptedIds)) {
                return response()->json(['message' => 'No IDs provided.'], 400);
            }
            $decryptedIds = array_map(function ($id) {
                return Crypt::decrypt($id);
            }, $encryptedIds);
            $singleEncryptedToken = Crypt::encrypt($decryptedIds);
            return response()->json(['token' => $singleEncryptedToken]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['message' => 'Failed to decrypt one or more IDs.', 'error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate token.', 'error' => $e->getMessage()], 500);
        }
    }

    public function resume_print($token)
    {
        try {
            $user = auth()->user();
            $evaluationIds = Crypt::decrypt($token);
            if (empty($evaluationIds)) {
                return redirect()->back()->with('error', 'Token is empty or invalid.');
            }
            $evaluations = Evaluation::with([
                'employee',
                'approval1',
                'approval2',
                'approval3',
                'approval4',
                'approval5',
                'approval6',
                'appraisal_position'
            ])
                ->whereIn('id', $evaluationIds)
                ->get();

            $data = ['evaluations' => $evaluations];
            $viewpdf = 'pages.hrd.evaluation.resume_pdf';

            $pdf = PDF::loadView($viewpdf, $data)
                ->setPaper('a4', 'landscape')
                ->setOption('is_svg_enabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true);

            return $pdf->stream('Evaluations_Resume.pdf');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid token provided.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to print document: ' . $e->getMessage());
        }
    }

    public function index_schedule(Request $request)
    {
        $departments = Department::all();
        $buildings = Building::all();
        if ($request->ajax()) {
            $today = Carbon::now()->toDateString();
            $sixtyDaysFromNow = Carbon::now()->addDays(60)->toDateString();
            $allEvaluatedIds = Evaluation::query()
                ->join('employees', 'employees.id', '=', 'evaluations.employee_id')
                ->whereColumn('eval_end', 'employees.enddate')
                ->where('purpose', 'Employment Status')
                ->pluck('employee_id')->toArray();
            $draftEmployeeIds = Evaluation::query()
                ->join('employees', 'employees.id', '=', 'evaluations.employee_id')
                ->whereColumn('eval_end', 'employees.enddate')
                ->where('purpose', 'Employment Status')
                ->whereNull('release_date')
                ->pluck('employee_id')->toArray();
            $releasedEmployeeIds = array_diff($allEvaluatedIds, $draftEmployeeIds);
            $query = Employee::with(['position', 'department', 'area', 'section', 'building'])
                ->whereIn('employees.status', ['CONTRACT', 'PROBATION'])
                ->whereNotNull('employees.enddate')
                ->whereDate('employees.enddate', '>=', $today)
                ->whereDate('employees.enddate', '<=', $sixtyDaysFromNow)
                ->whereNotIn('id', $releasedEmployeeIds);
            if ($request->filled('department_id') && $request->department_id !== 'ALL') {
                $query->where('department_id', $request->department_id);
            }
            if ($request->filled('building_id') && $request->building_id !== 'ALL') {
                $query->where('building_id', $request->building_id);
            }
            $employees = $query->get();
            $employees = $employees->sortBy(function ($employee) {
                if ($employee->enddate) {
                    $endDate = Carbon::parse($employee->enddate)->startOfDay();
                    $now = Carbon::now()->startOfDay();
                    return $now->diffInDays($endDate, false);
                }
                return PHP_INT_MAX; 
            })->values();
            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('is_draft', function ($employee) use ($draftEmployeeIds) {
                    return in_array($employee->id, $draftEmployeeIds) ? true : false;
                })
                ->addColumn('nik', fn($employee) => $employee->nik ?? '-')
                ->addColumn('fullname', fn($employee) => $employee->fullname ?? '-')
                ->addColumn('joindate', function ($employee) {
                    if (!$employee->joindate) return '-';
                    return [
                        'display' => Carbon::parse($employee->joindate)->format('d/m/Y'),
                        'timestamp' => Carbon::parse($employee->joindate)->getTimestamp()
                    ];
                })
                ->addColumn('status', function ($employee) {
                    $badges = [
                        'PERMANENT' => 'success',
                        'CONTRACT'  => 'primary',
                        'PROBATION' => 'warning',
                    ];
                    $color = $badges[$employee->status] ?? 'danger';
                    $label = $employee->status ?? 'TERMINATED';
                    return '<span class="badge text-bg-'.$color.'">'.$label.'</span>';
                })
                ->addColumn('contract_number', function ($employee) {
                    if ($employee->contract) {
                        return $employee->contract->name;
                    }
                    return ($employee->status == 'PROBATION') ? 'PROBATION' : '-';
                })
                ->addColumn('start_date', function ($employee) {
                    $date = $employee->contract_startdate ?? null;
                    if (!$date) return '-';
                    return [
                        'display' => Carbon::parse($date)->format('d/m/Y'),
                        'timestamp' => Carbon::parse($date)->getTimestamp()
                    ];
                })
                ->addColumn('end_date', function ($employee) {
                    $date = $employee->enddate ?? null;
                    if (!$date) return '-';
                    return [
                        'display' => Carbon::parse($date)->format('d/m/Y'),
                        'timestamp' => Carbon::parse($date)->getTimestamp()
                    ];
                })
                ->addColumn('service_year', function ($employee) {
                    if (!$employee->joindate) return '-';
                    $diff = Carbon::parse($employee->joindate)->diff(Carbon::now());
                    return $diff->format('%y Year(s) %m Month(s)');
                })
                ->addColumn('area', fn($employee) => $employee->area->name ?? '-')
                ->addColumn('department', fn($employee) => $employee->department->name ?? '-')
                ->addColumn('section', fn($employee) => $employee->section->nama ?? '-')
                ->addColumn('position', fn($employee) => $employee->position->nama ?? '-')
                ->addColumn('building', fn($employee) => $employee->building->nama ?? '-')
                ->addColumn('remaining', function ($employee) {
                    if (!$employee->enddate) return '';
                    $endDate = Carbon::parse($employee->enddate)->startOfDay();
                    $today = Carbon::now()->startOfDay();
                    $remainingDays = $today->diffInDays($endDate, false);
                    $info = ($remainingDays >= 0) ? "{$remainingDays} DAYS" : "+".abs($remainingDays)." DAYS";
                    return '<span class="badge text-bg-danger">'.$info.'</span>';
                })
                ->rawColumns(['status','remaining'])
                ->make(true);     
        }
        return view('pages.hrd.evaluation.schedule', compact('departments','buildings'));
    }

    public function getYearly(Request $request){
        if ($request->ajax()) {
            $currentYear = Carbon::now()->year;
            $remain = 30;
            $endDate = Carbon::createFromDate($currentYear, 12, 31);
            $startDate = $endDate->copy()->subDays($remain)->startOfDay();
            $fixedEndDateString = $endDate->toDateString();
            $startDateLimit = $startDate->toDateString();
            $isWithinPeriod = Carbon::now()->between($startDate, $endDate->endOfDay());
            $showAlert = !$isWithinPeriod;
            $allEvaluatedIds = Evaluation::query()
                ->join('employees', 'employees.id', '=', 'evaluations.employee_id')
                ->whereDate('evaluations.eval_end', $fixedEndDateString)
                ->where('purpose', 'Yearly Evaluation')
                ->pluck('employee_id')->toArray();
            $draftEmployeeIds = Evaluation::query()
                ->join('employees', 'employees.id', '=', 'evaluations.employee_id')
                ->whereDate('evaluations.eval_end', $fixedEndDateString)
                ->where('purpose', 'Yearly Evaluation')
                ->whereNull('evaluations.release_date')
                ->pluck('employee_id')->toArray();
            $releasedEmployeeIds = array_diff($allEvaluatedIds, $draftEmployeeIds);
            $query = Employee::with(['position', 'department', 'area', 'section', 'building'])
                ->whereIn('employees.status', ['PERMANENT'])
                ->whereRaw("CURDATE() >= ?", [$startDateLimit])
                ->whereNotIn('id', $releasedEmployeeIds);
            if ($request->filled('department_id') && $request->department_id !== 'ALL') {
                $query->where('department_id', $request->department_id);
            }
            if ($request->filled('building_id') && $request->building_id !== 'ALL') {
                $query->where('building_id', $request->building_id);
            }
            $employees = $query->get();
            $dataTableResponse = DataTables::of($employees)
                ->addColumn('is_draft', function ($employee) use ($draftEmployeeIds) {
                    return in_array($employee->id, $draftEmployeeIds) ? true : false;
                })
                ->addColumn('nik', fn($employee) => $employee->nik ?? '-')
                ->addColumn('fullname', fn($employee) => $employee->fullname ?? '-')
                ->addColumn('joindate', function ($employee) {
                    if (!$employee->joindate) return '-';
                    return [
                        'display' => Carbon::parse($employee->joindate)->format('d/m/Y'),
                        'timestamp' => Carbon::parse($employee->joindate)->getTimestamp()
                    ];
                })
                ->addColumn('fullname', fn($employee) => $employee->fullname ?? '-')
                ->addColumn('status', function ($employee) {
                    if ($employee->status == 'PERMANENT') {
                        return '<span class="badge text-bg-success">PERMANENT</span>';
                    } elseif ($employee->status == 'CONTRACT') {
                        return '<span class="badge text-bg-primary">CONTRACT</span>';
                    } elseif ($employee->status == 'PROBATION') {
                        return '<span class="badge text-bg-warning">PROBATION</span>';
                    } else {
                        return '<span class="badge text-bg-danger">TERMINATED</span>';
                    }
                })
                ->addColumn('contract_number', function ($employee) {
                    if ($employee->contract_number) {
                        return 'CONTRACT ' . $employee->contract_number;
                    }
                    return '-';
                })
                ->addColumn('start_date', function () {
                    $date = Carbon::now()->startOfYear();
                    return [
                        'display' => $date->format('d/m/Y'),
                        'timestamp' => $date->getTimestamp()
                    ];
                })
                ->addColumn('end_date', function () use ($endDate) {
                    return [
                        'display' => $endDate->format('d/m/Y'),
                        'timestamp' => $endDate->getTimestamp()
                    ];
                })
                ->addColumn('service_year', function ($employee) {
                    if ($employee->joindate) {
                        $joinDate = Carbon::parse($employee->joindate);
                        $now = Carbon::now();
                        return $joinDate->diff($now)->format('%y Year(s) %m Month(s)');
                    }
                    return '-';
                })
                ->addColumn('area', fn($employee) => $employee->area->name ?? '-')
                ->addColumn('department', fn($employee) => $employee->department->name ?? '-')
                ->addColumn('section', fn($employee) => $employee->section->nama ?? '-')
                ->addColumn('position', fn($employee) => $employee->position->nama ?? '-')
                ->addColumn('building', fn($employee) => $employee->building->nama ?? '-')
                ->addColumn('remaining', function ($employee) use ($fixedEndDateString) {
                    if (!empty($fixedEndDateString)) {
                        $endDate = Carbon::parse($fixedEndDateString)->startOfDay();
                        $today = Carbon::now()->startOfDay();
                        $remainingDays = $today->diffInDays($endDate, false);
                        $info = '';
                        $badgeClass = '';
                        if ($remainingDays >= 0) {
                            $info = "{$remainingDays} DAYS";
                            $badgeClass = 'text-bg-danger';
                        } else {
                            $expiredDays = abs($remainingDays);
                            $info = "+{$expiredDays} DAYS";
                            $badgeClass = 'text-bg-danger';
                        }
                        return "<span class=\"badge {$badgeClass}\">{$info}</span>";
                    }
                    return ''; 
                })
                ->rawColumns(['status','remaining'])
                ->addIndexColumn()
                ->toJson();
            $data = json_decode($dataTableResponse->content(), true);
            $data['yearly_end_date'] = Carbon::parse($fixedEndDateString)->format('d F Y');
            $data['remain'] = $remain;
            $data['show_alert'] = $showAlert;
            return response()->json($data);
        }
    }

    public function validateMultiple_schedule(Request $request)
    {
        $employeeIds = json_decode($request->input('employee_ids'), true);
        if (!$employeeIds || !is_array($employeeIds) || count($employeeIds) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employees selected.'
            ], 422);
        }

        $query = LineApprovalEmployee::whereIn('employee_id', $employeeIds)
            ->whereHas('lineApproval', function ($subQuery) {
                $subQuery->where('approval_type', 'Evaluation');
            })
            ->whereHas('employee', function ($employeeQuery) {
                $employeeQuery->whereExists(function ($appraisalExistsQuery) {
                    $appraisalExistsQuery->from('master_appraisal as appraisals')
                        ->whereColumn('appraisals.position_id', 'employees.position_id')
                        ->whereRaw('LOWER(appraisals.status) = LOWER(employees.status)');
                });
            });
        $lineApprovalEmployees = $query->get();

        if ($lineApprovalEmployees->count() !== count($employeeIds)) {
            $foundEmployeeIds = $lineApprovalEmployees->pluck('employee_id')->toArray();
            $missingIds = array_diff($employeeIds, $foundEmployeeIds);
            $missingEmployees = Employee::whereIn('id', $missingIds)->get();
            $missingDetailsArray = [];
            foreach ($missingEmployees as $emp) {
                $missingTags = [];

                $hasApproval = LineApprovalEmployee::where('employee_id', $emp->id)
                    ->whereHas('lineApproval', function ($q) {
                        $q->where('approval_type', 'Evaluation');
                    })->exists();
                if (!$hasApproval) {
                    $missingTags[] = '<b style="color:#d33;">[X Approval]</b>';
                }

                $hasAppraisal = Appraisal::where('position_id', $emp->position_id)
                    ->whereRaw('LOWER(status) = LOWER(?)', [$emp->status])
                    ->exists();
                    
                if (!$hasAppraisal) {
                    $missingTags[] = '<b style="color:#d33;">[X Appraisal]</b>';
                }

                $tagString = implode(' ', $missingTags);
                $missingDetailsArray[] = "{$emp->fullname} ({$emp->nik}) {$tagString}";
            }
            
            return response()->json([
                'status' => 'error',
                'missing_data' => $missingDetailsArray
            ], 422);
        }
        return response()->json(['status' => 'success'], 200);
    }

    public function createMultiple_schedule(Request $request)
    {
        $employeeIds = json_decode($request->input('employee_ids'), true);
        $purpose = $request->input('purpose');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $query = LineApprovalEmployee::with([
            'employee.position', 'employee.section', 'employee.department',
            'lineApproval.draft.position',
            'lineApproval.draft.user',
            'lineApproval.approve1.position', 'lineApproval.approve1.user',
            'lineApproval.approve2.position', 'lineApproval.approve2.user',
            'lineApproval.approve3.position', 'lineApproval.approve3.user',
            'lineApproval.approve4.position', 'lineApproval.approve4.user',
            'lineApproval.approve5.position', 'lineApproval.approve5.user',
            'lineApproval.approve6.position', 'lineApproval.approve6.user'
        ]);
        $query->whereIn('employee_id', $employeeIds);
        $query->whereHas('lineApproval', function ($subQuery) {
            $subQuery->where('approval_type', 'Evaluation');
        });
        $query->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereExists(function ($appraisalExistsQuery) {
                $appraisalExistsQuery->from('master_appraisal as appraisals')
                    ->whereColumn('appraisals.position_id', 'employees.position_id')
                    ->whereRaw('LOWER(appraisals.status) = LOWER(employees.status)');
            });
        });
        $lineApprovalEmployees = $query->get();
        $employees = $lineApprovalEmployees->map(function ($lineApprovalEmployee) {
            $employee = $lineApprovalEmployee->employee;
            $statusBadge = '';
            if ($employee->status == 'PERMANENT') {
                $statusBadge = '<span class="badge text-bg-success">'.$employee->status.'</span>';
            } elseif ($employee->status == 'PROBATION') {
                $statusBadge = '<span class="badge text-bg-secondary">'.$employee->status.'</span>';
            } elseif ($employee->status == 'CONTRACT') {
                $statusBadge = '<span class="badge text-bg-primary">'.$employee->status.'</span>';
            } elseif ($employee->status == 'OUTSOURCING') {
                $statusBadge = '<span class="badge text-bg-info">'.$employee->status.'</span>';
            }
            $lineApproval = $lineApprovalEmployee->lineApproval;
            if ($lineApproval) {
                for ($i = 1; $i <= 6; $i++) {
                    $approveKey = 'approve' . $i;
                    if (isset($lineApproval->$approveKey) && $lineApproval->$approveKey !== null) {
                        $positionName = $lineApproval->$approveKey->position->nama ?? null;
                        $lineApproval->$approveKey->default_role = Evaluation::getDefaultApprovals($positionName);
                    }
                }
            }
            return [
                'id' => $employee->id,
                'nik' => $employee->nik,
                'fullname' => $employee->fullname,
                'position' => $employee->position->nama ?? '-',
                'section' => $employee->section->nama ?? '-',
                'status' => $statusBadge,
                'action' => '<button type="button" class="btn btn-danger btn-sm remove-employee" data-id="' . $employee->id . '"><i class="ri-delete-bin-line"></i></button>',
                'line_approval' => [
                    'id' => $lineApproval->id ?? null,
                    'drafter' => $lineApproval->draft ?? null,
                    'approval1' => $lineApproval->approve1 ?? null,
                    'approval2' => $lineApproval->approve2 ?? null,
                    'approval3' => $lineApproval->approve3 ?? null,
                    'approval4' => $lineApproval->approve4 ?? null,
                    'approval5' => $lineApproval->approve5 ?? null,
                    'approval6' => $lineApproval->approve6 ?? null,
                ]
            ];
        });
        $schedule_data = $employees->toJson();
        $is_from_schedule = true;
        $departments = Department::all();
        $areas = Area::all();
        $buildings = Building::all();
        $positions = Position::all();
        $sections = Section::all();
        $approveds = Employee::whereNot('status', 'TERMINATED')->get();
        return view('pages.hrd.evaluation.create-multiple', compact(
            'departments', 'areas', 'buildings', 'positions', 'sections', 'approveds',
            'is_from_schedule', 'schedule_data', 'start_date', 'end_date', 'purpose', 'employeeIds'
        ));
    }

    public function process_export_xlsx(Request $request)
    {
        $now = Carbon::now()->format('Ymd_His');
        $fileName = 'Evaluation_Process_' . $now . '.xlsx';
        $formStatus = $request->get('form_status', 'ALL');
        return Excel::download(new EvaluationProcessExport($formStatus), $fileName);
    }

    public function done_export_xlsx(Request $request)
    {
        $now = Carbon::now()->format('Ymd_His');
        $fileName = 'Evaluation_Done_' . $now . '.xlsx';
        $tahun = $request->get('tahun', date('Y'));
        return Excel::download(new EvaluationDoneExport($tahun), $fileName);
    }
}
