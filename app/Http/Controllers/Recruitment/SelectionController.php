<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Position;
use App\Models\Recruitment\Candidate;
use App\Models\Recruitment\EmployeeRequisition;
use App\Models\Recruitment\EmployeeRequisitionHiringStep;
use App\Models\Recruitment\SelectionProcess;
use App\Models\Recruitment\SelectionProcessAssessment;
use App\Models\Recruitment\SelectionProcessCandidate;
use App\Models\Recruitment\SelectionProcessEmployee;
use App\Models\Section;
use App\Notifications\AccountNotification;
use App\Notifications\RecruitmentNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SelectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->get('status');
            $query = SelectionProcess::with([
                'requisition', 
                'hiringStep.masterHiring',
                'candidates', 
                'employees'
            ]);
            if ($status !== null && $status !== 'ALL') {
                $query->where('status', (int)$status);
            }
            return DataTables::of($query)
                ->editColumn('id', function ($row) {
                    $encryptedId = encrypt($row->id);
                    if ($row->status == 0) {
                        return '<input type="checkbox" class="row-checkbox" value="' . $encryptedId . '">';
                    } else {
                        return '<input type="checkbox" class="row-checkbox" disabled>';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at; 
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    $btn = '<a href="' . route('selection.detail', ['id' => $encryptedId]) . '" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                    if ($row->status === 0) {
                        $btn .= '<a href="' . route('selection.form', $encryptedId) . '" data-toggle="tooltip" title="Edit" class="btn btn-warning btn-sm edit-btn ms-1"><i class="ri-quill-pen-line"></i></a>';
                    }
                    if (Auth::user()->can('hrd.selection.delete') && ($row->status == 0)) {
                        $btn .= '<a href="#" data-id="' . $encryptedId . '" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-sm delete-btn ms-1"><i class="ri-delete-bin-line"></i></a>';
                    }
                    if ($row->status === 1) {
                        $btn .= '<a href="' . route('selection.passed', $encryptedId) . '" data-toggle="tooltip" title="Selection" class="btn btn-primary btn-sm edit-btn ms-1"><i class="ri-user-follow-line"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        0 => ['label' => 'DRAFT', 'class' => 'secondary'],
                        1 => ['label' => 'RELEASE', 'class' => 'primary'],
                        2 => ['label' => 'DONE', 'class' => 'success'],
                    ];
                    $info = $statusMap[$row->status] ?? ['label' => 'UNKNOWN', 'class' => 'danger'];
                    return "<span class=\"badge text-bg-{$info['class']}\">{$info['label']}</span>";
                })
                ->addColumn('requisition', function ($row) {
                    $requisition = optional($row->requisition);
                    $positionName = optional(optional($requisition)->position)->nama ?? 'N/A';
                    $sectionName = optional(optional($requisition)->section)->nama;
                    $noPengajuan = optional($requisition)->no_pengajuan ?? '';
                    if ($sectionName) {
                        return "{$positionName} {$sectionName} ({$noPengajuan})";
                    }
                    return "{$positionName} ({$noPengajuan})";
                })
                ->addColumn('selection', function ($row) {
                    $stepOrder = optional($row->hiringStep)->step_order;
                    $stepName = optional(optional($row->hiringStep)->masterHiring)->name ?? 'N/A';
                    $lastOrder = $row->requisition->hiringSteps()->max('step_order');
                    $output = "{$stepOrder} - {$stepName}";
                    if ($stepOrder == $lastOrder) {
                        $output .= ' (Last)';
                    }
                    return $output;
                })
                ->addColumn('noted', function ($row) {
                    return $row->noted ?? '-';
                })
                ->addColumn('participant', function ($row) {
                    $count = $row->candidates->count();
                    return "{$count} Candidates";
                })
                ->addColumn('schedule', function ($row) {
                    return $row->scheduled_at ? Carbon::parse($row->scheduled_at)->format('d/m/Y H:i') : '-';
                })
                ->addColumn('location', function ($row) {
                    return $row->location ?? '-';
                })
                ->addColumn('passed', function ($row) {
                    $count = $row->candidates->whereIn('result_status', [1, 3])->count();
                    return "{$count} Candidates";
                })
                ->rawColumns(['id', 'action', 'status'])
                ->make(true);
        }
        return view('pages.hrd.recruitment.selection.index');
    }

    public function getSelectionSteps($id)
    {
        try {
            $decryptedId = decrypt($id);
            $candidate = Candidate::with([
                'posting.requisition.hiringSteps' => function ($query) {
                    $query->orderBy('step_order', 'asc')
                        ->with('masterHiring');
                },
                'selections'
            ])->findOrFail($decryptedId);
            $candidate->selections->loadMissing('hiringStep.masterHiring');
            $steps = [];
            $steps[] = [
                'name' => 'Submit',
                'date' => $candidate->submit_date 
                    ? Carbon::parse($candidate->submit_date)->format('d M Y, H:i') 
                    : null,
                'status_class' => $candidate->submit_date != null ? 'completed' : '',
            ];
            $requisition = optional($candidate->posting)->requisition;
            if ($requisition) {
                $hiringSteps = $requisition->hiringSteps;
                $candidateProcessMap = $candidate->selections->keyBy('requisition_hiring_step_id');
                foreach ($hiringSteps as $hiringStep) {
                    $stepName = optional($hiringStep->masterHiring)->name ?? 'Unknown Step';
                    $dateDisplay = null;
                    $statusClass = '';
                    if ($candidateProcessMap->has($hiringStep->id)) {
                        $process = $candidateProcessMap->get($hiringStep->id);
                        $dateLabel = '';
                        $currentStatus = $process->pivot->result_status ?? $process->result_status;
                        if ($process->completed_at && in_array($currentStatus, [1, 3])) {
                            // PASS & COMPLETED
                            $displayDate = $process->completed_at;
                            $statusClass = 'completed'; 
                        } elseif ($process->completed_at && in_array($currentStatus, [2])) {
                            // NOT PASS
                            $displayDate = $process->completed_at;
                            $statusClass = 'failed';
                            $dateLabel = ' (Failed)';
                        } else {
                            // SCHEDULED
                            $displayDate = $process->scheduled_at;
                            $statusClass = ''; 
                            $dateLabel = ' (Schedule)';
                        }
                        if ($displayDate) {
                            $dateDisplay = Carbon::parse($displayDate)->format('d M Y, H:i') . $dateLabel;
                        }
                    }
                    $steps[] = [
                        'name' => $stepName,
                        'date' => $dateDisplay,
                        'status_class' => $statusClass,
                    ];
                }
            }
            return response()->json(['steps' => $steps]);
        } catch (DecryptException $e) {
            return response()->json(['error' => 'Invalid ID format.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Candidate not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An internal server error occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    public function form(string $id = null)
    {
        $selection = null;
        if ($id) {
            $id = decrypt($id);
            $selection = SelectionProcess::findOrFail($id);
        }
        $requisitions = EmployeeRequisition::where('decision', 'APPROVED')
                        ->whereNull('fulfilled_date')
                        ->whereHas('jobPosting', function ($query) {
                            $query->whereNotNull('publish_date')
                                ->whereNotNull('publish_id');
                        })
                        ->get();
        $employees = Employee::with('position')->whereNot('status', 'TERMINATED')->get();
        return view('pages.hrd.recruitment.selection.form', compact('selection','requisitions','employees'));
    }

    public function getSteps(EmployeeRequisition $requisition)
    {
        $steps = $requisition->hiringSteps()
            ->with('masterHiring')
            ->orderBy('step_order', 'asc')
            ->get();
        return response()->json($steps->map(function ($step) {
            return [
                'id' => $step->id,
                'text' => "{$step->step_order} - " . optional($step->masterHiring)->name
            ];
        }));
    }

    public function getCandidates(Request $request)
    {
        $requisitionId = $request->input('requisition_id'); 
        $stepId = $request->input('step_id');
        $selectionId = $request->input('id');
        $candidatesData = collect([]);

        // EDIT
        if ($selectionId) {
            $selection = SelectionProcess::find($selectionId);
            if ($selection) {
                $candidates = $selection->selectionCandidates()->with('candidate.posting', 'candidate.experiences', 'candidate.educations')->get();
                $candidatesData = $candidates->map(function($selCandidate) {
                    $row = $selCandidate->candidate;
                    if (!$row) return null;

                    $action_btn = '<button type="button" class="btn btn-danger btn-sm remove-candidate" data-id="' . $row->id . '"><i class="ri-delete-bin-line"></i></button>';
                    
                    // Age
                    $birthDate = $row->birthdate ? Carbon::parse($row->birthdate) : null;
                    $age = $birthDate ? $birthDate->diff(Carbon::now())->format('%y Years') : '-';
                    
                    // Education
                    $edu_output = '';
                    $educations = $row->educations->sortByDesc('end_year'); 
                    if ($educations->count() === 1) {
                        $edu_output = optional($educations->first())->institution_name ?? '-';
                    } else {
                        foreach ($educations as $index => $education) {
                            $nomor = $index + 1;
                            $institution = $education->institution_name;
                            $displayEdu = (!empty($institution) && $institution !== '-') ? $institution : '-';
                            $edu_output .= "{$nomor}. {$displayEdu}";
                            if ($index < $educations->count() - 1) { $edu_output .= '<br>'; }
                        }
                    }

                    // Experience
                    $exp_output = '';
                    $pos_output = '';
                    $comp_output = '';
                    $experiences = $row->experiences->sortByDesc('end_date'); 
                    if ($experiences->count() === 1) {
                        $experience = $experiences->first();
                        $exp_output = optional($experience)->years ? optional($experience)->years . ' Years' : '-';
                        $pos_output = optional($experience)->position ?? '-';
                        $comp_output = optional($experience)->company ?? '-';
                    } else {
                        foreach ($experiences as $index => $experience) {
                            $nomor = $index + 1;
                            $year = $experience->years;
                            $position = $experience->position;
                            $company = $experience->company;
                            $displayYear = (!empty($year) && $year !== '-') ? "{$year} Years" : '-';
                            $displayPosition = (!empty($position) && $position !== '-') ? $position : '-';
                            $displayCompany = (!empty($company) && $company !== '-') ? $company : '-';
                            
                            $exp_output .= "{$nomor}. {$displayYear}";
                            $pos_output .= "{$nomor}. {$displayPosition}";
                            $comp_output .= "{$nomor}. {$displayCompany}";
                            if ($index < $experiences->count() - 1) { 
                                $exp_output .= '<br>'; 
                                $pos_output .= '<br>'; 
                                $comp_output .= '<br>'; 
                            }
                        }
                    }
                    
                    return [
                        'id' => $row->id,
                        'created_at_ts' => optional($row->created_at)->timestamp ?? time(),
                        'action' => $action_btn,
                        'fullname' => $row->fullname ?? '-',
                        'age' => $age,
                        'edu' => $edu_output ?: '-',
                        'years_exp' => $exp_output ?: '-',
                        'position' => $pos_output ?: '-',
                        'company' => $comp_output ?: '-',
                        'skill' => $row->skill ?? '-',
                    ];
                })->filter()->values();
                
                return response()->json(['data' => $candidatesData]);
            }
        }

        // CREATE
        if ($requisitionId && $stepId) {
            $currentStep = EmployeeRequisitionHiringStep::find($stepId);
            if (!$currentStep) {
                return response()->json(['data' => []]);
            }
            $candidatesInCurrentStepIds = SelectionProcessCandidate::whereHas('selectionProcess', function ($query) use ($requisitionId, $stepId) {
                $query->where('requisition_id', $requisitionId)
                      ->where('requisition_hiring_step_id', $stepId);
            })->pluck('candidate_id')->toArray();
            $candidateQuery = Candidate::with(['posting', 'experiences', 'educations']);
            if ($currentStep->step_order == 1) {
                $candidateQuery->whereHas('posting', function ($query) use ($requisitionId) {
                    $query->where('requisition_id', $requisitionId);
                });
            } else {
                $prevStepOrder = $currentStep->step_order - 1;
                $prevStep = EmployeeRequisitionHiringStep::where('requisition_id', $requisitionId)
                    ->where('step_order', $prevStepOrder)
                    ->first();
                if ($prevStep) {
                    $passedCandidateIds = SelectionProcessCandidate::whereHas('selectionProcess', function($q) use ($prevStep) {
                            $q->where('requisition_hiring_step_id', $prevStep->id)
                              ->where('status', 2);
                        })
                        ->where('result_status', 1)
                        ->pluck('candidate_id')
                        ->toArray();
                    $candidateQuery->whereIn('id', $passedCandidateIds);
                } else {
                    return response()->json(['data' => []]);
                }
            }
            $candidates = $candidateQuery->whereNotIn('id', $candidatesInCurrentStepIds)->get();
            $candidatesData = $candidates->map(function($row) {
                $action_btn = '<button type="button" class="btn btn-primary btn-sm add-candidate" data-id="' . $row->id . '"><i class="ri-add-line"></i> Add</button>';
                
                // Age
                $birthDate = $row->birthdate ? Carbon::parse($row->birthdate) : null;
                $age = $birthDate ? $birthDate->diff(Carbon::now())->format('%y Years') : '-';
                
                // Education
                $edu_output = '';
                $educations = $row->educations->sortByDesc('end_year'); 
                if ($educations->count() === 1) {
                    $edu_output = optional($educations->first())->institution_name ?? '-';
                } else {
                    foreach ($educations as $index => $education) {
                        $nomor = $index + 1;
                        $institution = $education->institution_name;
                        $displayEdu = (!empty($institution) && $institution !== '-') ? $institution : '-';
                        $edu_output .= "{$nomor}. {$displayEdu}";
                        if ($index < $educations->count() - 1) { $edu_output .= '<br>'; }
                    }
                }

                // Experience
                $exp_output = '';
                $pos_output = '';
                $comp_output = '';
                $experiences = $row->experiences->sortByDesc('end_date'); 
                if ($experiences->count() === 1) {
                    $experience = $experiences->first();
                    $exp_output = optional($experience)->years ? optional($experience)->years . ' Years' : '-';
                    $pos_output = optional($experience)->position ?? '-';
                    $comp_output = optional($experience)->company ?? '-';
                } else {
                    foreach ($experiences as $index => $experience) {
                        $nomor = $index + 1;
                        $year = $experience->years;
                        $position = $experience->position;
                        $company = $experience->company;
                        $displayYear = (!empty($year) && $year !== '-') ? "{$year} Years" : '-';
                        $displayPosition = (!empty($position) && $position !== '-') ? $position : '-';
                        $displayCompany = (!empty($company) && $company !== '-') ? $company : '-';
                        
                        $exp_output .= "{$nomor}. {$displayYear}";
                        $pos_output .= "{$nomor}. {$displayPosition}";
                        $comp_output .= "{$nomor}. {$displayCompany}";
                        if ($index < $experiences->count() - 1) { 
                            $exp_output .= '<br>'; 
                            $pos_output .= '<br>'; 
                            $comp_output .= '<br>'; 
                        }
                    }
                }

                return [
                    'id' => $row->id,
                    'created_at_ts' => optional($row->created_at)->timestamp ?? time(),
                    'action' => $action_btn,
                    'fullname' => $row->fullname ?? '-',
                    'age' => $age,
                    'edu' => $edu_output ?: '-',
                    'years_exp' => $exp_output ?: '-',
                    'position' => $pos_output ?: '-',
                    'company' => $comp_output ?: '-',
                    'skill' => $row->skill ?? '-',
                ];
            });
            return response()->json(['data' => $candidatesData]);
        }
        return response()->json(['data' => ['Unknown Param']]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'requisition_id' => 'required|exists:employee_requisition,id',
            'requisition_hiring_step_id' => 'required|exists:employee_requisition_hiring_steps,id',
            'location' => 'required|string|max:255',
            'scheduled_at' => 'required|date_format:d/m/Y H:i', 
            'noted' => 'nullable|string|max:255',
            'candidate_ids' => 'required|json', 
            'status' => 'required|integer|in:0,1',
            'invited_employees' => 'nullable|array',
            'invited_employees.*' => 'exists:employees,id',
        ];

        $messages = [
            'requisition_id.required' => 'The Requisition field is required.',
            'requisition_hiring_step_id.required' => 'The Step Selection field is required.',
            'location.required' => 'The Location field is required.',
            'scheduled_at.required' => 'The Schedule field is required.',
            'scheduled_at.date_format' => 'The Schedule date format must be DD/MM/YYYY HH:MM (e.g., 31/12/2025 14:30).',
            'candidate_ids.required' => 'Please add at least one candidate to the selection list.',
            'status.required' => 'The process status is missing.',
        ];

        $isUpdate = $request->filled('id');
        if ($isUpdate) {
            $selection = SelectionProcess::find($request->input('id'));
            if ($selection) {
                unset($rules['requisition_id']);
                unset($rules['requisition_hiring_step_id']);
            }
        }
        
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed. Please correct the highlighted errors.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $candidateIdsArray = json_decode($validated['candidate_ids'], true);
        $status = (int)$validated['status'];
        
        try {
            $scheduledAt = Carbon::createFromFormat('d/m/Y H:i', $request->input('scheduled_at'));
        } catch (Exception $e) {
             return response()->json([
                'message' => 'Invalid schedule date/time format. Please use DD/MM/YYYY HH:MM.',
                'errors' => ['scheduled_at' => ['The scheduled date/time format is incorrect.']],
            ], 422);
        }

        DB::beginTransaction();

        try {
            $action = $isUpdate ? 'update' : 'insert';
            $selectionId = $request->input('id');
            $description = '';
            $statusValue = (int)$validated['status'];
            $statusLabel = ($statusValue === 1) ? 'RELEASE' : 'DRAFT';
            $stepName = 'NA';
            $reqNumber = 'NA';
            if ($isUpdate) {
                // --- EDIT / UPDATE ---
                $selection = $selection ?? SelectionProcess::find($selectionId);
                if ($selection->status > 0 && $status == 0) {
                     DB::rollBack();
                     return response()->json([
                        'message' => 'Failed to save. A released selection process cannot be reverted to DRAFT.',
                        'errors' => ['status' => ['Status change not allowed. This process is already released or done.']],
                    ], 422);
                }
                $updateData = [
                    'location' => $validated['location'],
                    'scheduled_at' => $scheduledAt,
                    'noted' => $validated['noted'],
                    'status' => $statusValue,
                ];
                $selection->update($updateData);
                $selection->load('requisition', 'hiringStep.masterHiring');
                $stepName = optional(optional($selection->hiringStep)->masterHiring)->name ?? 'NA';
                $reqNumber = optional($selection->requisition)->no_pengajuan ?? 'NA';
                $description = "Update Selection : {$stepName} [{$reqNumber}] with status : {$statusLabel}";
            } else {
                // --- CREATE / INSERT ---
                $createData = [
                    'requisition_id' => $validated['requisition_id'],
                    'requisition_hiring_step_id' => $validated['requisition_hiring_step_id'],
                    'location' => $validated['location'],
                    'scheduled_at' => $scheduledAt,
                    'noted' => $validated['noted'],
                    'status' => $statusValue,
                ];
                $selection = SelectionProcess::create($createData);
                $selection->load('requisition', 'hiringStep.masterHiring');
                $stepName = optional(optional($selection->hiringStep)->masterHiring)->name ?? 'NA';
                $reqNumber = optional($selection->requisition)->no_pengajuan ?? 'NA';
                $description = "Create New Selection : {$stepName} [{$reqNumber}] with status : {$statusLabel}";
            }

            // Sync Candidate
            SelectionProcessCandidate::where('selection_process_id', $selection->id)->delete();            
            $candidatesToInsert = collect($candidateIdsArray)->map(function ($candidateId) use ($selection) {
                return [
                    'candidate_id' => $candidateId,
                    'selection_process_id' => $selection->id,
                    'result_status' => 0, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            if (!empty($candidatesToInsert)) {
                SelectionProcessCandidate::insert($candidatesToInsert);
            }

            // Sync Employee
            $employeesInput = $request->input('invited_employees', []);
            $employeesInput = array_filter($employeesInput);
            SelectionProcessEmployee::where('selection_process_id', $selection->id)->delete();
            $employeesToInsert = [];
            foreach ($employeesInput as $empId) {
                $employeesToInsert[] = [
                    'selection_process_id' => $selection->id,
                    'employee_id'          => $empId,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
            }
            if (!empty($employeesToInsert)) {
                SelectionProcessEmployee::insert($employeesToInsert);
            }

            // Notif
            if ($statusValue === 1) {
                // Candidate
                $extraData = [
                    'selection' => $stepName, 
                    'location'  => $selection->location,
                    'schedule'  => $selection->scheduled_at, 
                ];
                $candidatesToNotify = Candidate::with('posting')
                    ->whereIn('id', $candidateIdsArray)
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->get();
                $defaultPositionName = optional(optional($selection->requisition)->jobPosting)->title 
                                        ?? optional(optional($selection->requisition)->position)->nama 
                                        ?? 'Unknown Position';
                foreach ($candidatesToNotify as $candidate) {
                    if (!filter_var($candidate->email, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }
                    $candidateData = [
                        'nickname' => $candidate->nickname ?? $candidate->fullname,
                        'no_ktp'   => $candidate->no_ktp,
                    ];
                    $positionName = optional($candidate->posting)->title ?? $defaultPositionName;
                    $candidate->notify(new RecruitmentNotification(
                        $candidateData,
                        $positionName,
                        'schedule', 
                        $extraData 
                    ));
                    SelectionProcessCandidate::where('selection_process_id', $selection->id)
                    ->where('candidate_id', $candidate->id)
                    ->update([
                        'email_notification_sent_at' => now()
                    ]);
                }

                // Employee
                $invitedEmployeeIds = $request->input('invited_employees', []);
                $invitedEmployeeIds = array_filter($invitedEmployeeIds);
                if (!empty($invitedEmployeeIds)) {
                    $employeesToNotify = Employee::with('user')
                        ->whereIn('id', $invitedEmployeeIds)
                        ->whereHas('user')
                        ->get();
                    foreach ($employeesToNotify as $employee) {
                        $recipientUser = $employee->user;
                        if (!$recipientUser || !filter_var($recipientUser->email, FILTER_VALIDATE_EMAIL)) {
                            continue; 
                        }
                        if ($recipientUser && !empty($recipientUser->email)) {
                            $actionURL = $recipientUser->hasPermissionTo('emp.menu')
                                ? route('recruitment.emp.index', ['tab_process_selection'])
                                : route('recruitment.profile.index', ['tab_process_selection']);
                            $details = [
                                'greeting' => 'Hi ' . optional($recipientUser->employee)->fullname ?? 'Interviewer',
                                'subject' => 'Recruitment Notification',
                                'body' => 'We would like to inform you that new Selection Process "' . $stepName . '" requires your attention.',
                                'actionText' => 'Please Login',
                                'actionURL' => $actionURL,
                                'thanks' => 'Thank you for your attention!!'
                            ];
                            $recipientUser->notify(new AccountNotification($details));
                        }
                    }
                }
            }

            // Log
            if ($user) {
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => $action,
                    'description' => $description,
                ]);
            }
            
            DB::commit();
            $message = 'Selection Process has been saved.';
            return response()->json([
                'message' => $message,
                'redirect' => route('selection.index'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An unexpected error occurred while processing the selection. Please contact support.',
                'errors' => ['general' => ['Database Error: ' . $e->getMessage()]],
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $encryptedIds = $request->input('ids') ?? [$request->input('id')];
        $decryptedIds = array_map(function ($id) {
            if (empty($id)) {
                return null;
            }
            try {
                return decrypt($id); 
            } catch (DecryptException $e) {
                return null;
            }
        }, $encryptedIds);
        $decryptedIds = array_filter($decryptedIds);
        if (empty($decryptedIds)) {
            return redirect()->back()->with('error', 'Error: No valid selection IDs were provided for deletion.');
        }
        $user = auth()->user();
        $deletedCount = 0;
        $failedCount = 0;
        DB::beginTransaction();
        try {
            $selections = SelectionProcess::whereIn('id', $decryptedIds)
                            ->with('hiringStep.masterHiring', 'requisition')
                            ->get();
            foreach ($selections as $selection) {
                $statusLabel = ($selection->status === 1) ? 'RELEASE' : (($selection->status === 2) ? 'DONE' : 'DRAFT');
                $stepName = optional(optional($selection->hiringStep)->masterHiring)->name ?? 'NA';
                $reqNumber = optional($selection->requisition)->no_pengajuan ?? 'NA';
                if ($selection->status == 0) {
                    $selection->delete();
                    $deletedCount++;
                    if ($user) {
                        $description = "Delete Selection : {$stepName} [{$reqNumber}] with status : {$statusLabel}";
                        Log::create([
                            'user_id' => $user->id,
                            'ip_address' => $request->ip(),
                            'action' => 'delete',
                            'description' => $description,
                        ]);
                    }
                } else {
                    $failedCount++;
                }
            }
            DB::commit();
            $successMessage = "{$deletedCount} Selection successfully deleted.";
            if ($failedCount > 0) {
                $errorMessage = "Warning: {$failedCount} selection(s) failed to delete because they were not in DRAFT status.";
                return redirect()->back()->with('warning', $successMessage . ' ' . $errorMessage);
            }
            return redirect()->back()->with('success', $successMessage);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete Selection Process(es): ' . $e->getMessage());
        }
    }

    public function getProcessSelection(Request $request)
    {
        if ($request->ajax()) {
            $employeeId = auth()->user()->employee_id;
            $query = SelectionProcess::with([
                'requisition.position',
                'requisition.section',
                'hiringStep.masterHiring',
                'candidates', 
                'employees',
                'candidates.assessments' => function($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
                }
            ])
            ->where('status', 1)
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId)
                ->whereNull('completed_at'); 
            });
            return DataTables::of($query)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at; 
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $token = encrypt($row->id . '|' . $user->employee_id); 
                    $isTime = $row->scheduled_at && Carbon::now()->greaterThanOrEqualTo(Carbon::parse($row->scheduled_at));
                    $routePrefix = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.selection.review' : 'recruitment.profile.selection.review';
                    $btn = '-';
                    if ($isTime) {
                        $btn = '<a href="'.route($routePrefix, $token).'" data-toggle="tooltip" title="Review" class="btn btn-success btn-sm"><i class="ri-quill-pen-line"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $isTime = $row->scheduled_at && Carbon::now()->greaterThanOrEqualTo(Carbon::parse($row->scheduled_at));
                    if ($isTime) {
                        return '<span class="badge text-bg-success">ONGOING</span>';
                    } else {
                        return '<span class="badge text-bg-primary">UPCOMING</span>';
                    }
                })
                ->addColumn('requisition', function ($row) {
                    $requisition = optional($row->requisition);
                    $positionName = optional(optional($requisition)->position)->nama ?? 'N/A';
                    $sectionName = optional(optional($requisition)->section)->nama;
                    $noPengajuan = optional($requisition)->no_pengajuan ?? '';
                    if ($sectionName) {
                        return "{$positionName} {$sectionName} ({$noPengajuan})";
                    }
                    return "{$positionName} ({$noPengajuan})";
                })
                ->addColumn('selection', function ($row) {
                    $stepOrder = optional($row->hiringStep)->step_order;
                    $stepName = optional(optional($row->hiringStep)->masterHiring)->name ?? 'N/A';
                    return "{$stepOrder} - {$stepName}";
                })
                ->addColumn('noted', function ($row) {
                    return $row->noted ?? '-';
                })
                ->addColumn('participant', function ($row) {
                    $count = $row->candidates->count();
                    return "{$count} Candidates";
                })
                ->addColumn('schedule', function ($row) {
                    return $row->scheduled_at ? Carbon::parse($row->scheduled_at)->format('d/m/Y H:i') : '-';
                })
                ->addColumn('location', function ($row) {
                    return $row->location ?? '-';
                })
                ->addColumn('passed', function ($row) use ($employeeId) {
                    $passedCount = 0;
                    foreach ($row->candidates as $candidate) {
                        $myAssessment = $candidate->assessments->first();
                        if ($myAssessment && $myAssessment->result_status == 1) {
                            $passedCount++;
                        }
                    }
                    return "{$passedCount} Candidates";
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        abort(404);
    }

    public function countProcessSelection()
    {
        $employeeId = auth()->user()->employee_id;
        $count = SelectionProcess::where('status', 1)
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId)
                ->whereNull('completed_at');
            })
            ->count();
        return response()->json([
            'jml_review' => $count
        ]);
    }

    public function getDoneSelection(Request $request)
    {
        if ($request->ajax()) {
            $employeeId = auth()->user()->employee_id;
            $query = SelectionProcess::with([
                'requisition.position',
                'requisition.section',
                'hiringStep.masterHiring',
                'candidates', 
                'employees',
                'candidates.assessments' => function($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
                }
            ])
            ->whereIn('status', [1, 2]) 
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId)
                ->whereNotNull('completed_at');
            });

            return DataTables::of($query)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at; 
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $token = encrypt($row->id . '|' . $user->employee_id);
                    $routePrefix = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.selection.detail' : 'recruitment.profile.selection.detail';
                    $btn = '<a href="'.route($routePrefix, $token).'" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        1 => ['label' => 'ONGOING', 'class' => 'success'],
                        2 => ['label' => 'DONE', 'class' => 'success'],
                    ];
                    $info = $statusMap[$row->status] ?? ['label' => 'UNKNOWN', 'class' => 'danger'];
                    return "<span class=\"badge text-bg-{$info['class']}\">{$info['label']}</span>";
                })
                ->addColumn('requisition', function ($row) {
                    $requisition = optional($row->requisition);
                    $positionName = optional(optional($requisition)->position)->nama ?? 'N/A';
                    $sectionName = optional(optional($requisition)->section)->nama;
                    $noPengajuan = optional($requisition)->no_pengajuan ?? '';
                    if ($sectionName) {
                        return "{$positionName} {$sectionName} ({$noPengajuan})";
                    }
                    return "{$positionName} ({$noPengajuan})";
                })
                ->addColumn('selection', function ($row) {
                    $stepOrder = optional($row->hiringStep)->step_order;
                    $stepName = optional(optional($row->hiringStep)->masterHiring)->name ?? 'N/A';
                    return "{$stepOrder} - {$stepName}";
                })
                ->addColumn('noted', function ($row) {
                    return $row->noted ?? '-';
                })
                ->addColumn('participant', function ($row) {
                    $count = $row->candidates->count();
                    return "{$count} Candidates";
                })
                ->addColumn('schedule', function ($row) {
                    return $row->scheduled_at ? Carbon::parse($row->scheduled_at)->format('d/m/Y H:i') : '-';
                })
                ->addColumn('location', function ($row) {
                    return $row->location ?? '-';
                })
                ->addColumn('passed', function ($row) use ($employeeId) {
                    $passedCount = 0;
                    if ($row->status == 2) {
                        $passedCount = $row->candidates->whereIn('result_status', [1,3])->count();
                    } else {
                        foreach ($row->candidates as $candidate) {
                            $myAssessment = $candidate->assessments->first();
                            if ($myAssessment && $myAssessment->result_status == 1) {
                                $passedCount++;
                            }
                        }
                    }
                    return "{$passedCount} Candidates";
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        abort(404);
    }

    public function passed(string $id)
    {
        $selection = null;
        if ($id) {
            $id = decrypt($id);
            $selection = SelectionProcess::findOrFail($id);
            if (in_array($selection->status, [0,2])) {
                abort(403, 'This Selection is not available for review.');
            }
        }
        return view('pages.hrd.recruitment.selection.passed', compact('selection'));
    }

    public function passed_store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'id' => 'required|exists:selection_process,id',
            'candidates_grading' => 'required|json',
            'process_status' => 'required|in:1,2',
        ]);
        try {
            DB::beginTransaction();
            $selectionId = $request->id;
            $gradingData = json_decode($request->candidates_grading, true);
            foreach ($gradingData as $item) {
                $candidateId = $item['candidate_id'];
                $isPassed = $item['is_passed'];
                $comment = $item['comment'];
                $isPresent   = $item['is_present'] ?? 0;
                $resultStatus = ($isPassed == 1) ? 1 : 2;
                SelectionProcessCandidate::updateOrCreate(
                    [
                        'selection_process_id' => $selectionId,
                        'candidate_id' => $candidateId
                    ],
                    [
                        'result_status' => $resultStatus,
                        'comment' => $comment,
                        'is_present' => $isPresent,
                    ]
                );
            }

            $selection = SelectionProcess::findOrFail($selectionId);
            $selection->load('requisition', 'hiringStep.masterHiring');
            $stepName = optional(optional($selection->hiringStep)->masterHiring)->name ?? 'NA';
            $reqNumber = optional($selection->requisition)->no_pengajuan ?? 'NA';
            $isFulfilled = false;
            if ($request->process_status == 2) {
                $selection->status = 2;
                $selection->completed_at = now();
                $selection->save();
                $isFulfilled = $this->checkAndAutoCloseRequisition($selection->requisition_id);
                $description = "Closing Selection : {$stepName} [{$reqNumber}]";
                $action = 'closing';
                $message = 'Selection has been Closing.';
            } else {
                $description = "Updating Selection : {$stepName} [{$reqNumber}]";
                $action = 'update';
                $message = 'Selection has been Saved.';
            }
            if ($isFulfilled) {
                $message .= ' Requisition is now Fulfilled.';
            }
            // Log
            if ($user) {
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => $action,
                    'description' => $description,
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => $message,
                'redirect' => route('selection.index'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function review($token)
    {
        try {
            [$id, $empId] = explode('|', decrypt($token));
        } catch (Exception $e) {
            abort(404, 'Invalid or expired link.');
        }
        $user = auth()->user();
        $selection = SelectionProcess::findOrFail($id);
        $loggedInEmployeeId = Auth::user()->employee_id;
        if ($loggedInEmployeeId != $empId) {
            abort(403, 'You are not authorized to view this Selection.');
        }
        if (in_array($selection->status, [0,2])) {
            abort(403, 'This Selection is not available for review.');
        }
        $viewPath = ($user->hasPermissionTo('emp.menu'))
            ? 'pages.employee.recruitment.selection.review'
            : 'pages.profile.recruitment.selection.review';
        return view($viewPath, compact('selection','user','token','loggedInEmployeeId'));
    }

    public function review_store(Request $request, $token)
    {
        try {
            [$id, $empId] = explode('|', decrypt($token));
        } catch (Exception $e) {
            abort(404, 'Invalid or expired link.');
        }

        $user = auth()->user();
        $loggedInEmployeeId = $user->employee_id ?? null;
        if ($loggedInEmployeeId != $empId) {
            abort(403, 'You are not authorized to review this Selection.');
        }

        $selection = SelectionProcess::findOrFail($id);
        if (!in_array($selection->status, [1])) { 
            return response()->json(['message' => 'This Selection is not open for review.'], 403);
        }

        $request->validate([
            'id' => 'required|exists:selection_process,id',
            'candidates_grading' => 'required|json',
            'process_status' => 'required|in:1,2',
        ]);

        try {
            DB::beginTransaction();
            $selectionId = $request->id;
            $gradingData = json_decode($request->candidates_grading, true);
            foreach ($gradingData as $item) {
                $candidateId = $item['candidate_id'];
                $isPassed    = $item['is_passed'];
                $comment     = $item['comment'];
                $assessmentStatus = ($isPassed == 1) ? 1 : 2;
                $pivotCandidate = SelectionProcessCandidate::where('selection_process_id', $selectionId)
                                    ->where('candidate_id', $candidateId)
                                    ->first();
                if ($pivotCandidate) {
                    SelectionProcessAssessment::updateOrCreate(
                        [
                            'sel_process_candidate_id' => $pivotCandidate->id,
                            'employee_id'              => $loggedInEmployeeId
                        ],
                        [
                            'result_status' => $assessmentStatus,
                            'comment'       => $comment
                        ]
                    );
                }
            }

            $action = 'update';
            $tab = 'tab_process_selection';
            if ($request->process_status == 2) {
                SelectionProcessEmployee::where('selection_process_id', $selectionId)
                    ->where('employee_id', $loggedInEmployeeId)
                    ->update(['completed_at' => now()]);
                $message = 'Selection has been submitted successfully.';
                $tab = 'tab_done_selection';
            } else {
                $message = 'Selection saved as Draft.';
                $tab = 'tab_process_selection';
            }

            $selection->load('requisition', 'hiringStep.masterHiring');
            $stepName  = optional(optional($selection->hiringStep)->masterHiring)->name ?? 'NA';
            $reqNumber = optional($selection->requisition)->no_pengajuan ?? 'NA';
            $description = "Assessment Candidate Selection : {$stepName} [{$reqNumber}]";
            
            Log::create([
                'user_id'      => $user->id,
                'ip_address'   => $request->ip(),
                'action'       => $action,
                'description'  => $description,
            ]);

            DB::commit();
            $redirectRoute = ($user->hasPermissionTo('emp.menu')) ? 'recruitment.emp.index' : 'recruitment.profile.index';
            return response()->json([
                'message'  => $message,
                'redirect' => route($redirectRoute, [$tab])
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAttendance(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'selection_id' => 'required|exists:selection_process,id',
            'candidates'   => 'required|array',
            'candidates.*.candidate_id' => 'required',
            'candidates.*.is_present'   => 'required|boolean',
        ]);
        try {
            DB::beginTransaction();
            foreach ($request->candidates as $item) {
                SelectionProcessCandidate::where('selection_process_id', $request->selection_id)
                    ->where('candidate_id', $item['candidate_id'])
                    ->update(['is_present' => $item['is_present']]);
            }

            $selection = selectionProcess::findOrFail($request->selection_id);
            $selection->load('requisition', 'hiringStep.masterHiring');
            $stepName  = optional(optional($selection->hiringStep)->masterHiring)->name ?? 'NA';
            $reqNumber = optional($selection->requisition)->no_pengajuan ?? 'NA';
            $description = "Updating Candidate Selection : {$stepName} [{$reqNumber}]";
            Log::create([
                'user_id'      => $user->id,
                'ip_address'   => $request->ip(),
                'action'       => 'update',
                'description'  => $description,
            ]);
            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function detail(string $id)
    {
        $id = decrypt($id);
        $selection = SelectionProcess::findOrFail($id);
        return view('pages.hrd.recruitment.selection.detail', compact('selection'));
    }

    public function profile_detail($token)
    {
        try {
            [$id, $empId] = explode('|', decrypt($token));
        } catch (Exception $e) {
            abort(404, 'Invalid or expired link.');
        }
        $user = auth()->user();
        $selection = SelectionProcess::findOrFail($id);
        $loggedInEmployeeId = Auth::user()->employee_id;
        if ($loggedInEmployeeId != $empId) {
            abort(403, 'You are not authorized to view this Selection.');
        }
        if (in_array($selection->status, [0])) {
            abort(403, 'This Selection is not available for Detail.');
        }
        $viewPath = ($user->hasPermissionTo('emp.menu'))
            ? 'pages.employee.recruitment.selection.detail'
            : 'pages.profile.recruitment.selection.detail';
        return view($viewPath, compact('selection','user','loggedInEmployeeId'));
    }

    public function upload_attachment(Request $request)
    {
        $request->validate([
            'selection_id' => 'required',
            'candidate_id' => 'required',
            'attachment'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        try {
            $candidate = Candidate::findOrFail($request->candidate_id);
            $pivotData = SelectionProcessCandidate::where('selection_process_id', $request->selection_id)
                            ->where('candidate_id', $request->candidate_id)
                            ->firstOrFail();
            if ($request->hasFile('attachment')) {
                $actionWord = !empty($pivotData->attachment) ? 'Replace' : 'Upload';
                $file = $request->file('attachment');
                if ($pivotData->attachment) {
                    if (Storage::disk('public')->exists('candidates/selection/' . $pivotData->attachment)) {
                        Storage::disk('public')->delete('candidates/selection/' . $pivotData->attachment);
                    }
                }
                $extension = $file->getClientOriginalExtension();
                $filename = $candidate->no_ktp . '_selection_' . time() . '_' . uniqid() . '.' . $extension;
                $path = $file->storeAs('candidates/selection', $filename, 'public');
                $pivotData->attachment = $filename;
                $pivotData->save();
                $selectionProcess = SelectionProcess::with('hiringStep.masterHiring')->find($request->selection_id);
                $selectionName = $selectionProcess->hiringStep->masterHiring->name ?? 'Unknown Step';
                $description = "{$actionWord} Attachment Selection [{$selectionName}] for Candidate : {$candidate->fullname} [{$candidate->no_ktp}]";
                Log::create([
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'action' => 'update',
                    'description' => $description,
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Attachment has been uploaded successfully.',
                    'filename' => $filename,
                    'path' => $path
                ], 200);
            }
            return response()->json(['message' => 'File not found.'], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An internal server error occurred. Please try again later.'
            ], 500);
        }
    }

    public function delete_attachment(Request $request)
    {
        $request->validate([
            'selection_id' => 'required',
            'candidate_id' => 'required',
        ]);
        try {
            $candidate = Candidate::findOrFail($request->candidate_id);
            $pivotData = SelectionProcessCandidate::where('selection_process_id', $request->selection_id)
                            ->where('candidate_id', $request->candidate_id)
                            ->firstOrFail();
            if ($pivotData->attachment) {
                if (Storage::disk('public')->exists('candidates/selection/' . $pivotData->attachment)) {
                    Storage::disk('public')->delete('candidates/selection/' . $pivotData->attachment);
                }
                $pivotData->attachment = null;
                $pivotData->save();
                $selectionProcess = SelectionProcess::with('hiringStep.masterHiring')->find($request->selection_id);
                $selectionName = $selectionProcess->hiringStep->masterHiring->name ?? 'Unknown Step';
                $description = "Delete Attachment Selection [{$selectionName}] for Candidate : {$candidate->fullname} [{$candidate->no_ktp}]";
                Log::create([
                    'user_id'     => Auth::id(),
                    'ip_address'  => $request->ip(),
                    'action'      => 'delete',
                    'description' => $description,
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Attachment has been deleted successfully.'
                ], 200);
            }
            return response()->json(['message' => 'No attachment found to delete.'], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An internal server error occurred. Please try again later.'
            ], 500);
        }
    }

    public function getResult(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $loggedInEmployeeId = $user->employee_id;
            $routePrefix = $user->hasPermissionTo('emp.menu') ? 'recruitment.emp.result.' : 'recruitment.profile.result.';
            $data = EmployeeRequisition::query()
                ->select([
                    'employee_requisition.*',
                    'pos.nama as position_name',
                    'dept.name as department_name',
                    'sect.nama as section_name',
                    'ar.name as area_name'
                ])
                ->leftJoin('master_position as pos', 'pos.id', '=', 'employee_requisition.position_id')
                ->leftJoin('departments as dept', 'dept.id', '=', 'employee_requisition.department_id')
                ->leftJoin('master_section as sect', 'sect.id', '=', 'employee_requisition.section_id')
                ->leftJoin('areas as ar', 'ar.id', '=', 'employee_requisition.area_id')
                ->with(['hiringSteps.selectionProcesses.candidates'])
                ->where('employee_requisition.applicant_id', $loggedInEmployeeId)
                ->where('employee_requisition.status', 'DONE')
                ->where('employee_requisition.decision', 'APPROVED')
                ->orderBy('employee_requisition.created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id', fn($row) => encrypt($row->id))
                ->addColumn('requisition', function ($row) {
                    $positionName = optional($row->position)->nama ?? 'N/A';
                    $sectionName = optional($row->section)->nama;
                    $noPengajuan = $row->no_pengajuan ?? '';
                    if ($sectionName) {
                        return "{$positionName} {$sectionName} ({$noPengajuan})";
                    }
                    return "{$positionName} ({$noPengajuan})";
                })
                ->editColumn('needs', fn($row) => ($row->needs ?? 0) . ' Candidates')
                ->addColumn('fulfilled', function ($row) {
                    $lastStep = $row->hiringSteps->sortByDesc('step_order')->first();
                    if (!$lastStep) return '0 Candidates';
                    $allBatches = $lastStep->selectionProcesses;
                    $totalPassed = $allBatches->sum(function ($batch) {
                        return $batch->candidates->where('result_status', 1)->count();
                    });
                    return $totalPassed . ' Candidates';
                })
                ->addColumn('employee_status', fn($row) => $row->employee_status ?? '-')
                ->editColumn('reason', fn($row) => $row->fulfilled_reason ?? '-')
                ->editColumn('status', function ($row) {
                    if (!is_null($row->fulfilled_date)) {
                        return '<span class="badge text-bg-success">CLOSE</span>';
                    } else {
                        return '<span class="badge text-bg-primary">OPEN</span>';
                    }
                })
                ->addColumn('action', function ($row) use ($routePrefix) {
                    $url = route($routePrefix . 'detail', encrypt($row->id));
                    return '<a href="' . $url . '" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        abort(404);
    }

    private function checkAndAutoCloseRequisition($requisitionId)
    {
        $er = EmployeeRequisition::with(['hiringSteps.selectionProcesses.candidates'])
            ->find($requisitionId);
        if (!$er) return false;
        $lastStep = $er->hiringSteps->sortByDesc('step_order')->first();
        if (!$lastStep) return false;
        $totalPassed = $lastStep->selectionProcesses->sum(function ($batch) {
            return $batch->candidates->where('result_status', 1)->count();
        });
        if ($totalPassed >= $er->needs) {
            if (is_null($er->fulfilled_date)) {
                $er->update([
                    'fulfilled_date' => now(),
                    'fulfilled_reason' => null
                ]);
                return true;
            }
        } else {
            if (!is_null($er->fulfilled_date) && is_null($er->fulfilled_reason)) {
                $er->update([
                    'fulfilled_date' => null
                ]);
            }
        }
        return false;
    }

    public function result_detail($id)
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
        $approvals = array_filter([
            'approval1' => $er->approval1,
            'approval2' => $er->approval2,
            'approval3' => $er->approval3,
            'approval4' => $er->approval4,
        ]);
        $departments = Department::all();
        $areas = Area::all();
        $positions = Position::all();
        $sections = Section::all();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $viewPath = ($user->hasPermissionTo('emp.menu'))
            ? 'pages.employee.recruitment.result.detail'
            : 'pages.profile.recruitment.result.detail';
        return view($viewPath, compact(
            'user',
            'er',
            'departments',
            'areas',
            'positions',
            'sections',
            'employees',
            'approvals',
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
}
