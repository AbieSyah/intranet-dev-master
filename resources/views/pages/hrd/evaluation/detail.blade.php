@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style type="text/css">
        body {
            background: #f7fbf8;
        }

        .preview {
            text-align: center;
            overflow: hidden;
            width: 160px;
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }

        .section {
            margin-top: 150px;
            background: #fff;
            padding: 50px 30px;
        }

        div.dataTables_wrapper {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Evaluation</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Evaluation</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Detail Evaluation {{ $evaluation->release_id ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light">
                            <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                        </a>
                        @php
                            $forbiddenStatuses = ['RELEASE','REVISE','DRAFT','REJECT'];
                        @endphp
                        @if (!in_array($evaluation->status, $forbiddenStatuses) && !empty($evaluation->total_score) && $evaluation->total_score > 0)
                            <a href="{{ route('evaluation.done.print', encrypt($evaluation->id)) }}" target="_blank"
                                class="btn btn-success btn-label waves-effect waves-light">
                                <i class="ri-printer-fill label-icon align-middle fs-16 me-2"></i> Print
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-3">

                        <div class="col-12">
                            <h5 class="text-center">Employee Information</h5>
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Full Name</label>
                            <input disabled type="text" id="employee_id"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->employee->fullname ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Employee ID (NIK)</label>
                            <input disabled type="text" id="nik"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->employee->nik ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Gender</label>
                            <input disabled type="text" id="gender"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->employee->gender ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Position</label>
                            <input disabled type="text" id="appraisal_id"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->appraisal_position->nama ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Status</label>
                            <input disabled type="text" id="position_status"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->appraisal_status ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Department</label>
                            <input disabled type="text" id="department"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->employee->department->name ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Section</label>
                            <input disabled type="text" id="section"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->employee->section->nama ?? '' }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Building</label>
                            <input disabled type="text" id="building"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->employee->building->nama ?? '' }}" />
                        </div>

                        <div class="col-12">
                            <hr>
                            <h5 class="text-center">Evaluation Information</h5>
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Start Period</label>
                            <input required disabled type="text" id="start_period"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->eval_start->format('j F Y') }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">End Period</label>
                            <input required disabled type="text" id="end_period"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->eval_end->format('j F Y') }}" />
                        </div>

                        <div class="col-lg-4 col-sm-6 p-2">
                            <label class="fw-semibold fs-6 mb-2">Purpose</label>
                            <input required disabled type="text" id="purpose"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $evaluation->purpose ?? '' }}" />
                        </div>

                        <div class="col-12">
                            <hr>
                            <h5 class="text-center">Approval Information</h5>
                        </div>
                        @if (!empty($evaluation->drafter_id))
                            <div class="row">
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Drafter <span class="text-danger">(Not Approval)</span></label>
                                    <input disabled type="text"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $evaluation->drafter->fullname ?? '' }}" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Position</label>
                                    <input disabled type="text"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $evaluation->drafter->position->nama ?? '' }}" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Email</label>
                                    <input disabled type="text"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $evaluation->drafter->user->email ?? '-' }}" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">As <span class="text-danger">(Not Sign)</span></label>
                                    <input disabled type="text"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="Drafter" />
                                </div>
                            </div>
                        @endif
                        @for ($i = 1; $i <= 6; $i++)
                            @php
                                $approvalIdField = 'approval' . $i . '_id';
                                $approvalAsField = 'approval' . $i . '_as';
                                $approvalRelation = 'approval' . $i;
                                $approvalExists = !empty($evaluation->{$approvalIdField});
                                $approver = $evaluation->{$approvalRelation} ?? null;
                            @endphp
                            @if ($approvalExists)
                                <div class="row approval-group approval-group-{{ $i }}">
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Line Approval {{ $i }}</label>
                                        <input disabled type="text" id="approval{{ $i }}_name"
                                            class="form-control form-control-solid mb-3 mb-lg-0"
                                            value="{{ optional($approver)->fullname ?? '' }}" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Position</label>
                                        <input disabled type="text" id="position_ev{{ $i }}"
                                            class="form-control form-control-solid mb-3 mb-lg-0"
                                            value="{{ optional($approver->position)->nama ?? '' }}" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Email</label>
                                        <input disabled type="text" id="email_ev{{ $i }}"
                                            class="form-control form-control-solid mb-3 mb-lg-0"
                                            value="{{ optional($approver->user)->email ?? '-' }}" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Sign {{ $i }} As</label>
                                        <input disabled type="text" id="approval{{ $i }}_as_display"
                                            class="form-control form-control-solid mb-3 mb-lg-0"
                                            value="{{ $evaluation->{$approvalAsField} ?? '' }}" />
                                    </div>
                                </div>
                            @endif
                        @endfor

                        <div class="col-12">
                            <hr>
                        </div>
                    </div>

                    {{-- Evaluation Aspect Section (View Only) --}}
                    @if ($evaluation->total_score > 0)
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12 text-center">
                                            <h4 class="text-primary">Evaluation Aspect</h4>
                                            <h6 class="card-subtitle">Decision is a Rights & Authority Board Of
                                                Directors
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- 1. KPI --}}
                                        @if ($evaluation->kpi_w > 0)
                                            <div class="col-12 p-2">
                                                <div class="card">
                                                    <div class="card-header fw-bold text-center text-uppercase">1. KEY
                                                        PERFORMANCE INDICATOR (KPI)</div>
                                                    <div class="card-body row">
                                                        {{-- Achievement --}}
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="kpi_s">Achievement</label>
                                                            <div class="input-group">
                                                                <input type="number" id="kpi_s" name="kpi_s"
                                                                    class="form-control" min="0" max="100"
                                                                    step="0.01"
                                                                    value="{{ old('kpi_s', $evaluation->kpi_s ?? '') }}"
                                                                    required disabled>
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        {{-- Score --}}
                                                        <div class="col-md-4 mb-3">
                                                            <label class="fw-semibold fs-6 mb-2"
                                                                for="kpi_score">Score</label>
                                                            <input type="text" id="kpi_score" class="form-control"
                                                                disabled value="{{ $evaluation->kpi_sc ?? '' }}">
                                                        </div>
                                                        {{-- Comment --}}
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="kpi_c">Comment</label>
                                                            <input type="text" id="kpi_c" name="kpi_c"
                                                                class="form-control"
                                                                value="{{ old('kpi_c', $evaluation->kpi_c ?? '') }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 2. Attitude & Performance --}}
                                        @if ($evaluation->ap_w > 0)
                                            <div class="col-12 p-2">
                                                <div class="card">
                                                    <div class="card-header fw-bold text-center text-uppercase">2. ATTITUDE & PERFORMANCE</div>
                                                    <div class="card-body row">
                                                        @php
                                                            $jsonPath = resource_path('views/partials/evaluation/profile/ap_guide.json');
                                                            if (file_exists($jsonPath)) {
                                                                $staticGuides = json_decode(file_get_contents($jsonPath), true);
                                                            } else {
                                                                $staticGuides = [];
                                                            }
                                                            $items = [];
                                                            if(is_array($staticGuides)) {
                                                                foreach ($staticGuides as $guideData) {
                                                                    $prefix = $guideData['prefix'];
                                                                    $items[] = [
                                                                        'label'  => $guideData['label'],
                                                                        'prefix' => $prefix,
                                                                        'max'    => $evaluation->{$prefix . '_w'} ?? 0, 
                                                                        'guide'  => $guideData['guide'] ?? [],
                                                                    ];
                                                                }
                                                            }
                                                        @endphp
                                                        
                                                        @foreach ($items as $item)
                                                            @if ($item['max'] > 0)
                                                                <div class="col-12 card mb-1">
                                                                    <div class="modal fade" id="modal-{{ $item['prefix'] }}" tabindex="-1" aria-labelledby="modal-{{ $item['prefix'] }}-label" aria-hidden="true">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="modal-{{ $item['prefix'] }}-label">
                                                                                        Panduan Penilaian - {{ $item['label'] }}
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body scrollable">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-striped table-bordered">
                                                                                            <thead class="bg-primary text-white">
                                                                                                <tr>
                                                                                                    <th scope="col" class="text-center" style="width: 10%;">No.</th>
                                                                                                    <th scope="col">Perilaku yang Ditunjukkan</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @if(isset($item['guide']) && is_array($item['guide']))
                                                                                                    @foreach ($item['guide'] as $no => $perilaku)
                                                                                                        <tr>
                                                                                                            <td class="text-center">{{ $no }}</td>
                                                                                                            <td style="white-space: normal;">{{ $perilaku }}</td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body row">
                                                                        <h5 class="card-title text-center border-bottom pb-2">
                                                                            {{ $item['label'] }}
                                                                            <button type="button" class="btn btn-sm btn-link p-0 text-primary ms-1" 
                                                                                data-bs-toggle="modal" data-bs-target="#modal-{{ $item['prefix'] }}">
                                                                                <i class="ri-question-fill fs-5 align-middle"></i>
                                                                            </button>
                                                                        </h5>
                                                                        
                                                                        {{-- Achievement --}}
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                                for="{{ $item['prefix'] }}_s">Achievement</label>
                                                                            <div class="input-group">
                                                                                <input type="number" id="{{ $item['prefix'] }}_s" name="{{ $item['prefix'] }}_s"
                                                                                    class="form-control" min="0" max="100" step="0.01"
                                                                                    value="{{ old($item['prefix'] . '_s', $evaluation->{$item['prefix'] . '_s'} ?? '') }}"
                                                                                    required disabled>
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        {{-- Score --}}
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="fw-semibold fs-6 mb-2"
                                                                                for="{{ $item['prefix'] }}_score">Score</label>
                                                                            <input type="text" id="{{ $item['prefix'] }}_score" class="form-control"
                                                                                disabled value="{{ $evaluation->{$item['prefix'] . '_sc'} ?? '' }}">
                                                                        </div>
                                                                        
                                                                        {{-- Comment --}}
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                                for="{{ $item['prefix'] }}_c">Comment</label>
                                                                            <input type="text" id="{{ $item['prefix'] }}_c" name="{{ $item['prefix'] }}_c"
                                                                                class="form-control"
                                                                                value="{{ old($item['prefix'] . '_c', $evaluation->{$item['prefix'] . '_c'} ?? '') }}"
                                                                                disabled>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <div class="col-12 mt-3 text-center">
                                                            <label class="fw-bold fs-5">Total Attitude & Performance Score</label>
                                                            <input type="text" class="form-control fw-bold text-center"
                                                                id="ap_s_display" disabled
                                                                value="{{ $evaluation->ap_s ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 3. Attendance --}}
                                        @if ($evaluation->attendance_w > 0)
                                            <div class="col-12 p-2">
                                                <div class="card">
                                                    <div class="card-header fw-bold text-center text-uppercase">3.
                                                        ATTENDANCE
                                                    </div>
                                                    <div class="card-body row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="attendance_s">Achievement</label>
                                                            <div class="input-group">
                                                                <input type="number" id="attendance_s"
                                                                    name="attendance_s" class="form-control"
                                                                    min="0" max="100" step="0.01"
                                                                    value="{{ old('attendance_s', $evaluation->attendance_s ?? '') }}"
                                                                    required disabled>
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="fw-semibold fs-6 mb-2"
                                                                for="attendance_score">Score</label>
                                                            <input type="text" id="attendance_score"
                                                                class="form-control" disabled
                                                                value="{{ $evaluation->attendance_sc ?? '' }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="attendance_c">Comment</label>
                                                            <input type="text" id="attendance_c" name="attendance_c"
                                                                class="form-control"
                                                                value="{{ old('attendance_c', $evaluation->attendance_c ?? '') }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 4. Total --}}
                                        <div class="col-12 p-2">
                                            <div class="card">
                                                <div class="card-header fw-bold text-center text-uppercase">TOTAL SCORE
                                                    OF
                                                    EVALUATION ASPECT</div>
                                                <div class="card-body row">
                                                    <div class="col-md-4 mb-3 text-center">
                                                        <label class="required fw-semibold fs-6 mb-2">Minus
                                                            points</label>
                                                        @php $options = [0, 2, 5, 10, 25, 40]; @endphp
                                                        <select id="minus_poin" name="minus_poin" class="form-select"
                                                            required disabled>
                                                            <option value="" disabled
                                                                {{ old('minus_poin', $evaluation->minus_poin ?? '') === '' ? 'selected' : '' }}>
                                                                Select an option
                                                            </option>
                                                            @foreach ($options as $value)
                                                                <option value="{{ $value }}"
                                                                    {{ old('minus_poin', $evaluation->minus_poin ?? '') == $value ? 'selected' : '' }}>
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3 text-center">
                                                        <label class="fw-semibold fs-6 mb-2">Total Score</label>
                                                        <input type="text" id="total_score" name="total_score"
                                                            class="form-control fw-bold text-center" disabled
                                                            value="{{ $evaluation->total_score ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3 text-center">
                                                        <label class="fw-semibold fs-6 mb-2">Evaluation Grade</label>
                                                        <input type="text" id="grade" name="grade"
                                                            class="form-control fw-bold text-center" disabled
                                                            value="{{ $evaluation->grade ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 5. Comment --}}
                                        <div class="col-12 p-2">
                                            <div class="card">
                                                <div class="card-header fw-bold text-center text-uppercase">COMMENT
                                                </div>
                                                <div class="card-body row">
                                                    <h6 class="card-subtitle mb-2 text-center">Please describe the
                                                        Positive
                                                        or
                                                        Weakness Matters of Ratee</h6>
                                                    <div class="col-md-6 mb-3 text-center">
                                                        <label for="positive"
                                                            class="form-label fw-semibold fs-6 mb-2">{{ Str::title('POSITIVE MATTERS') }}</label>
                                                        <textarea class="form-control" id="positive" name="positive" rows="5" disabled>{{ old('positive', $evaluation->positive ?? '') }}</textarea>
                                                    </div>
                                                    <div class="col-md-6 mb-3 text-center">
                                                        <label for="weakness"
                                                            class="form-label fw-semibold fs-6 mb-2">{{ Str::title('WEAKNESS MATTERS') }}</label>
                                                        <textarea class="form-control" id="weakness" name="weakness" rows="5" disabled>{{ old('weakness', $evaluation->weakness ?? '') }}</textarea>
                                                    </div>
                                                    @cannot('hrd.evaluation.note')
                                                        <div class="col-12 mb-3 text-center">
                                                            <label for="note_hrd"
                                                                class="form-label fw-semibold fs-6 mb-2">Note
                                                                from HRD</label>
                                                            <textarea class="form-control" id="note_hrd" name="note_hrd" rows="3" disabled>{{ old('note_hrd', $evaluation->note_hrd ?? '') }}</textarea>
                                                        </div>
                                                    @endcannot
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 6. Decision --}}
                                        @if ($evaluation->purpose == 'Employment Status')
                                            <div class="col-12 p-2">
                                                <div class="card">
                                                    <div class="card-header fw-bold text-center text-uppercase">
                                                        DECISION OF
                                                        EMPLOYMENT STATUS</div>
                                                    <div class="card-body row">
                                                        <h6 class="card-subtitle mb-2 text-center"><span
                                                                class="text-danger">*</span>
                                                            Grade A or Grade B is possible to
                                                            be proposed as permanent employee or contract extend.</h6>
                                                        <div class="col-12 mb-3 text-center">
                                                            @php
                                                                $options = [
                                                                    'Contract extend',
                                                                    'Assign as permanent employee',
                                                                    'Terminated',
                                                                ];
                                                            @endphp
                                                            <select id="decision_employment" name="decision_employment"
                                                                class="form-select" required disabled>
                                                                <option value="" disabled
                                                                    {{ old('decision_employment', $evaluation->decision_employment ?? '') === '' ? 'selected' : '' }}>
                                                                    Select an option
                                                                </option>
                                                                @foreach ($options as $value)
                                                                    <option value="{{ $value }}"
                                                                        {{ old('decision_employment', $evaluation->decision_employment ?? '') == $value ? 'selected' : '' }}>
                                                                        {{ $value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @if (isset($evaluation->month_extend) && !empty($evaluation->month_extend))
                                                                <div id="month_extend_container" class="mt-2">
                                                                    @php
                                                                        $displayExtend = $evaluation->month_extend . ' months';
                                                                        if (isset($evaluation->date_extend) && !empty($evaluation->date_extend)) {
                                                                            $formattedDate = optional(\Carbon\Carbon::parse($evaluation->date_extend))->format('d M Y');
                                                                            $displayExtend .= ' (' . $formattedDate . ')';
                                                                        }
                                                                    @endphp
                                                                    <input type="text"
                                                                        class="form-control" 
                                                                        value="{{ $displayExtend }}" 
                                                                        disabled>
                                                                </div>
                                                            @endif
                                                            @if (isset($evaluation->decision_reason) && !empty($evaluation->decision_reason))
                                                                <label class="fw-semibold fs-6 mt-2">Reason for Decision</label>
                                                                <input type="text" id="decision_reason" name="decision_reason"
                                                                    class="form-control text-center" disabled
                                                                    value="{{ $evaluation->decision_reason ?? '' }}">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- End Evaluation Aspect Section --}}
                </div>
            </div>
        </div>
    </div>
    @can('hrd.evaluation.note')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form class="form" id="noteHRDForm" action="{{ route('evaluation.detail.notes-hrd.store') }}"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="id" id="id" value="{{ $evaluation->id ?? '' }}">
                        <div class="card-header align-items-center d-flex justify-content-between">
                            <h3 class="card-title">Note from HRD <span class="text-danger">(Max. <span id="count_note_hrd">100</span> Character)</span></h3>
                            <div class="flex-shrink-0">
                                <button type="submit" id="save-noteHRD-btn"
                                    class="btn btn-primary btn-label waves-effect waves-light d-none">
                                    <i class="ri-ball-pen-line label-icon align-middle fs-16 me-2"></i> Save
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="note_hrd" name="note_hrd" rows="3" maxlength="100">{{ old('note_hrd', $evaluation->note_hrd ?? '') }}</textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form" id="detailAttachForm" action="{{ route('evaluation.detail.attach.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="id" id="id" value="{{ $evaluation->id ?? '' }}">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h3 class="card-title">Attachments</h3>
                        <div class="flex-shrink-0">
                            <button type="submit" id="save-attachment-btn"
                                class="btn btn-primary btn-label waves-effect waves-light d-none">
                                <i class="ri-attachment-2 label-icon align-middle fs-16 me-2"></i> Save
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap mb-0" id="attachment-table">
                                <thead class="align-middle">
                                    <tr class="table-active">
                                        <th scope="col" style="width: 5%;">#</th>
                                        <th scope="col" style="width: 45%;">Attachment Name<span
                                                class="text-danger">*</span></th>
                                        <th scope="col" style="width: 40%;">File<span class="text-danger">*</span>
                                        </th>
                                        <th scope="col" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="attachment-list">
                                    @if (isset($evaluation) && $evaluation->attachments && $evaluation->attachments->count() > 0)
                                        @foreach ($evaluation->attachments as $index => $attachment)
                                            <tr class="existing-attachment-row">
                                                <th scope="row" class="attachment-id">{{ $index + 1 }}</th>
                                                <td>
                                                    <div class="mb-2">
                                                        <input type="text" class="form-control"
                                                            name="existing_attachment_names[{{ $attachment->id }}]"
                                                            value="{{ $attachment->name }}" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                            class="btn btn-primary" target="_blank">View File</a>
                                                    </div>
                                                </td>
                                                <td class="attachment-removal">
                                                    <button type="button"
                                                        class="btn btn-danger remove-existing-attachment-btn"
                                                        data-attachment-id="{{ $attachment->id }}">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="4">
                                            <a href="javascript:void(0)" id="add-attachment-item"
                                                class="btn btn-soft-secondary fw-medium"><i
                                                    class="ri-add-fill me-1 align-bottom"></i> Add Attachment</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Approval Reason</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered display nowrap" style="width:100%" id="table_approval_reason">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="width: 20%;">As</th>
                                <th scope="col" class="text-center" style="width: 30%;">Name</th>
                                <th scope="col" style="width: 50%;">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hasApproval = false;
                            @endphp
                            @for ($i = 1; $i <= 6; $i++)
                                @php
                                    $approvalIdField = 'approval' . $i . '_id';
                                    $approvalAsField = 'approval' . $i . '_as';
                                    $approvalReasonField = 'approval' . $i . '_reason';
                                    $approvalRelation = 'approval' . $i;
                                @endphp
                                @if (!empty($evaluation->$approvalIdField))
                                    @php $hasApproval = true; @endphp
                                    <tr>
                                        <td class="text-center">{{ $evaluation->$approvalAsField ?? '-' }}</td>
                                        <td class="text-center">{{ optional($evaluation->$approvalRelation)->fullname ?? '-' }}</td>
                                        <td class="text-wrap">{{ $evaluation->$approvalReasonField ?? '-' }}</td>
                                    </tr>
                                @endif
                            @endfor
                            @if (!$hasApproval)
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No approval data available yet.</td>
                                </tr>
                            @endif
                        </tbsody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @if ($evaluation->evaluationHistories->isNotEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h3 class="card-title">Evaluation History</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped bordered display nowrap" style="width:100%"
                            id="table_evaluation_history">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Date Time</th>
                                    <th scope="col" class="text-center">User</th>
                                    <th scope="col" class="text-center">IP Address</th>
                                    <th scope="col" class="text-center">Action</th>
                                    <th scope="col">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($evaluation->evaluationHistories->sortByDesc('created_at') as $history)
                                    <tr>
                                        <td class="text-center">{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="text-center">{{ $history->user->name }}</td>
                                        <td class="text-center">{{ $history->ip_address }}</td>
                                        <td class="text-center">
                                            @php
                                                $action = $history->action;
                                                $badges = [
                                                    'RELEASE' => 'success',
                                                    'DRAFT' => 'secondary',
                                                    'REVISE' => 'danger',
                                                    'REJECT' => 'dark',
                                                    'CANCEL' => 'dark',
                                                    '1st Evaluator' => 'success',
                                                    '2nd Evaluator' => 'success',
                                                    '3rd Evaluator' => 'success',
                                                    'HRD Approved' => 'success',
                                                    'Prodir' => 'success',
                                                    'Presdir' => 'success',
                                                    'DONE' => 'success',
                                                ];
                                                $displayText = ($action === 'RELEASE') ? 'HRD' : $action;
                                            @endphp
                                            @if (isset($badges[$action]))
                                                <span class="badge text-bg-{{ $badges[$action] }}">{{ $displayText }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $history->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('script')
    <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            const saveBtn = $('#save-attachment-btn');
            const initialAttachmentCount = $('#attachment-list tr').length;
            let originalAttachments = {
                existingNames: {}
            };

            function captureInitialState() {
                originalAttachments.existingNames = {};
                $('#attachment-list input[name^="existing_attachment_names"]').each(function() {
                    const attachmentId = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    originalAttachments.existingNames[attachmentId] = $(this).val();
                });
            }

            function checkForChanges() {
                const newAttachmentsCount = $('.new-attachment-row').length;
                const deletedAttachmentsCount = $('input[name="deleted_attachments[]"]').length;
                const currentTotalAttachments = $('#attachment-list tr').not('.new-attachment-row').length;
                let nameChanged = false;
                $('#attachment-list input[name^="existing_attachment_names"]').each(function() {
                    const attachmentId = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    if (originalAttachments.existingNames[attachmentId] !== $(this).val()) {
                        nameChanged = true;
                        return false;
                    }
                });
                if (newAttachmentsCount > 0 || deletedAttachmentsCount > 0 || nameChanged ||
                    currentTotalAttachments !== initialAttachmentCount) {
                    saveBtn.removeClass('d-none');
                } else {
                    saveBtn.addClass('d-none');
                }
            }

            function updateAttachmentNumbers() {
                $('#attachment-list tr').each(function(index) {
                    $(this).find('.attachment-id').text(index + 1);
                });
            }

            function addAttachmentRow(defaultName = '') {
                const currentTotalRows = $('#attachment-list tr').length;
                const newCounter = currentTotalRows + 1;
                const newRow = `
                    <tr class="new-attachment-row">
                        <th scope="row" class="attachment-id">${newCounter}</th>
                        <td>
                            <div class="mb-2">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="new_attachment_names[]" 
                                    placeholder="e.g., Attendance List" 
                                    value="${defaultName}" 
                                    required>
                            </div>
                        </td>
                        <td>
                            <div class="mb-2">
                                <input type="file" class="form-control" name="new_attachments[]" required>
                            </div>
                        </td>
                        <td class="attachment-removal">
                            <button type="button" class="btn btn-danger remove-new-attachment-btn">Delete</button>
                        </td>
                    </tr>
                    `;
                $('#attachment-list').append(newRow);
                updateAttachmentNumbers();
            }

            captureInitialState();
            updateAttachmentNumbers();

            const totalExistingRows = $('#attachment-list tr').length;
            if (totalExistingRows === 0) {
                addAttachmentRow('KPI');
                addAttachmentRow('Attendance');
                checkForChanges(); 
            }

            $('#add-attachment-item').on('click', function() {
                addAttachmentRow();
                checkForChanges();
            });

            $(document).on('click', '.remove-new-attachment-btn', function() {
                $(this).closest('tr').remove();
                updateAttachmentNumbers();
                checkForChanges();
            });

            $(document).on('click', '.remove-existing-attachment-btn', function() {
                const attachmentId = $(this).data('attachment-id');
                $(this).closest('tr').remove();
                $('#detailAttachForm').append(
                    `<input type="hidden" name="deleted_attachments[]" value="${attachmentId}">`);
                updateAttachmentNumbers();
                checkForChanges();
            });

            $(document).on('change', 'input[name="new_attachments[]"]', function() {
                checkForChanges();
            });

            $(document).on('input',
                'input[name^="existing_attachment_names"], input[name="new_attachment_names[]"]',
                function() {
                    checkForChanges();
                });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("form").submit(function(e) {
                e.preventDefault();
                if (!this.reportValidity()) {
                    return;
                }
                swalert = Swal.fire({
                    title: 'Saving data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                const formData = new FormData(this);
                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        swalert.hideLoading();
                        swalert.update({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                popup: 'swal2-noanimation',
                                confirmButton: "btn btn-primary"
                            }
                        });
                        swalert.then(() => {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function(xhr, status, error) {
                        $("#loadingSpinner").hide();
                        console.log({
                            xhr,
                            status,
                            error
                        });
                        handleErrorResponse(xhr.responseJSON);
                    }
                });
            });

            function handleErrorResponse(responseJson) {
                let errorMessage = '';

                if (responseJson.message) {
                    errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
                }

                if (responseJson.errors) {
                    for (const fieldName in responseJson.errors) {
                        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
                    }
                }

                if (responseJson.responseText) {
                    errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;
                }

                if (errorMessage === '') {
                    errorMessage += '<p class="text-danger">An error occurred.</p>';
                }

                swalert.update({
                    title: 'Error',
                    html: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        });
    </script>

    @if ($evaluation->evaluationHistories->isNotEmpty())
        <script type="text/javascript">
            $(document).ready(function() {
                $('#table_evaluation_history').DataTable({
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    scrollX: true,
                    order: [
                        [0, 'desc']
                    ]
                });
            });
        </script>
    @endif
    <script>
        @if (Session::has('success'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.error("{{ session('error') }}");
        @endif
    </script>
    @can('hrd.evaluation.note')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const textarea = document.getElementById('note_hrd');
                const saveBtn = document.getElementById('save-noteHRD-btn');
                const originalValue = textarea.value;
                textarea.addEventListener('input', function() {
                    if (this.value !== originalValue) {
                        saveBtn.classList.remove('d-none');
                    } else {
                        saveBtn.classList.add('d-none');
                    }
                });
            });
            $(document).ready(function() {
                // Count Character
                function updateCharacterCount(textareaId, countId, maxLength) {
                    const textarea = document.getElementById(textareaId);
                    const countSpan = document.getElementById(countId);
                    if (!textarea || !countSpan) return;
                    const updateCount = function() {
                        const currentLength = textarea.value.length;
                        const remaining = maxLength - currentLength;
                        countSpan.textContent = remaining;
                        if (remaining < 0) {
                            countSpan.classList.add('text-warning');
                        } else {
                            countSpan.classList.remove('text-warning');
                        }
                    };
                    textarea.addEventListener('input', updateCount);
                    updateCount(); 
                }

                const inputToMonitor = [
                    { id: 'note_hrd', max: 100 }, 
                ];
                inputToMonitor.forEach(item => {
                    updateCharacterCount(item.id, `count_${item.id}`, item.max); 
                });
            });
        </script>
    @endcan
@endsection
