<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\Hiring;
use App\Models\Master\LineApprovalEmployee;
use App\Models\Position;
use App\Models\Recruitment\Candidate;
use App\Models\Recruitment\EmployeeRequisition;
use App\Models\Recruitment\EmployeeRequisitionEducation;
use App\Models\Recruitment\EmployeeRequisitionHiringStep;
use App\Models\Recruitment\EmployeeRequisitionRecruitmentSource;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AccountNotification;
use App\Notifications\EvaluationNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class EmployeeRequisitionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tahun = $request->get('tahun');
            $query = EmployeeRequisition::with(['applicant', 'department', 'position'])
                ->where('employee_requisition.status', 'DONE'); 
            if ($tahun) {
                $query->whereYear('employee_requisition.submit_date', $tahun);
            }
            return DataTables::of($query)
                ->addColumn('id', function ($row) {
                    $isDisabled = ($row->decision == 'APPROVED') ? 'disabled' : '';
                    $encryptedId = Crypt::encrypt($row->id); 
                    return '<input type="checkbox" class="row-checkbox" value="'.$encryptedId.'" '.$isDisabled.'>';
                })
                ->addColumn('no_pengajuan', function ($row) {
                    return $row->no_pengajuan ?? '-';
                })
                ->addColumn('applicant', function ($row) {
                    return $row->applicant->fullname ?? '-';
                })
                ->addColumn('position', function ($row) {
                    return $row->position->nama ?? '-';
                })
                ->addColumn('department', function ($row) {
                    return $row->department->name ?? '-';
                })
                ->addColumn('needs', function ($row) {
                    return $row->needs ?? '0';
                })
                ->addColumn('reason', function ($row) {
                    return $row->reason_requisition;
                })
                ->addColumn('decision', function ($row) {
                    $badges = [
                        'APPROVED' => 'success',
                        'PENDING' => 'warning',
                        'DISAPPROVED' => 'danger',
                    ];
                    $decision = $row->decision;
                    $badgeClass = $badges[$decision] ?? 'dark';
                    return "<span class=\"badge text-bg-{$badgeClass}\">{$decision}</span>";
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $encryptedId = Crypt::encrypt($row->id);
                    if (Auth::user()->can('hrd.recruitment.read')) {
                        $btn .= '<a href="' . route('employee-requisition.detail', ['id' => $encryptedId]) . '" title="Detail" class="btn btn-info btn-sm me-1"><i class="ri-eye-2-line"></i></a>';
                        $excluded_statuses = ['DRAFT', 'REVISE', 'REJECT'];
                        if (!in_array($row->status, $excluded_statuses)) {
                            $btn .= '<a href="#" data-id="' . $encryptedId . '" title="Steps" class="btn btn-primary btn-sm me-1 btn-view-steps"><i class="ri-list-check"></i></a>';
                        }
                        if ($row->status === 'DONE') {
                            $btn .= '<a href="' . route('employee-requisition.print', ['er' => $encryptedId]) . '" target="_blank" title="Print" class="btn btn-success btn-sm me-1"><i class="ri-printer-fill"></i></a>';
                        }
                    }
                    if (Auth::user()->can('hrd.employee-requisition.delete') && $row->decision != 'APPROVED') {
                        $btn .= '<button type="button" data-id="' . $encryptedId . '" title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                ->rawColumns(['id', 'decision', 'action'])
                ->make(true);
        }
        $years = EmployeeRequisition::whereNotNull('submit_date')
            ->where('employee_requisition.status', 'DONE')
            ->selectRaw('YEAR(submit_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4); 
        }
        return view('pages.hrd.recruitment.er.index', compact('years'));
    }

    public function emp_index(Request $request)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;
        $hasLineApproval = LineApprovalEmployee::where('employee_id', $employeeId)
            ->whereHas('lineApproval', function ($query) {
                $query->where('approval_type', 'Employee Requisition');
            })
            ->exists();
        $years = EmployeeRequisition::whereNotNull('submit_date')
            ->selectRaw('YEAR(submit_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4);
        }
        return view('pages.employee.recruitment.index', compact('user', 'years', 'hasLineApproval'));
    }

    public function emp_form(string $id = null)
    {
        $user = auth()->user();
        $approvals = [
            'approval1' => null,
            'approval2' => null,
            'approval3' => null,
            'approval4' => null,
        ];

        if ($id) {
            $id = decrypt($id);
            $er = EmployeeRequisition::with(['applicant'])->findOrFail($id);
            if ($er && Auth::user()->can('emp.recruitment.read') && ($er->status != 'DRAFT' && $er->status != 'REVISE') || $er->applicant_id != $user->employee_id) {
                return redirect()->route('recruitment.emp.index')
                    ->with('error', 'Access Denied!');
            }
            $approvals = [
                'approval1' => $er->approval1 ?? null,
                'approval2' => $er->approval2 ?? null,
                'approval3' => $er->approval3 ?? null,
                'approval4' => $er->approval4 ?? null,
            ];
        } else {
            $er = null;
            $employeeId = $user->employee->id;
            $lineApprovalEmployee = LineApprovalEmployee::with([
                'lineApproval.approve1.position',
                'lineApproval.approve1.user',
                'lineApproval.approve2.position',
                'lineApproval.approve2.user',
                'lineApproval.approve3.position',
                'lineApproval.approve3.user',
                'lineApproval.approve4.position',
                'lineApproval.approve4.user',
            ])
                ->where('employee_id', $employeeId)
                ->whereHas('lineApproval', function ($query) {
                    $query->where('approval_type', 'Employee Requisition');
                })
                ->first();

            if (is_null($lineApprovalEmployee)) {
                return redirect()->back()
                    ->with('error', 'Access Denied!');
            }

            if ($lineApprovalEmployee && $lineApprovalEmployee->lineApproval) {
                $lineApproval = $lineApprovalEmployee->lineApproval;
                $approvals = [
                    'approval1' => $lineApproval->approve1 ?? null,
                    'approval2' => $lineApproval->approve2 ?? null,
                    'approval3' => $lineApproval->approve3 ?? null,
                    'approval4' => $lineApproval->approve4 ?? null,
                ];
            }
        }

        $approvals = array_filter($approvals);

        $departments = Department::all();
        $areas = Area::all();
        $positions = Position::all();
        $sections = Section::all();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();

        return view('pages.employee.recruitment.er.form', compact(
            'user',
            'er',
            'departments',
            'areas',
            'positions',
            'sections',
            'employees',
            'approvals'
        ));
    }

    public function store(Request $request)
    {
        $status = $request->input('status');
        $rules = [
            'applicant_id' => 'required|exists:employees,id',
            'id' => 'nullable|exists:employee_requisition,id',

            'needs' => 'nullable|integer|min:1',
            'reason_requisition' => 'nullable|string|max:255',
            'employee_status' => 'nullable|string|max:255',
            'work_experience' => 'nullable|string|max:255',
            'qualification' => 'nullable|string',
            'employment_date' => 'nullable|date_format:d/m/Y',

            'approval1_id' => 'required|exists:employees,id',
            'approval1_as' => 'required|string|max:255',

            'reason_replacement' => 'nullable|string|max:255',
            'reason_replacement_other' => 'nullable|string|max:255',
            'contract_period' => 'nullable|integer|min:1|max:12',
            'duration_work_experience' => 'nullable|integer|min:1',

            'education_names' => 'nullable|array',
            'education_names.*' => 'nullable|string|max:255',
            'major_requirements' => 'nullable|array',

            'gender_select' => 'nullable|array',
            'gender_needs.*' => 'nullable|integer|min:1',
            'gender_start_age.*' => 'nullable|integer|min:1',
            'gender_end_age.*' => 'nullable|integer|min:1',
        ];

        if ($status === 'SUBMIT') {
            $rules = array_merge($rules, [
                'needs' => 'required|integer|min:1',
                'reason_requisition' => 'required|string|max:255',
                'employee_status' => 'required|string|max:255',
                'work_experience' => 'required|string|max:255',
                'qualification' => 'required|string',
                'employment_date' => 'required|date_format:d/m/Y',

                'education_names' => 'required|array|min:1',
                'education_names.*' => 'required|string|max:255',

                'gender_select' => 'required|array|min:1',
                'gender_needs.*' => 'required|integer|min:1',
                'gender_start_age.*' => 'required|integer|min:1',
                'gender_end_age.*' => 'required|integer|min:1',
            ]);

            if ($request->input('employee_status') === 'Kontrak / Contract') {
                $rules['contract_period'] = 'required|integer|min:1|max:12';
            }
            if ($request->input('work_experience') === 'Dibutuhkan / Required') {
                $rules['duration_work_experience'] = 'required|integer|min:1';
            }
            if ($request->input('reason_requisition') === 'Penggantian / Replacement') {
                $rules['person_replaced_id'] = 'required|exists:employees,id';
                $rules['reason_replacement'] = 'required|string|max:255';
                if ($request->input('reason_replacement') === 'Lainnya / Others') {
                    $rules['reason_replacement_other'] = 'required|string|max:255';
                }
            }

            $totalNeeds = (int) $request->input('needs', 0);
            $totalGenderCount = array_sum(array_map('intval', $request->input('gender_needs', [])));

            if ($totalGenderCount !== $totalNeeds) {
                return response()->json([
                    'message' => 'Jumlah Kebutuhan Tidak Sama (Total Mismatch) [' . $totalNeeds . ']',
                    'errors' => [],
                ], 422);
            }
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $redirectRoute = $user->hasPermissionTo('emp.menu')
            ? route('recruitment.emp.index')
            : route('recruitment.profile.index');

        DB::beginTransaction();
        try {
            $erData = $request->except([
                '_token',
                'id',
                'reason_replacement_other',
                'education_names',
                'major_requirements',
                'gender_select',
                'gender_needs',
                'gender_start_age',
                'gender_end_age'
            ]);

            $statusFromRequest = $request->input('status');
            if ($statusFromRequest === 'SUBMIT') {
                $erData['status'] = 'PROPOSE';
            } else {
                $erData['status'] = $statusFromRequest;
            }

            if (isset($erData['employment_date'])) {
                $erData['employment_date'] = Carbon::createFromFormat('d/m/Y', $erData['employment_date'])->toDateString();
            }

            if (isset($erData['reason_replacement']) && $erData['reason_replacement'] === 'Lainnya / Others') {
                $erData['reason_replacement_other'] = $request->input('reason_replacement_other');
            } else {
                $erData['reason_replacement_other'] = null;
            }

            $er = EmployeeRequisition::updateOrCreate(
                ['id' => $request->input('id') ?? null],
                $erData
            );
            $wasRecentlyCreated = $er->wasRecentlyCreated;

            $educationNames = $request->input('education_names', []);
            $majorRequirements = $request->input('major_requirements', []);
            $syncEducationData = [];
            foreach ($educationNames as $eduName) {
                $educationModel = EmployeeRequisitionEducation::firstOrCreate(['name' => $eduName]);
                $educationId = $educationModel->id;

                $majorOrOtherDescription = $majorRequirements[$eduName] ?? null;

                $syncEducationData[$educationId] = ['major' => $majorOrOtherDescription];
            }
            $er->educationalRequirements()->sync($syncEducationData);

            $genderSelections = $request->input('gender_select', []);
            $genderNeeds = $request->input('gender_needs', []);
            $genderStartAge = $request->input('gender_start_age', []);
            $genderEndAge = $request->input('gender_end_age', []);

            $er->genderRequirements()->delete();

            foreach ($genderSelections as $genderName => $value) {
                $needsCount = $genderNeeds[$genderName] ?? null;
                $startAge = $genderStartAge[$genderName] ?? null;
                $endAge = $genderEndAge[$genderName] ?? null;

                if ($needsCount && $startAge && $endAge) {
                    $er->genderRequirements()->create([
                        'gender_name' => $genderName,
                        'needs_count' => $needsCount,
                        'start_age' => $startAge,
                        'end_age' => $endAge,
                    ]);
                }
            }

            if ($er->status === 'PROPOSE') {
                $er->submit_date = now();
                $er->recruitmentSources()->detach();
                $er->decision = null;
                $er->decision_comment = null;
                $er->save();
                $recipientId = $er->approval1_id;
                if ($recipientId) {
                    $recipientUser = User::where('employee_id', $recipientId)->first();
                    if ($recipientUser && !empty($recipientUser->email)) {
                        $actionURL = $recipientUser->hasPermissionTo('emp.menu')
                            ? route('recruitment.emp.index')
                            : route('recruitment.profile.index');
                        $details = [
                            'greeting' => 'Hi ' . optional($recipientUser->employee)->fullname ?? 'Approval',
                            'subject' => 'Employee Requisition Notification',
                            'body' => 'We would like to inform you that new Employee Requisition from "' . optional($er->applicant)->fullname . '" has been submitted and requires your attention.',
                            'actionText' => 'Please Login',
                            'actionURL' => $actionURL,
                            'thanks' => 'Thank you for your attention!!'
                        ];
                        $recipientUser->notify(new AccountNotification($details));
                    }
                }
            }

            $employeeName = optional($er->applicant)->fullname ?? '-';
            $logAction = $wasRecentlyCreated ? 'insert' : 'update';
            $logDescription = ($wasRecentlyCreated ? 'Create New' : 'Modify')
                . ' Employee Requisition from applicant "' . $employeeName . '" with status: ' . ($er->status ?? '-');
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $logAction,
                'description' => $logDescription,
            ]);
            DB::commit();
            return response()->json([
                'message' => "Employee Requisition has been saved.",
                'redirect' => $redirectRoute
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'An error occurred while saving the requisition: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function profile_index(Request $request)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;
        $hasLineApproval = LineApprovalEmployee::where('employee_id', $employeeId)
            ->whereHas('lineApproval', function ($query) {
                $query->where('approval_type', 'Employee Requisition');
            })
            ->exists();
        $years = EmployeeRequisition::whereNotNull('submit_date')
            ->selectRaw('YEAR(submit_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4);
        }
        return view('pages.profile.recruitment.index', compact('user', 'years', 'hasLineApproval'));
    }

    public function profile_form(string $id = null)
    {
        $user = auth()->user();
        $approvals = [
            'approval1' => null,
            'approval2' => null,
            'approval3' => null,
            'approval4' => null,
        ];

        if ($id) {
            $id = decrypt($id);
            $er = EmployeeRequisition::with(['applicant'])->findOrFail($id);
            if ($er && ($er->status != 'DRAFT' && $er->status != 'REVISE' || $er->applicant_id != $user->employee_id)) {
                return redirect()->route('recruitment.profile.index')
                    ->with('error', 'Access Denied!');
            }
            $approvals = [
                'approval1' => $er->approval1 ?? null,
                'approval2' => $er->approval2 ?? null,
                'approval3' => $er->approval3 ?? null,
                'approval4' => $er->approval4 ?? null,
            ];
        } else {
            $er = null;
            $employeeId = $user->employee->id;
            $lineApprovalEmployee = LineApprovalEmployee::with([
                'lineApproval.approve1.position',
                'lineApproval.approve1.user',
                'lineApproval.approve2.position',
                'lineApproval.approve2.user',
                'lineApproval.approve3.position',
                'lineApproval.approve3.user',
                'lineApproval.approve4.position',
                'lineApproval.approve4.user',
            ])
                ->where('employee_id', $employeeId)
                ->whereHas('lineApproval', function ($query) {
                    $query->where('approval_type', 'Employee Requisition');
                })
                ->first();

            if (is_null($lineApprovalEmployee)) {
                return redirect()->back()
                    ->with('error', 'Access Denied!');
            }

            if ($lineApprovalEmployee && $lineApprovalEmployee->lineApproval) {
                $lineApproval = $lineApprovalEmployee->lineApproval;
                $approvals = [
                    'approval1' => $lineApproval->approve1 ?? null,
                    'approval2' => $lineApproval->approve2 ?? null,
                    'approval3' => $lineApproval->approve3 ?? null,
                    'approval4' => $lineApproval->approve4 ?? null,
                ];
            }
        }

        $approvals = array_filter($approvals);

        $departments = Department::all();
        $areas = Area::all();
        $positions = Position::all();
        $sections = Section::all();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();

        return view('pages.profile.recruitment.er.form', compact(
            'user',
            'er',
            'departments',
            'areas',
            'positions',
            'sections',
            'employees',
            'approvals'
        ));
    }

    private function getBaseMyERQuery($type = 'all')
    {
        $loggedInEmployeeId = Auth::user()->employee_id;
        $query = EmployeeRequisition::query()
            ->leftJoin('employees as applicant_emp', 'applicant_emp.id', '=', 'employee_requisition.applicant_id')
            ->leftJoin('master_position as pos', 'pos.id', '=', 'employee_requisition.position_id')
            ->leftJoin('departments as dept', 'dept.id', '=', 'employee_requisition.department_id')
            ->leftJoin('master_section as sect', 'sect.id', '=', 'employee_requisition.section_id')
            ->leftJoin('areas as ar', 'ar.id', '=', 'employee_requisition.area_id')
            ->select(
                'employee_requisition.*',
                'applicant_emp.fullname as applicant_name',
                'pos.nama as position_name',
                'dept.name as department_name',
                'sect.nama as section_name',
                'ar.name as area_name'
            )
            ->where('employee_requisition.applicant_id', $loggedInEmployeeId);
        if ($type === 'process') {
            $query->whereNotIn('employee_requisition.status', ['DONE']);
        } elseif ($type === 'done') {
            $query->where('employee_requisition.status', 'DONE');
        }
        $query->orderBy('employee_requisition.created_at', 'desc');
        return $query;
    }

    private function getRequisitionDataTableResponse($query, string $routePrefix)
    {
        return DataTables::of($query)
            ->editColumn('id', fn($data) => encrypt($data->id))
            ->addColumn('no_pengajuan', fn($data) => $data->no_pengajuan ?? '-')
            ->addColumn('applicant', fn($data) => optional($data->applicant)->fullname ?? '-')
            ->editColumn('needs', fn($data) => $data->needs)
            ->editColumn('reason', fn($data) => $data->reason_requisition)
            ->addColumn('position', fn($data) => optional($data->position)->nama ?? '-')
            ->addColumn('employee_status', fn($data) => $data->employee_status ?? '-')
            ->addColumn('department', fn($data) => optional($data->department)->name ?? '-')
            ->addColumn('section', fn($data) => optional($data->section)->nama ?? 'NA')
            ->addColumn('area', fn($data) => optional($data->area)->name ?? '-')
            ->addColumn('decision', function ($data) {
                $badges = [
                    'APPROVED' => 'success', 'PENDING' => 'warning', 'DISAPPROVED' => 'danger',
                ];
                $decision = $data->decision;
                return isset($badges[$decision]) ? "<span class=\"badge text-bg-{$badges[$decision]}\">{$decision}</span>" : '-';
            })
            ->addColumn('status', function ($data) {
                $badges = [
                    'PROPOSE' => 'success', 'DRAFT' => 'secondary', 'REVISE' => 'danger',
                    'REJECT' => 'dark', 'Checked' => 'success', 'Approved' => 'success',
                    'Prodir' => 'success', 'Presdir' => 'success', 'DONE' => 'success',
                ];
                $status = $data->status;
                return isset($badges[$status]) ? "<span class=\"badge text-bg-{$badges[$status]}\">{$status}</span>" : '-';
            })
            ->addColumn('action', function ($data) use ($routePrefix) {
                $encryptedId = encrypt($data->id);
                $actions = '';
                if ($data->status === 'DRAFT' || $data->status === 'REVISE') {
                    $actions .= '<a href="' . route($routePrefix . 'form', $encryptedId) . '" title="Edit" class="btn btn-warning btn-sm"><i class="ri-quill-pen-line"></i></a>';
                    $actions .= '&nbsp;<a href="#" data-id="' . $encryptedId . '" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></a>';
                } else {
                    $actions .= '<a href="' . route($routePrefix . 'detail', $encryptedId) . '" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                }
                $excluded_statuses = ['DRAFT', 'REVISE', 'REJECT'];
                if (!in_array($data->status, $excluded_statuses)) {
                    $actions .= '&nbsp;<button type="button" data-id="' . $encryptedId . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></button>';
                }
                $actions .= '&nbsp;<a href="' . route($routePrefix . 'print', $encryptedId) . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>';
                return $actions;
            })
            ->rawColumns(['action', 'status', 'decision'])
            ->make(true);
    }

    public function emp_getMyRequisition(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->getBaseMyERQuery();
            $routePrefix = 'recruitment.emp.er.';
            return $this->getRequisitionDataTableResponse($query, $routePrefix);
        }
    }

    public function profile_getMyRequisition(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->getBaseMyERQuery();
            $routePrefix = 'recruitment.profile.er.';
            return $this->getRequisitionDataTableResponse($query, $routePrefix);
        }
    }

    private function getBaseApproveERQuery($type)
    {
        $loggedInEmployeeId = Auth::user()->employee_id;
        $query = EmployeeRequisition::query()
            ->leftJoin('employees as applicant_emp', 'applicant_emp.id', '=', 'employee_requisition.applicant_id')
            ->leftJoin('master_position as pos', 'pos.id', '=', 'employee_requisition.position_id')
            ->leftJoin('departments as dept', 'dept.id', '=', 'employee_requisition.department_id')
            ->leftJoin('master_section as sect', 'sect.id', '=', 'employee_requisition.section_id')
            ->leftJoin('areas as ar', 'ar.id', '=', 'employee_requisition.area_id')
            ->select(
                'employee_requisition.*',
                'applicant_emp.fullname as applicant_name',
                'pos.nama as position_name',
                'dept.name as department_name',
                'sect.nama as section_name',
                'ar.name as area_name'
            )
            ->whereNotIn('employee_requisition.status', ['DRAFT', 'REJECT', 'REVISE']);
        if ($type === 'process') {
            $query->where(function ($q) use ($loggedInEmployeeId) {
                $q->where('employee_requisition.approval1_id', $loggedInEmployeeId)
                    ->whereNull('employee_requisition.approval1_date');
                $q->orWhere(fn($subQ) => $subQ->where('employee_requisition.approval2_id', $loggedInEmployeeId)
                    ->whereNotNull('employee_requisition.approval1_date')
                    ->whereNull('employee_requisition.approval2_date'));
                $q->orWhere(fn($subQ) => $subQ->where('employee_requisition.approval3_id', $loggedInEmployeeId)
                    ->whereNull('employee_requisition.approval3_date')
                    ->where(fn($subQ2) => $subQ2->whereNotNull('employee_requisition.approval2_date')
                        ->orWhereNull('employee_requisition.approval2_id')));
                $q->orWhere(fn($subQ) => $subQ->where('employee_requisition.approval4_id', $loggedInEmployeeId)
                    ->whereNull('employee_requisition.approval4_date')
                    ->where(fn($subQ2) => $subQ2->whereNotNull('employee_requisition.approval3_date')
                        ->orWhereNull('employee_requisition.approval3_id')));
            });
        } elseif ($type === 'done') {
            $query->where(function ($q) use ($loggedInEmployeeId) {
                $q->where('employee_requisition.approval1_id', $loggedInEmployeeId)->whereNotNull('employee_requisition.approval1_date')
                    ->orWhere(fn($subQ) => $subQ->where('employee_requisition.approval2_id', $loggedInEmployeeId)->whereNotNull('employee_requisition.approval2_date'))
                    ->orWhere(fn($subQ) => $subQ->where('employee_requisition.approval3_id', $loggedInEmployeeId)->whereNotNull('employee_requisition.approval3_date'))
                    ->orWhere(fn($subQ) => $subQ->where('employee_requisition.approval4_id', $loggedInEmployeeId)->whereNotNull('employee_requisition.approval4_date'));
            });
        }
        return $query;
    }

    private function getActionERRole($data, $loggedInEmployeeId)
    {
        $isApproval1Done = !is_null($data->approval1_date);
        $isApproval2Done = !is_null($data->approval2_date);
        $isApproval3Done = !is_null($data->approval3_date);
        if ($data->approval1_id == $loggedInEmployeeId && is_null($data->approval1_date)) {
            return 'approval1';
        } elseif ($data->approval2_id == $loggedInEmployeeId && is_null($data->approval2_date) && $isApproval1Done) {
            return 'approval2';
        } elseif ($data->approval3_id == $loggedInEmployeeId && is_null($data->approval3_date)) {
            if ($isApproval2Done || (is_null($data->approval2_id) && $isApproval1Done)) {
                return 'approval3';
            }
        } elseif ($data->approval4_id == $loggedInEmployeeId && is_null($data->approval4_date)) {
            if ($isApproval3Done || (is_null($data->approval3_id) && $isApproval2Done) || (is_null($data->approval3_id) && is_null($data->approval2_id) && $isApproval1Done)) {
                return 'approval4';
            }
        }
        return '';
    }

    private function getApprovalERDataTableResponse($query, $loggedInEmployeeId, $user)
    {
        return DataTables::of($query)
            ->editColumn('id', fn($data) => encrypt($data->id))
            ->addColumn('no_pengajuan', fn($data) => $data->no_pengajuan ?? '-')
            ->addColumn('applicantName', fn($data) => $data->applicant->fullname ?? '-')
            ->editColumn('needs', fn($data) => $data->needs)
            ->editColumn('reason', fn($data) => $data->reason_requisition)
            ->addColumn('position', fn($data) => $data->position_name ?? '-')
            ->addColumn('employee_status', fn($data) => $data->employee_status ?? '-')
            ->addColumn('department', fn($data) => $data->department_name ?? '-')
            ->addColumn('section', fn($data) => $data->section_name ?? 'NA')
            ->addColumn('area', fn($data) => $data->area_name ?? '-')
            ->addColumn('status', function ($data) {
                $badges = [
                    'PROPOSE' => 'success', 'DRAFT' => 'secondary', 'REVISE' => 'danger',
                    'REJECT' => 'dark', 'Checked' => 'success', 'Approved' => 'success',
                    'Prodir' => 'success', 'Presdir' => 'success', 'DONE' => 'success',
                ];
                $status = $data->status;
                return isset($badges[$status]) ? "<span class=\"badge text-bg-{$badges[$status]}\">{$status}</span>" : '-';
            })
            ->addColumn('action', function ($data) use ($loggedInEmployeeId, $user) {
                $role = $this->getActionERRole($data, $loggedInEmployeeId);
                $encryptedId = encrypt($data->id);
                $actions = '';
                $approveRoute = '#';
                $printRoute = '#';
                $routePrefix = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.er.' : 'recruitment.profile.er.';
                if (!empty($role)) {
                    $token = encrypt($data->id . '|' . $role); 
                    $approveRoute = route($routePrefix . 'approve.form', $token);
                    $actions .= '<a href="' . $approveRoute . '" title="Review" class="btn btn-success btn-sm"><i class="ri-quill-pen-line"></i></a>';
                }
                $actions .= '&nbsp;<button type="button" data-id="' . $encryptedId . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></button>';
                $excluded_statuses = ['DRAFT', 'REVISE', 'REJECT'];
                if (!in_array($data->status, $excluded_statuses)) {
                    $printRoute = route($routePrefix . 'print', ['er' => $encryptedId]);
                    $actions .= '&nbsp;<a href="' . $printRoute . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>';
                }
                return $actions;
            })
            ->addColumn('has_action', fn($data) => !empty($this->getActionERRole($data, $loggedInEmployeeId)))
            ->addColumn('role', fn($data) => $this->getActionERRole($data, $loggedInEmployeeId))
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function getApproveER(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->getBaseApproveERQuery('process');
            $loggedInEmployeeId = Auth::user()->employee_id;
            $user = Auth::user();
            return $this->getApprovalERDataTableResponse($query, $loggedInEmployeeId, $user);
        }
    }

    public function getDoneER(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $loggedInEmployeeId = $user->employee_id;
            $routePrefix = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.er.' : 'recruitment.profile.er.';
            $approveErDoneCollection = $this->getBaseApproveERQuery('done')->get();
            $approveErDoneCollection = $approveErDoneCollection->map(function ($item) use ($loggedInEmployeeId) {
                $item->applicantName = $item->applicant_name ?? '-';
                $item->is_my_er = false;
                $item->role = null;
                $item->has_action = false;
                return $item;
            });
            $myErDoneCollection = $this->getBaseMyERQuery('done')->get();
            $myErDoneCollection = $myErDoneCollection->map(function ($item) use ($user) {
                $item->applicantName = $item->applicant_name ?? $user->name;
                $item->is_my_er = true;
                $item->role = null;
                $item->has_action = false;
                return $item;
            });
            $combinedData = $approveErDoneCollection->merge($myErDoneCollection)->sortByDesc('updated_at');
            return DataTables::of($combinedData)
                ->editColumn('id', fn($data) => encrypt($data->id))
                ->addColumn('no_pengajuan', fn($data) => $data->no_pengajuan ?? '-')
                ->addColumn('applicantName', fn($data) => $data->applicantName ?? '-')
                ->editColumn('needs', fn($data) => $data->needs)
                ->editColumn('reason', fn($data) => $data->reason_requisition)
                ->addColumn('position', fn($data) => $data->position_name ?? '-')
                ->addColumn('employee_status', fn($data) => $data->employee_status ?? '-')
                ->addColumn('department', fn($data) => $data->department_name ?? '-')
                ->addColumn('section', fn($data) => $data->section_name ?? 'NA')
                ->addColumn('area', fn($data) => $data->area_name ?? '-')
                ->addColumn('status', function ($data) {
                    $badges = [
                        'PROPOSE' => 'success', 'DRAFT' => 'secondary', 'REVISE' => 'danger',
                        'REJECT' => 'dark', 'Checked' => 'success', 'Approved' => 'success',
                        'Prodir' => 'success', 'Presdir' => 'success', 'DONE' => 'success',
                    ];
                    $status = $data->status;
                    return isset($badges[$status]) ? "<span class=\"badge text-bg-{$badges[$status]}\">{$status}</span>" : '-';
                })
                ->addColumn('action', function ($data) use ($routePrefix, $loggedInEmployeeId) {
                    $encryptedId = encrypt($data->id);
                    $actions = '';
                    $actions .= '<a href="' . route($routePrefix . 'detail', $encryptedId) . '" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                    $excluded_statuses = ['DRAFT', 'REVISE', 'REJECT'];
                    if (!in_array($data->status, $excluded_statuses)) {
                        $actions .= '&nbsp;<button type="button" data-id="' . $encryptedId . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></button>';
                    }
                    if (!in_array($data->status, ['DRAFT', 'REVISE', 'REJECT'])) {
                        $printRoute = route($routePrefix . 'print', ['er' => $encryptedId]);
                        $actions .= '&nbsp;<a href="' . $printRoute . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>';
                    }
                    return $actions;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function countApproveER()
    {
        $query = $this->getBaseApproveERQuery('process');
        $jml_approve = $query->count();
        return response()->json(['jml_approve' => $jml_approve]);
    }

    public function getERSteps($id)
    {
        try {
            $decryptedId = decrypt($id);
            $empreq = EmployeeRequisition::findOrFail($decryptedId);
            if (!$empreq) {
                return response()->json(['error' => 'Employee Requisition not found.'], 404);
            }
            $steps = [];
            $steps[] = [
                'name' => 'Propose',
                'approval' => '',
                'date' => $empreq->submit_date ? Carbon::parse($empreq->submit_date)->format('d M Y, H:i') : null,
                'completed' => $empreq->submit_date != null,
            ];
            for ($i = 1; $i <= 4; $i++) {
                $approvalIdKey = "approval{$i}_id";
                $approvalRelKey = "approval{$i}";
                $approvalAsKey = "approval{$i}_as";
                $approvalDateKey = "approval{$i}_date";
                if ($empreq->$approvalIdKey !== null) {
                    $isCompleted = $empreq->$approvalDateKey != null;
                    $steps[] = [
                        'name' => $empreq->$approvalAsKey,
                        'approval' => ' by ' . $empreq->$approvalRelKey->fullname ?? '',
                        'date' => $empreq->$approvalDateKey ? Carbon::parse($empreq->$approvalDateKey)->format('d M Y, H:i') : null,
                        'completed' => $isCompleted,
                    ];
                }
            }
            return response()->json(['steps' => $steps]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$request->input('id')];
        }
        $decryptedIds = array_map(function ($id) {
            try {
                return decrypt($id);
            } catch (DecryptException $e) {
                return null;
            }
        }, $ids);
        $decryptedIds = array_filter($decryptedIds);
        if (empty($decryptedIds)) {
            return redirect()->back()->with('error', 'No valid Employee Requisition(s) were selected.');
        }
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $requisitionsToLog = EmployeeRequisition::with('applicant')
                ->whereIn('id', $decryptedIds)
                ->get();
            $deletedCount = 0;
            foreach ($requisitionsToLog as $er) {
                $applicantName = optional($er->applicant)->fullname ?? 'N/A';
                $erStatus = $er->status ?? 'N/A';
                $logDescription = "Deleted Employee Requisition " . ($er->no_pengajuan ? "(No: {$er->no_pengajuan})" : "") . " from applicant: {$applicantName} with status: {$erStatus}";
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'delete',
                    'description' => $logDescription,
                ]);
                $deletedCount++;
            }
            EmployeeRequisition::whereIn('id', $decryptedIds)->delete();
            DB::commit();
            $message = "$deletedCount Employee Requisition(s) have been successfully deleted.";
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete Employee Requisition(s): ' . $e->getMessage());
        }
    }

    public function review($token)
    {
        try {
            [$id, $role] = explode('|', decrypt($token));
        } catch (\Exception $e) {
            abort(404, 'Invalid or expired Employee Requisition link.');
        }
        $user = auth()->user();
        $er = EmployeeRequisition::findOrFail($id);
        $loggedInEmployeeId = Auth::user()->employee_id;
        if (in_array($er->status, ['DRAFT', 'REJECT', 'REVISE', 'DONE'])) {
            abort(403, 'This Employee Requisition is not available for review.');
        }
        $approvalSteps = [
            'approval1' => ['id_field' => 'approval1_id', 'date_field' => 'approval1_date'],
            'approval2' => ['id_field' => 'approval2_id', 'date_field' => 'approval2_date'],
            'approval3' => ['id_field' => 'approval3_id', 'date_field' => 'approval3_date'],
            'approval4' => ['id_field' => 'approval4_id', 'date_field' => 'approval4_date'],
        ];
        $currentStep = $approvalSteps[$role] ?? null;
        if (!$currentStep) {
            abort(403, 'Invalid role for Employee Requisition.');
        }
        if ($loggedInEmployeeId != $er->{$currentStep['id_field']}) {
            abort(403, 'You are not authorized to view this Employee Requisition.');
        }
        $isPreviousStepCompleted = true;
        foreach ($approvalSteps as $stepRole => $step) {
            if ($stepRole === $role) {
                break;
            }
            if (!is_null($er->{$step['id_field']}) && is_null($er->{$step['date_field']})) {
                $isPreviousStepCompleted = false;
                break;
            }
        }
        if (!$isPreviousStepCompleted) {
            abort(403, 'Previous approval steps have not been completed.');
        }
        $lastApprovalRole = null;
        foreach (array_keys($approvalSteps) as $step) {
            if (isset($er->{$approvalSteps[$step]['id_field']})) {
                $lastApprovalRole = $step;
            }
        }
        $isLastApproval = ($role == $lastApprovalRole);
        $reviewer = Employee::find($er->{$currentStep['id_field']});
        $approvals = [
            'approval1' => $er->approval1 ?? null,
            'approval2' => $er->approval2 ?? null,
            'approval3' => $er->approval3 ?? null,
            'approval4' => $er->approval4 ?? null,
        ];
        $viewPath = ($user->hasPermissionTo('emp.menu'))
            ? 'pages.employee.recruitment.er.review'
            : 'pages.profile.recruitment.er.review';
        return view($viewPath, compact(
            'er',
            'role',
            'token',
            'reviewer',
            'user',
            'approvals',
            'isLastApproval'
        ));
    }

    public function review_store(Request $request, $token)
    {
        try {
            [$id, $role] = explode('|', decrypt($token));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid or expired token.'], 404);
        }

        $user = auth()->user();
        $loggedInEmployeeId = Auth::user()->employee_id;

        $er = EmployeeRequisition::with([
            'recruitmentSources', 'approval1', 'approval2', 'approval3', 'approval4'
        ])->findOrFail($id);

        if (in_array($er->status, ['DONE'])) {
            return response()->json(['message' => 'Employee Requisition is already finalized.'], 403);
        }

        $approvalSteps = [
            'approval1' => ['id_field' => 'approval1_id', 'date_field' => 'approval1_date', 'as_field' => 'approval1_as'],
            'approval2' => ['id_field' => 'approval2_id', 'date_field' => 'approval2_date', 'as_field' => 'approval2_as'],
            'approval3' => ['id_field' => 'approval3_id', 'date_field' => 'approval3_date', 'as_field' => 'approval3_as'],
            'approval4' => ['id_field' => 'approval4_id', 'date_field' => 'approval4_date', 'as_field' => 'approval4_as'],
        ];

        $currentStep = $approvalSteps[$role] ?? null;

        if (!$currentStep || $loggedInEmployeeId != $er->{$currentStep['id_field']}) {
            return response()->json(['message' => 'You are not authorized to approve this form.'], 403);
        }

        $lastApprovalRole = null;
        foreach (array_keys($approvalSteps) as $step) {
            if (isset($er->{$approvalSteps[$step]['id_field']})) {
                $lastApprovalRole = $step;
            }
        }
        $isLastApproval = ($role == $lastApprovalRole);
        $rawAction = $request->input('status');
        $action = ($rawAction === 'SUBMIT_NON_LAST') ? 'SUBMIT' : $rawAction;
        $rules = [
            'id' => 'required|exists:employee_requisition,id',
            'status' => 'required|string|in:APPROVED,DISAPPROVED,PENDING,DRAFT,SUBMIT,SUBMIT_NON_LAST',             
            'decision_comment' => 'nullable|string|max:255',
        ];
        if ($isLastApproval && in_array($action, ['DISAPPROVED', 'PENDING'])) {
            $rules['decision_comment'] = 'required|string|max:255';
        }
        $validatedData = $request->validate($rules);
        $decisionComment = $validatedData['decision_comment'] ?? null;
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $logAction = '';
            $nextApprovalId = null;
            $finalStatus = null;
            if ($action === 'SUBMIT') {
                $er->{$currentStep['date_field']} = $now;
                $currentApprovalAs = $er->{$currentStep['as_field']};
                switch ($currentApprovalAs) {
                    case 'Checker': $finalStatus = 'Checked'; break;
                    case 'Approval': $finalStatus = 'Approved'; break;
                    case 'Director': $finalStatus = 'Prodir'; break;
                    case 'President Director': $finalStatus = 'Presdir'; break;
                    default: $finalStatus = ucfirst(str_replace('approval', 'approval ', $role)); break;
                }
                $er->status = $finalStatus;
                $foundCurrent = false;
                $approvalRoles = array_keys($approvalSteps);
                foreach ($approvalRoles as $stepRole) {
                    if ($stepRole === $role) {
                        $foundCurrent = true;
                        continue;
                    }
                    if ($foundCurrent && !empty($er->{$approvalSteps[$stepRole]['id_field']})) {
                        $nextApprovalId = $er->{$approvalSteps[$stepRole]['id_field']};
                        break;
                    }
                }
                $logAction = 'approved';
                $message = 'Employee Requisition Submitted successfully!';
            } elseif ($action === 'DRAFT') {
                $er->decision_comment = $decisionComment;
                $logAction = 'update';
                $message = 'Employee Requisition Drafted successfully!';
            } else {
                $er->decision = $action;
                $er->decision_comment = $decisionComment;
                $er->{$currentStep['date_field']} = $now;
                $currentApprovalAs = $er->{$currentStep['as_field']};
                switch ($currentApprovalAs) {
                    case 'Checker': $finalStatus = 'Checked'; break;
                    case 'Approval': $finalStatus = 'Approved'; break;
                    case 'Director': $finalStatus = 'Prodir'; break;
                    case 'President Director': $finalStatus = 'Presdir'; break;
                    default: $finalStatus = ucfirst(str_replace('approval', 'approval ', $role)); break;
                }
                $er->status = $finalStatus;
                $logAction = 'approved';
                $foundCurrent = false;
                $approvalRoles = array_keys($approvalSteps);
                foreach ($approvalRoles as $stepRole) {
                    if ($stepRole === $role) {
                        $foundCurrent = true;
                        continue;
                    }
                    if ($foundCurrent && !empty($er->{$approvalSteps[$stepRole]['id_field']})) {
                        $nextApprovalId = $er->{$approvalSteps[$stepRole]['id_field']};
                        break;
                    }
                }
                if (!$nextApprovalId) {
                    $er->status = 'DONE';
                    $finalStatus = 'DONE';
                    $er->no_pengajuan = EmployeeRequisition::generateNoPengajuan();
                }
            }
            $er->updated_at = $now;
            $er->save();
            $tab = '';
            if ($action === 'APPROVED') {
                $log = "Submitted Employee Requisition from {$er->applicant->fullname} with status ({$er->status})[{$role}]";
                $message = 'Employee Requisition Submitted successfully!';
                $tab = 'tab_done';
                $logAction = 'approved';
            } elseif (in_array($action, ['DISAPPROVED', 'PENDING'])) {
                $actionTitleCase = ucwords(strtolower($action));
                $log = "{$actionTitleCase} Employee Requisition from {$er->applicant->fullname} with status ({$er->status})[{$role}]";
                $message = 'Employee Requisition Submitted successfully!';
                $tab = 'tab_done';
                $logAction = 'approved';
            } elseif ($action === 'SUBMIT') {
                $log = "Submitted Employee Requisition from {$er->applicant->fullname} with status ({$er->status})[{$role}]";
                $message = 'Employee Requisition Submitted successfully!';
                $tab = 'tab_done';
                $logAction = 'approved';
            } else {
                $log = "Drafted Employee Requisition from {$er->applicant->fullname} with status ({$er->status})[{$role}]";
                $message = 'Employee Requisition Drafted successfully!';
                $logAction = 'update';
            }
            
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $logAction,
                'description' => $log,
            ]);

            if ($nextApprovalId) {
                $recipientUser = User::where('employee_id', $nextApprovalId)->first();
                if ($recipientUser && !empty($recipientUser->email)) {
                    $actionURL = $recipientUser->hasPermissionTo('emp.menu')
                        ? route('recruitment.emp.index')
                        : route('recruitment.profile.index');
                    $details = [
                        'greeting' => 'Hi ' . optional($recipientUser->employee)->fullname ?? 'Approval',
                        'subject' => 'Employee Requisition Notification',
                        'body' => 'We would like to inform you that new Employee Requisition from "' . optional($er->applicant)->fullname . '" requires your attention.',
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $recipientUser->notify(new AccountNotification($details));
                }
            }
            DB::commit();
            $redirectRoute = ($user->hasPermissionTo('emp.menu')) ? 'recruitment.emp.index' : 'recruitment.profile.index';
            return response()->json([
                'message' => $message,
                'redirect' => route($redirectRoute, [$tab])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to process Employee Requisition review.',
                'errors' => ['server' => ['An unexpected error occurred: ' . $e->getMessage()]],
                'responseText' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $user = auth()->user();
        $id = decrypt($id);
        $er = EmployeeRequisition::with(['applicant'])->findOrFail($id);
        $approvals = [
            'approval1' => $er->approval1 ?? null,
            'approval2' => $er->approval2 ?? null,
            'approval3' => $er->approval3 ?? null,
            'approval4' => $er->approval4 ?? null,
        ];

        $approvals = array_filter($approvals);

        $departments = Department::all();
        $areas = Area::all();
        $positions = Position::all();
        $sections = Section::all();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();

        $viewPath = ($user->hasPermissionTo('emp.menu'))
            ? 'pages.employee.recruitment.er.detail'
            : 'pages.profile.recruitment.er.detail';

        return view($viewPath, compact(
            'user',
            'er',
            'departments',
            'areas',
            'positions',
            'sections',
            'employees',
            'approvals'
        ));
    }

    public function detail_hrd($id)
    {
        $user = auth()->user();
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }
        $er = EmployeeRequisition::with([
            'applicant.position',
            'applicant.department',
            'position', 'department', 'section', 'area', 'person_replace',
            'educationalRequirements', 'genderRequirements', 'recruitmentSources',
            'approval1', 'approval2', 'approval3', 'approval4',
            'hiringSteps.masterHiring',
            'hiringSteps.selectionProcesses.candidates.candidate' 
        ])->findOrFail($id);
        $finalCandidates = $this->getFinalCandidates($er);
        $candidateKtps = array_column($finalCandidates, 'no_ktp');
        $activeEmployeeKtps = Employee::whereIn('no_ktp', $candidateKtps)
            ->where('status', '!=', 'TERMINATED')
            ->pluck('no_ktp')
            ->toArray();
        foreach ($finalCandidates as &$candidate) {
            $candidate['is_hired'] = in_array($candidate['no_ktp'], $activeEmployeeKtps);
        }
        unset($candidate);
        $approvals = array_filter([
            'approval1' => $er->approval1,
            'approval2' => $er->approval2,
            'approval3' => $er->approval3,
            'approval4' => $er->approval4,
        ]);
        $masterHiring = Hiring::all();
        return view('pages.hrd.recruitment.er.detail', compact(
            'user',
            'er',
            'approvals',
            'masterHiring',
            'finalCandidates' 
        ));
    }

    private function getFinalCandidates(EmployeeRequisition $er)
    {
        $candidates = [];
        $lastStep = $er->hiringSteps->sortByDesc('step_order')->first();
        if ($lastStep) {
            foreach ($lastStep->selectionProcesses as $process) {
                foreach ($process->candidates as $pivotData) {
                    if ($pivotData->result_status == 1) {
                        $candidate = $pivotData->candidate;
                        if ($candidate) {
                            $age = $candidate->birthdate ? Carbon::parse($candidate->birthdate)->diff(Carbon::now())->format('%y Years') : '-';
                            $rawData = [
                                'nickname' => $candidate->nickname,
                                'ktp_address' => $candidate->ktp_address,
                                'domicile_address' => $candidate->domicile_address,
                                'birthplace' => $candidate->birthplace,
                                'birthdate' => $candidate->birthdate ? $candidate->birthdate->format('d/m/Y') : '-',
                                'age' => $age,
                                'gender' => $candidate->gender,
                                'religion' => $candidate->religion,
                                'marital' => $candidate->marital,
                                'height' => $candidate->height,
                                'weight' => $candidate->weight,
                                'phone' => $candidate->phone,
                                'email' => $candidate->email,
                                'photo' => $candidate->photo,
                                'expected_salary' => $candidate->expected_salary,
                                'posting_title' => $candidate->posting->title ?? '-',
                                'submit_date' => $candidate->submit_date ? $candidate->submit_date->format('d F Y') : '-',
                                'pos_name' => $candidate->position->nama ?? '-',
                                'sect_name' => $candidate->section->nama ?? '-',
                                'dept_name' => $candidate->department->name ?? '-',
                                'area_name' => $candidate->area->name ?? '-',
                            ];
                            $candidates[] = [
                                'candidate_id' => $candidate->id,
                                'fullname' => $candidate->fullname,
                                'no_ktp' => $candidate->no_ktp,
                                'attachment' => $pivotData->attachment ?? null,
                                'age' => $age,
                                'gender' => $candidate->gender,
                                'action' => '<button type="button" title="Detail" class="btn btn-info btn-sm view-detail" data-id="' . $candidate->id . '"><i class="ri-eye-2-line"></i></button>',
                                'raw_data' => $rawData,
                                'raw_educations' => $candidate->educations->sortByDesc('year_graduated')->values(),
                                'raw_experiences' => $candidate->experiences->sortByDesc('years')->values(),
                            ];
                        }
                    }
                }
            }
        }
        return $candidates;
    }

    public function print($id)
    {
        try {
            $er_id = decrypt($id);
            $er = EmployeeRequisition::findOrFail($er_id);
            $er->load([
                'applicant',
                'approval1',
                'approval2',
                'approval3',
                'approval4',
                'person_replace'
            ]);
            $data = ['er' => $er];
            $pdf = Pdf::loadView('pages.hrd.recruitment.er.print', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('is_svg_enabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true);
            return $pdf->stream(($er->no_pengajuan ?? 'Employee Requisition') . ' - ' . ($er->applicant->fullname ?? 'Employee') . '.pdf');
        } catch (DecryptException $e) {
            abort(404, 'Invalid ID.');
        } catch (ModelNotFoundException $e) {
            abort(404, 'Employee Requisition Not Found.');
        }
    }

    public function qr_code_approval($token)
    {
        try {
            [$id, $role] = explode('|', decrypt($token));
        } catch (\Exception $e) {
            abort(404, 'Invalid Employee Requisition approval data link.');
        }
        $er = EmployeeRequisition::with('educationalRequirements')->findOrFail($id);
        $erId = $er->no_pengajuan;
        $approvalName = optional($er->{$role})->fullname;
        if ($role == 'applicant') {
            $approvalAs   = 'Applicant';
            $approvalOn   = $er->submit_date;
        } else {
            $approvalAs   = $er->{$role . '_as'};
            $approvalOn   = $er->{$role . '_date'};
        }
        return view('pages.hrd.recruitment.er.codeqr-approval', compact('erId', 'approvalName', 'approvalAs', 'approvalOn'));
    }

    public function getProcessCombinedER(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $loggedInEmployeeId = $user->employee_id;
            $routePrefix = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.er.' : 'recruitment.profile.er.';
            $myErCollection = $this->getBaseMyERQuery('process')->get();
            $myErCollection = $myErCollection->map(function ($item) use ($user) {
                $item->applicantName = $item->applicant_name ?? $user->name;
                $item->is_my_er = true;
                $item->role = null;
                $item->has_action = false;
                return $item;
            });
            $approveErCollection = $this->getBaseApproveERQuery('process')->get();
            $approveErCollection = $approveErCollection->map(function ($item) use ($loggedInEmployeeId) {
                $item->applicantName = $item->applicant_name ?? '-';
                $item->is_my_er = false;
                $item->role = $this->getActionERRole($item, $loggedInEmployeeId);
                $item->has_action = !empty($item->role);
                return $item;
            });
            $combinedData = $myErCollection->merge($approveErCollection)->sortByDesc('created_at');
            return DataTables::of($combinedData)
                ->editColumn('id', fn($data) => encrypt($data->id))
                ->addColumn('no_pengajuan', fn($data) => $data->no_pengajuan ?? '-')
                ->addColumn('applicantName', fn($data) => $data->applicantName ?? '-')
                ->editColumn('needs', fn($data) => $data->needs)
                ->editColumn('reason', fn($data) => $data->reason_requisition)
                ->addColumn('position', fn($data) => optional($data->position)->nama ?? $data->position_name ?? '-')
                ->addColumn('employee_status', fn($data) => $data->employee_status ?? '-')
                ->addColumn('department', fn($data) => optional($data->department)->name ?? $data->department_name ?? '-')
                ->addColumn('section', fn($data) => optional($data->section)->nama ?? $data->section_name ?? 'NA')
                ->addColumn('area', fn($data) => optional($data->area)->name ?? $data->area_name ?? '-')
                ->addColumn('decision', function ($data) {
                    $badges = ['APPROVED' => 'success', 'PENDING' => 'warning', 'DISAPPROVED' => 'danger'];
                    $decision = $data->decision ?? '-';
                    return isset($badges[$decision]) ? "<span class=\"badge text-bg-{$badges[$decision]}\">{$decision}</span>" : '-';
                })
                ->addColumn('status', function ($data) {
                    $badges = [
                        'PROPOSE' => 'success', 'DRAFT' => 'secondary', 'REVISE' => 'danger',
                        'REJECT' => 'dark', 'Checked' => 'success', 'Approved' => 'success',
                        'Prodir' => 'success', 'Presdir' => 'success', 'DONE' => 'success',
                    ];
                    $status = $data->status;
                    return isset($badges[$status]) ? "<span class=\"badge text-bg-{$badges[$status]}\">{$status}</span>" : '-';
                })
                ->addColumn('action', function ($data) use ($routePrefix, $user) {
                    $encryptedId = encrypt($data->id);
                    $actions = '';
                    if (isset($data->has_action) && $data->has_action === true) {
                        $token = encrypt($data->id . '|' . $data->role);
                        $actions .= '<a href="' . route($routePrefix . 'approve.form', $token) . '" title="Review" class="btn btn-success btn-sm"><i class="ri-quill-pen-line"></i></a>';
                    } else if (isset($data->is_my_er) && $data->is_my_er === true && ($data->status === 'DRAFT' || $data->status === 'REVISE' || $data->status == 'REJECT')) {
                        if ($data->status === 'DRAFT' || $data->status === 'REVISE') {
                            $actions .= '<a href="' . route($routePrefix . 'form', $encryptedId) . '" title="Edit" class="btn btn-warning btn-sm"><i class="ri-quill-pen-line"></i></a>';
                        }
                        $actions .= '&nbsp;<a href="#" data-id="' . $encryptedId . '" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></a>';
                    } else {
                        $actions .= '<a href="' . route($routePrefix . 'detail', $encryptedId) . '" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                    }
                    $excluded_steps = ['DRAFT', 'REVISE', 'REJECT'];
                    if (!in_array($data->status, $excluded_steps)) {
                        $actions .= '&nbsp;<button type="button" data-id="' . $encryptedId . '" title="Steps" class="btn btn-primary btn-sm btn-view-steps"><i class="ri-list-check"></i></button>';
                    }
                    if ($data->status != 'REVISE' && $data->status != 'REJECT') {
                        $printRoute = route($routePrefix . 'print', ['er' => $encryptedId]);
                        $actions .= '&nbsp;<a href="' . $printRoute . '" target="_blank" title="Print" class="btn btn-success btn-sm"><i class="ri-printer-fill"></i></a>';
                    }
                    if ($data->status == 'REVISE' || $data->status == 'REJECT') {
                        $actions .= '&nbsp;<button type="button" data-id="' . $encryptedId . '" title="Reason" class="btn btn-secondary btn-sm btn-reason"><i class="ri-information-line"></i></a>';
                    }
                    return $actions;
                })
                ->rawColumns(['action', 'status', 'decision'])
                ->make(true);
        }
    }

    public function revice(Request $request, $token)
    {
        $request->validate([
            'reason_comment' => 'required|string|max:60',
        ]);
        try {
            [$erId, $role] = explode('|', decrypt($token));
            $user = auth()->user();
            $er = EmployeeRequisition::with(['applicant'])->findOrFail($erId);
            $approvalFields = [
                'approval1' => ['id' => 'approval1_id', 'as' => 'approval1_as', 'relation' => 'approval1'],
                'approval2' => ['id' => 'approval2_id', 'as' => 'approval2_as', 'relation' => 'approval2'],
                'approval3' => ['id' => 'approval3_id', 'as' => 'approval3_as', 'relation' => 'approval3'],
                'approval4' => ['id' => 'approval4_id', 'as' => 'approval4_as', 'relation' => 'approval4'],
            ];
            $currentApproval = $approvalFields[$role];
            $applicantName = optional($er->applicant)->fullname ?? 'N/A';
            $evaluatorRole = $er->{$currentApproval['as']} ?? 'Unknown Role';
            $reviceReason = $request->input('reason_comment');
            $er->status = 'REVISE';
            $er->decision_comment = $reviceReason;
            $er->approval1_date = null;
            $er->approval2_date = null;
            $er->approval3_date = null;
            $er->approval4_date = null;
            $er->save();
            $logDescription = "Employee Requisition from {$applicantName} Revice by ({$evaluatorRole}) with reason: " . $reviceReason;
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'revised',
                'description' => $logDescription,
            ]);
            $redirectRoute = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.index' : 'recruitment.profile.index';
            return response()->json([
                'message' => 'Employee Requisition successfully Revice!',
                'redirect' => route($redirectRoute)
            ]);
        } catch (DecryptException $e) {
            return back()->with('error', 'Failed to revice Employee Requisition: Invalid token.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to revice Employee Requisition: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $token)
    {
        $request->validate([
            'reason_comment' => 'required|string|max:60',
        ]);
        try {
            [$erId, $role] = explode('|', decrypt($token));
            $user = auth()->user();
            $er = EmployeeRequisition::with(['applicant'])->findOrFail($erId);
            $approvalFields = [
                'approval1' => ['id' => 'approval1_id', 'as' => 'approval1_as', 'relation' => 'approval1'],
                'approval2' => ['id' => 'approval2_id', 'as' => 'approval2_as', 'relation' => 'approval2'],
                'approval3' => ['id' => 'approval3_id', 'as' => 'approval3_as', 'relation' => 'approval3'],
                'approval4' => ['id' => 'approval4_id', 'as' => 'approval4_as', 'relation' => 'approval4'],
            ];
            $currentApproval = $approvalFields[$role];
            $applicantName = optional($er->applicant)->fullname ?? 'N/A';
            $evaluatorRole = $er->{$currentApproval['as']} ?? 'Unknown Role';
            $rejectReason = $request->input('reason_comment');
            $er->status = 'REJECT';
            $er->decision_comment = $rejectReason;
            $er->submit_date = null;
            $er->approval1_date = null;
            $er->approval2_date = null;
            $er->approval3_date = null;
            $er->approval4_date = null;
            $er->save();
            $logDescription = "Employee Requisition from {$applicantName} Reject by ({$evaluatorRole}) with reason: " . $rejectReason;
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'revised',
                'description' => $logDescription,
            ]);
            $redirectRoute = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.index' : 'recruitment.profile.index';
            return response()->json([
                'message' => 'Employee Requisition successfully Reject!',
                'redirect' => route($redirectRoute)
            ]);
        } catch (DecryptException $e) {
            return back()->with('error', 'Failed to reject Employee Requisition: Invalid token.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject Employee Requisition: ' . $e->getMessage());
        }
    }

    public function getDecisionReason($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $er = EmployeeRequisition::findOrFail($id);
            if (!$er) {
                return response()->json(['message' => 'Data not found.'], 404);
            }
            return response()->json([
                'reason' => $er->decision_comment
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['message' => 'Invalid ID.'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
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
            $nextApprovals = collect();

            $erIds = $request->input('ids');

            if (empty($erIds)) {
                return back()->with('error', 'No Employee Requisitions were selected.');
            }

            $approvalSteps = [
                'approval1' => ['id_field' => 'approval1_id', 'date_field' => 'approval1_date', 'as_field' => 'approval1_as'],
                'approval2' => ['id_field' => 'approval2_id', 'date_field' => 'approval2_date', 'as_field' => 'approval2_as'],
                'approval3' => ['id_field' => 'approval3_id', 'date_field' => 'approval3_date', 'as_field' => 'approval3_as'],
                'approval4' => ['id_field' => 'approval4_id', 'date_field' => 'approval4_date', 'as_field' => 'approval4_as'],
            ];

            $statusMap = [
                'Checker' => 'Checked',
                'Approval' => 'Approved',
                'Director' => 'Prodir',
                'President Director' => 'Presdir',
            ];

            $er = EmployeeRequisition::with(['applicant', 'approval1', 'approval2', 'approval3', 'approval4'])
                ->whereIn('id', array_map('decrypt', $erIds))
                ->get();

            foreach ($er as $req) {
                $applicantName = optional($req->applicant)->fullname ?? 'N/A';
                $skipReason = '';
                $currentRole = null;
                $currentStep = null;
                foreach ($approvalSteps as $stepName => $stepData) {
                    if ($loggedInEmployeeId == $req->{$stepData['id_field']} && is_null($req->{$stepData['date_field']})) {
                        $currentRole = $stepName;
                        $currentStep = $stepData;
                        break;
                    }
                }

                if (is_null($currentRole)) {
                    $skippedCount++;
                    $failedNames[] = "{$applicantName} (Not the designated approver or already approved.)";
                    continue;
                }

                $isPreviousStepCompleted = true;
                foreach ($approvalSteps as $stepRole => $step) {
                    if ($stepRole === $currentRole) break;
                    if (!is_null($req->{$step['id_field']}) && is_null($req->{$step['date_field']})) {
                        $isPreviousStepCompleted = false;
                        $skipReason = 'Previous approval step (' . $stepRole . ') is not yet completed.';
                        break;
                    }
                }

                if ($isPreviousStepCompleted) {
                    $currentApprovalAs = $req->{$currentStep['as_field']};
                    $newStatus = $statusMap[$currentApprovalAs] ?? 'Unknown Status';
                    $req->status = $newStatus;
                    $req->{$currentStep['date_field']} = now();
                    $isLastStep = true;
                    $foundCurrent = false;
                    foreach ($approvalSteps as $stepRole => $step) {
                        if ($stepRole === $currentRole) {
                            $foundCurrent = true;
                            continue;
                        }
                        if ($foundCurrent && !empty($req->{$step['id_field']})) {
                            $isLastStep = false;
                            $nextApprovals->push($req->{$step['id_field']});
                            break;
                        }
                    }

                    if ($isLastStep) {
                        $req->status = 'DONE';
                        $req->decision = 'APPROVED';
                        $req->no_pengajuan = EmployeeRequisition::generateNoPengajuan();
                    }
                    $req->save();

                    $logDescription = "Submitted Employee Requisition from {$applicantName} with status ({$req->status})[{$currentRole}]";
                    Log::create([
                        'user_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'action' => 'approved',
                        'description' => $logDescription,
                    ]);

                    $approvedCount++;
                    $approvedNames[] = $applicantName;
                } else {
                    $skippedCount++;
                    $failedNames[] = $applicantName;
                }
            }

            // Email Notif
            $uniqueNextApprovalIds = $nextApprovals->unique();
            foreach ($uniqueNextApprovalIds as $approvalId) {
                $nextApprovalUser = User::where('employee_id', $approvalId)->first();
                if ($nextApprovalUser && !empty($nextApprovalUser->email)) {
                    $erForUser = $er->where($currentStep['id_field'], $loggedInEmployeeId);
                    $erCount = $erForUser->count();
                    $bodyMessage = '';
                    $employeeNames = [];
                    $actionURL = $nextApprovalUser->hasPermissionTo('emp.menu')
                        ? route('recruitment.emp.index')
                        : route('recruitment.profile.index');
                    if ($erCount > 10) {
                        $bodyMessage = 'We would like to inform you that ' . $erCount . ' Employee Requisitions require your attention.';
                    } else {
                        $employeeNames = $erForUser->map(fn($e) => optional($e->applicant)->fullname)->filter()->values()->toArray();
                        $bodyMessage = 'We would like to inform you that ' . $erCount . ' Employee Requisitions for the following applicants require your attention:';
                    }
                    $details = [
                        'greeting' => 'Hi ' . optional($nextApprovalUser->employee)->fullname ?? 'Approval',
                        'subject' => 'Employee Requisition Notification',
                        'body' => $bodyMessage,
                        'employeeNames' => $employeeNames,
                        'actionText' => 'Please Login',
                        'actionURL' => $actionURL,
                        'thanks' => 'Thank you for your attention!!'
                    ];
                    $nextApprovalUser->notify(new EvaluationNotification($details));
                }
            }

            DB::commit();
            $message = "Successfully approved {$approvedCount} Employee Requisition(s).";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} Employee Requisition(s) due to invalid status or authorization: " . implode(', ', $failedNames);
            }
            $redirectRoute = ($user->hasPermissionTo('emp.menu')) ? 'recruitment.emp.index' : 'recruitment.profile.index';
            return redirect()->route($redirectRoute)->with('status', $message)->with('tab_done', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve Employee Requisitions: ' . $e->getMessage());
        }
    }

    public function detailRecSourceHRDStore(Request $request, $token)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $erId = decrypt($token);
            $er = EmployeeRequisition::findOrFail($erId);
            $request->validate([
                'recruitment_source' => 'nullable|array', 
                'other_source.Others' => 'nullable|string|max:60',
            ]);
            $selectedSourceNames = $request->input('recruitment_source', []); 
            $syncData = [];
            if (!empty($selectedSourceNames)) {
                $sourceMasters = EmployeeRequisitionRecruitmentSource::whereIn('name', $selectedSourceNames)->get();
                $otherDetail = $request->input('other_source.Others', null);
                foreach ($sourceMasters as $source) {
                    $pivotData = [];
                    if ($source->name === 'Others') {
                        $pivotData = ['other_detail' => $otherDetail];
                    }
                    $syncData[$source->id] = $pivotData;
                }
            }
            $er->recruitmentSources()->sync($syncData);
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => 'Updated Recruitment Source for Employee Requisition ('.$er->no_pengajuan.')',
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Recruitment Source has been saved'
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Invalid Request Token: ' . $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed Saving. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stepSelectionStore($id, Request $request)
    {
        $user = auth()->user();
        try {
            $requisitionId = decrypt($id);
            $er = EmployeeRequisition::findOrFail($requisitionId);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Invalid Requisition ID.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'steps.*.master_hiring_id' => 'required|exists:master_hiring,id',
            'new_steps.*.master_hiring_id' => 'required|exists:master_hiring,id',
            'master_hiring_ids' => 'sometimes|array', 
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error.', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            if ($request->has('deleted_steps')) {
                EmployeeRequisitionHiringStep::whereIn('id', $request->input('deleted_steps'))
                    ->where('requisition_id', $requisitionId)
                    ->delete();
            }

            if ($request->has('steps')) {
                foreach ($request->input('steps') as $stepId => $stepData) {
                    EmployeeRequisitionHiringStep::where('id', $stepId)
                        ->where('requisition_id', $requisitionId)
                        ->update([
                            'master_hiring_id' => $stepData['master_hiring_id'],
                            'step_order' => $stepData['order'],
                        ]);
                }
            }

            if ($request->has('new_steps')) {
                $newStepsData = [];
                foreach ($request->input('new_steps') as $newStepData) {
                    $newStepsData[] = [
                        'requisition_id' => $requisitionId,
                        'master_hiring_id' => $newStepData['master_hiring_id'],
                        'step_order' => $newStepData['order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                EmployeeRequisitionHiringStep::insert($newStepsData);
            }
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => 'Updated Step Selection for Employee Requisition ('.$er->no_pengajuan.')',
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Step Selection has been saved',
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An unexpected error occurred during saving. Please try again.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function closeRequisition($id, Request $request)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['message' => 'Invalid Requisition ID.'], 400);
        }
        $request->validate([
            'fulfilled_reason' => 'required|string|max:255',
        ]);
        $er = EmployeeRequisition::findOrFail($decryptedId);
        if (!is_null($er->fulfilled_date)) {
            return response()->json(['message' => 'This requisition is already closed.'], 400);
        }
        $er->update([
            'fulfilled_date' => now(),
            'fulfilled_reason' => $request->fulfilled_reason
        ]);
        Log::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'action' => 'closing',
            'description' => 'Closing for Employee Requisition ('.$er->no_pengajuan.')',
        ]);
        return response()->json([
            'message' => 'Employee Requisition has been Closed.'
        ], 200);
    }

    public function storeHiredCandidates($id, Request $request)
    {
        try {
            $erId = decrypt($id);
            $requisition = EmployeeRequisition::lockForUpdate()->findOrFail($erId);
            $candidateIds = $request->candidate_ids;
            if (empty($candidateIds)) {
                return response()->json(['message' => 'No candidates selected.'], 400);
            }
            DB::beginTransaction();
            $hiredCount = 0;
            $duplicateCount = 0;
            $currentYear = date('Y');

            $reqStatus = $requisition->employee_status;
            if ($reqStatus == 'Magang / Internship') {
                DB::rollBack();
                return response()->json(['message' => 'Candidates with "Internship" status cannot be added to the Employee.'], 422);
            }
            $employeeStatusEnum = 'PROBATION';
            switch ($reqStatus) {
                case 'Percobaan / Probation':
                    $employeeStatusEnum = 'PROBATION';
                    break;
                case 'Kontrak / Contract':
                    $employeeStatusEnum = 'CONTRACT';
                    break;
                case 'Alih Daya / Outsourcing':
                    $employeeStatusEnum = 'OUTSOURCING';
                    break;
            }

            foreach ($candidateIds as $candidateId) {
                $candidate = Candidate::find($candidateId);
                if (!$candidate) continue;

                $isActiveEmployee = Employee::where('no_ktp', $candidate->no_ktp)
                                        ->where('status', '!=', 'TERMINATED')
                                        ->exists();
                if ($isActiveEmployee) {
                    $duplicateCount++; 
                    continue;
                }

                // Generate NIK
                $lastEmployee = Employee::where('nik', 'like', $currentYear . '%')
                                    ->orderBy('nik', 'desc')
                                    ->first();
                if ($lastEmployee) {
                    $lastSequence = (int) substr($lastEmployee->nik, -3);
                    $newSequence = $lastSequence + 1;
                } else {
                    $newSequence = 1;
                }
                $generatedNIK = $currentYear . str_pad($newSequence, 3, '0', STR_PAD_LEFT);

                Employee::create([
                    'nik'           => $generatedNIK,
                    'no_ktp'        => $candidate->no_ktp,
                    'fullname'      => $candidate->fullname,
                    'email'         => $candidate->email,
                    'addressktp'    => $candidate->ktp_address,
                    'birthplace'    => $candidate->birthplace,
                    'birthdate'     => $candidate->birthdate,
                    'religion'      => $candidate->religion,
                    'marital'       => $candidate->marital,
                    'gender'        => $candidate->gender,
                    'hp'            => $candidate->phone,

                    'department_id' => $requisition->department_id,
                    'section_id'    => $requisition->section_id,
                    'area_id'       => $requisition->area_id,
                    'position_id'   => $requisition->position_id,
                    'joindate'      => $requisition->employment_date ?? now(),
                    'status'        => $employeeStatusEnum,
                ]);
                Log::create([
                    'user_id'     => Auth::id(),
                    'ip_address'  => $request->ip(),
                    'action'      => 'insert',
                    'description' => 'Create New Employee '.'"'.$candidate->fullname.'"',
                ]);
                $hiredCount++;
            }

            if ($hiredCount === 0) {
                DB::rollBack();
                if ($duplicateCount > 0) {
                    return response()->json(['message' => 'No new employees hired. Selected candidates are already active employees.'], 422);
                }
                return response()->json(['message' => 'No new employees were created.'], 422);
            }

            DB::commit();
            $message = "Successfully hired $hiredCount Candidate(s).";
            if ($duplicateCount > 0) {
                $message .= " ($duplicateCount candidates skipped because they are already active employees).";
            }
            return response()->json([
                'message' => $message
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'nik')) {
                return response()->json(['message' => 'Failed to generate a unique Employee ID (NIK). Please try again.'], 409);
            }
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
