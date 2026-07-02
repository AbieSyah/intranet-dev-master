@php
    $asField = $role . '_as';
    $isHrd = isset($evaluation->$asField) && $evaluation->$asField === 'HRD Approval';
    $showButtons = false;
    $dateField = $role . '_date';
    if (is_null($evaluation->$dateField)) {
        $showButtons = true;
    }
    $canDraft = in_array($role, ['drafter', 'approval1']);
@endphp
{{-- Employee Information Card --}}
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h4 class="text-primary">Employee Information</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ $backRoute }}"
                        class="btn btn-primary btn-label waves-effect waves-light float-end">
                        <i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back
                    </a>
                </div>
                <div class="col-md-6">
                    <p><strong>Period <span class="d-none d-sm-inline"> : </span></strong>
                        <span class="d-sm-none"><br></span>
                        {{ 
                            ($evaluation->eval_start ? \Carbon\Carbon::parse($evaluation->eval_start)->format('d M Y') : '-') 
                            . ' - ' . 
                            ($evaluation->eval_end ? \Carbon\Carbon::parse($evaluation->eval_end)->format('d M Y') : '-') 
                        }}
                    </p>
                    <p><strong>Evaluation Number <span class="d-none d-sm-inline"> : </span></strong>
                        <span class="d-sm-none"><br></span>
                        {{ strtoupper($evaluation->release_id ?? '') }}
                    </p>
                    <p><strong>Name <span class="d-none d-sm-inline"> : </span></strong>
                        <span class="d-sm-none"><br></span>
                        {{ strtoupper($evaluation->employee->fullname ?? '') }}
                    </p>
                    <p><strong>Position <span class="d-none d-sm-inline"> : </span></strong>
                        <span class="d-sm-none"><br></span>
                        {{ strtoupper($evaluation->appraisal->position->nama ?? '') }}
                    </p>
                    <p><strong>Department / Section <span class="d-none d-sm-inline"> : </span></strong>
                        <span class="d-sm-none"><br></span>
                        {{ isset($evaluation->appraisal->department->name) ? strtoupper($evaluation->appraisal->department->name) : 'N/A' }}
                        {{ isset($evaluation->appraisal->section->nama) 
                            ? ' / ' . strtoupper($evaluation->appraisal->section->nama) 
                            : ' / N/A' }}
                    </p>
                    <p><strong>Status <span class="d-none d-sm-inline"> : </span></strong>
                        <span class="d-sm-none"><br></span>
                        {{ strtoupper($evaluation->appraisal->status ?? '') }}
                    </p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-primary">Approval Information</h4>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-danger btn-label waves-effect waves-light float-end"
                        data-bs-toggle="modal" data-bs-target="#reviceModal">
                        <i class="ri-edit-line label-icon align-middle fs-16 me-2"></i> Revise
                    </button>
                    <div class="modal fade" id="reviceModal" tabindex="-1" aria-labelledby="reviceModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header text-white">
                                    <h5 class="modal-title" id="reviceModalLabel">Revise Evaluation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ $reviceRoute }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="revice_reason" class="form-label">Please provide a reason for revision :</label>
                                            <textarea class="form-control" id="revice_reason" name="revice_reason" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Revise</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $approvalSteps = ['approval1', 'approval2', 'approval3', 'approval4', 'approval5', 'approval6'];
                    $count = 0;
                @endphp
                @foreach($approvalSteps as $step)
                    @if(isset($evaluation->{$step . '_id'}))
                        <div class="col-md-4">
                            <p><strong>As <span class="d-none d-sm-inline"> : </span></strong>
                                <span class="d-sm-none"><br></span>
                                {{ $evaluation->{$step . '_as'} }}
                                @if($role == $step)
                                    <span class="fw-bold text-success">(You)</span>
                                @elseif($role == 'drafter' && $step == 'approval1')
                                    <span class="fw-bold text-primary">(Drafter)</span>
                                @endif
                            </p>
                            <p><strong>Name <span class="d-none d-sm-inline"> : </span></strong>
                                <span class="d-sm-none"><br></span>
                                {{ strtoupper($evaluation->{$step}->fullname ?? '') }}
                            </p>
                            <p><strong>Position <span class="d-none d-sm-inline"> : </span></strong>
                                <span class="d-sm-none"><br></span>
                                {{ strtoupper($evaluation->{$step}->position->nama ?? '') }}
                            </p>
                            <p><strong>Department / Section <span class="d-none d-sm-inline"> : </span></strong>
                                <span class="d-sm-none"><br></span>
                                {{ strtoupper($evaluation->{$step}->department->name) }}
                                {{ isset($evaluation->{$step}->section->nama) 
                                    ? ' / ' . strtoupper($evaluation->{$step}->section->nama) 
                                    : ' / NA' }}
                            </p>
                        </div>
                        @php $count++; @endphp
                        @if ($count % 3 == 0)
                            <div class="col-12"><hr></div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Evaluation Aspect Card --}}
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <h4 class="text-primary">Evaluation Aspect</h4>
                    <h6 class="card-subtitle">Decision is a Rights & Authority Board Of Directors</h6>
                </div>
            </div>
            <form action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data" id="evalForm">
                @csrf
                @method('POST')
                <div class="row">
                    <input type="hidden" name="id" id="id" value="{{ $evaluation->id ?? '' }}">

                    {{-- 1. KPI --}}
                    @if ($evaluation->appraisal->kpi_weight > 0)
                        <div class="col-12 p-2">
                            <div class="card">
                                <div class="card-header fw-bold text-center text-uppercase">1. KEY PERFORMANCE INDICATOR (KPI)</div>
                                <div class="card-body row">
                                    <div class="col-md-4 mb-3">
                                        <label class="required fw-semibold fs-6 mb-2" for="kpi_s">Achievement</label>
                                        <div class="input-group">
                                            <input type="number" id="kpi_s" name="kpi_s" class="form-control"
                                                placeholder="0 - 100" min="0" max="100" step="any"
                                                value="{{ old('kpi_s', $evaluation->kpi_s ?? '') }}" {{ $isHrd ? 'disabled' : 'required' }}>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="fw-semibold fs-6 mb-2" for="kpi_score">Score</label>
                                        <input type="text" id="kpi_score" class="form-control" disabled
                                            value="{{ $evaluation->kpi_score ?? '' }}">
                                        <input type="hidden" id="TKPI_score" name="kpi_sc"
                                            value="{{ old('kpi_sc', $evaluation->kpi_sc ?? '') }}">
                                        <input type="hidden" name="kpi_w" value="{{ $evaluation->appraisal->kpi_weight }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="required fw-semibold fs-6 mb-2" for="kpi_c">Comment
                                            <span class="text-danger">(Max. <span id="count_kpi_c">60</span> Character)</span>
                                        </label>
                                        <input type="text" id="kpi_c" name="kpi_c" class="form-control"
                                            placeholder="Your Comment" maxlength="60"
                                            value="{{ old('kpi_c', $evaluation->kpi_c ?? '') }}" {{ $isHrd ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const formType = @json($evaluation->appraisal->form_type);
                                const kpiWeight = parseFloat(@json($evaluation->appraisal->kpi_weight));
                                const inputAch = document.getElementById('kpi_s');
                                const inputScore = document.getElementById('kpi_score');
                                const hiddenScore = document.getElementById('TKPI_score');
                                function hitungKPIScore() {
                                    let ach = parseFloat(inputAch.value);
                                    if (isNaN(ach) || ach < 0 || ach > 100) {
                                        inputScore.value = '';
                                        hiddenScore.value = '';
                                    } else {
                                        let score = (formType === 'A') ?
                                            kpiWeight * (ach / 100) :
                                            (kpiWeight / 100) * (ach / 100) * 100;
                                        inputScore.value = score.toFixed(2);
                                        hiddenScore.value = score.toFixed(2);
                                    }
                                    hiddenScore.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                                inputAch.addEventListener('input', hitungKPIScore);
                                if (inputAch.value) hitungKPIScore();
                            });
                        </script>
                    @endif

                    {{-- 2. Attitude & Performance --}}
                    @if ($evaluation->appraisal->ap_weight > 0)
                        <div class="col-12 p-2">
                            <div class="card">
                                <div class="card-header fw-bold text-center text-uppercase">2. ATTITUDE & PERFORMANCE</div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-label waves-effect waves-light mt-3"
                                        data-bs-toggle="modal" data-bs-target="#ratingGuideModal">
                                        <i class="ri-book-2-line label-icon align-middle fs-16 me-2"></i> Scoring Guide
                                    </button>
                                    <div class="modal fade" id="ratingGuideModal" tabindex="-1" aria-labelledby="ratingGuideModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="ratingGuideModalLabel">Panduan Penilaian Kinerja</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body scrollable">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered">
                                                            <thead class="bg-primary text-white">
                                                                <tr>
                                                                    <th scope="col" style="width: 15%;">Achievement</th>
                                                                    <th scope="col">Deskripsi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $ratingGuide = [
                                                                        100 => 'Menunjukkan 10 dari 10 perilaku untuk aspek terkait',
                                                                        90  => 'Menunjukkan 9 dari 10 perilaku untuk aspek terkait',
                                                                        80  => 'Menunjukkan 8 dari 10 perilaku untuk aspek terkait',
                                                                        70  => 'Menunjukkan 7 dari 10 perilaku untuk aspek terkait',
                                                                        60  => 'Menunjukkan 6 dari 10 perilaku untuk aspek terkait',
                                                                        50  => 'Menunjukkan 5 dari 10 perilaku untuk aspek terkait',
                                                                        40  => 'Menunjukkan 4 dari 10 perilaku untuk aspek terkait',
                                                                        30  => 'Menunjukkan 3 dari 10 perilaku untuk aspek terkait',
                                                                        20  => 'Menunjukkan 2 dari 10 perilaku untuk aspek terkait',
                                                                        10  => 'Menunjukkan 1 dari 10 perilaku untuk aspek terkait',
                                                                    ];
                                                                @endphp
                                                                @foreach ($ratingGuide as $achievement => $description)
                                                                    <tr>
                                                                        <td>{{ $achievement }}%</td>
                                                                        <td>{{ $description }}</td>
                                                                    </tr>
                                                                @endforeach
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
                                </div>
                                
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
                                                    'max'    => $evaluation->appraisal->$prefix ?? 0, 
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
                                                    <div class="col-md-4 mb-3 mt-2">
                                                        <label class="required fw-semibold fs-6 mb-2" for="{{ $item['prefix'] }}_s">Achievement</label>
                                                        <select class="form-select" id="{{ $item['prefix'] }}_s" name="{{ $item['prefix'] }}_s" {{ $isHrd ? 'disabled' : 'required' }}>
                                                            <option value="" disabled {{ old($item['prefix'] . '_s', $evaluation->{$item['prefix'] . '_s'} ?? '') === '' ? 'selected' : '' }}>
                                                                Select an option
                                                            </option>
                                                            @for ($i = 10; $i <= 100; $i += 10)
                                                                <option value="{{ $i }}" {{ old($item['prefix'] . '_s', $evaluation->{$item['prefix'] . '_s'} ?? '') == $i ? 'selected' : '' }}>
                                                                    {{ $i }}%
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 mb-3 mt-2">
                                                        <label class="fw-semibold fs-6 mb-2">Scoring Scale</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" value="{{ $item['max'] * 0.2 }}" disabled>
                                                            <span class="input-group-text">~</span>
                                                            <input type="text" class="form-control max-value-input" value="{{ $item['max'] }}" disabled>
                                                            <input type="hidden" name="{{ $item['prefix'] }}_w" value="{{ $item['max'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-3 mt-2">
                                                        <label class="fw-semibold fs-6 mb-2" for="{{ $item['prefix'] }}_score">Score</label>
                                                        <input type="text" class="form-control" id="{{ $item['prefix'] }}_score" disabled>
                                                        <input type="hidden" name="{{ $item['prefix'] }}_sc"
                                                            value="{{ old($item['prefix'] . '_sc', $evaluation->{$item['prefix'] . '_sc'} ?? '') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3 mt-2">
                                                        <label class="required fw-semibold fs-6 mb-2" for="{{ $item['prefix'] }}_c">Comment
                                                            <span class="text-danger">(Max. <span id="count_{{ $item['prefix'] }}_c">60</span> Character)</span>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            id="{{ $item['prefix'] }}_c" name="{{ $item['prefix'] }}_c"
                                                            placeholder="Your Comment" maxlength="60"
                                                            value="{{ old($item['prefix'] . '_c', $evaluation->{$item['prefix'] . '_c'} ?? '') }}" {{ $isHrd ? 'disabled' : '' }}>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="col-12 mt-3 text-center">
                                        <label class="fw-bold fs-5">Total Attitude & Performance Score</label>
                                        <input type="text" class="form-control fw-bold text-center" id="ap_s_display" disabled
                                            value="{{ $evaluation->ap_s ?? '' }}">
                                        <input type="hidden" id="TAP_score" name="ap_s" value="{{ $evaluation->ap_s ?? '' }}">
                                        <input type="hidden" id="TAP_score_sc" name="ap_sc" value="{{ $evaluation->ap_sc ?? '' }}">
                                        <input type="hidden" name="ap_w" value="{{ $evaluation->appraisal->ap_weight }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const formType = @json($evaluation->appraisal->form_type);
                                const apWeight = parseFloat(@json($evaluation->appraisal->ap_weight));
                                const selects = document.querySelectorAll('select[name$="_s"]');
                                const totalField = document.getElementById('ap_s_display');
                                const hiddenAP = document.getElementById('TAP_score');
                                const hiddenAPSC = document.getElementById('TAP_score_sc');
                                function hitungAPScore() {
                                    let totalScore = 0, totalMax = 0;
                                    selects.forEach(select => {
                                        const maxVal = parseFloat(select.closest('.card-body').querySelector('.max-value-input').value);
                                        const scoreInput = select.closest('.row').querySelector('[id$="_score"]');
                                        let achVal = parseFloat(select.value);
                                        const hiddenScoreInput = select.closest('.card-body').querySelector(`input[name="${select.name.replace('_s', '_sc')}"]`); 
                                        if (!isNaN(maxVal)) totalMax += maxVal;
                                        if (!isNaN(achVal) && maxVal > 0) {
                                            let score = maxVal * (achVal / 100);
                                            scoreInput.value = score.toFixed(2);
                                            totalScore += score;
                                            if (hiddenScoreInput) hiddenScoreInput.value = score.toFixed(2);
                                        } else {
                                            scoreInput.value = '';
                                            if (hiddenScoreInput) hiddenScoreInput.value = '';
                                        }
                                    });                                    
                                    let finalScore = 0;
                                    if (totalMax > 0) {
                                        finalScore = (formType === 'A') ?
                                            (totalScore * apWeight) / totalMax :
                                            ((totalScore * (apWeight / 100)) / totalMax) * 100;
                                    }
                                    totalField.value = finalScore.toFixed(2);
                                    hiddenAP.value = finalScore.toFixed(2);
                                    hiddenAPSC.value = totalScore.toFixed(2);
                                    hiddenAP.dispatchEvent(new Event('input', { bubbles: true }));
                                }                                
                                selects.forEach(select => select.addEventListener('change', hitungAPScore));
                                hitungAPScore();
                            });
                        </script>
                    @endif
        
                    {{-- 3. Attendance --}}
                    @if ($evaluation->appraisal->attendance > 0)
                        <div class="col-12 p-2">
                            <div class="card">
                                <div class="card-header fw-bold text-center text-uppercase">3. ATTENDANCE</div>
                                <div class="card-body row">
                                    <div class="col-md-4 mb-3">
                                        <label class="required fw-semibold fs-6 mb-2" for="attendance_s">Achievement</label>
                                        <div class="input-group">
                                            <input type="number" id="attendance_s" name="attendance_s" class="form-control"
                                                placeholder="0 - 100" min="0" max="100" step="any"
                                                value="{{ old('attendance_s', $evaluation->attendance_s ?? '') }}" {{ $isHrd ? 'disabled' : 'required' }}>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="fw-semibold fs-6 mb-2" for="attendance_score">Score</label>
                                        <input type="text" id="attendance_score" class="form-control" disabled
                                            value="{{ $evaluation->attendance_score ?? '' }}">
                                        <input type="hidden" id="TAT_score" name="attendance_sc"
                                            value="{{ old('attendance_sc', $evaluation->attendance_sc ?? '') }}">
                                        <input type="hidden" name="attendance_w" value="{{ $evaluation->appraisal->attendance }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="required fw-semibold fs-6 mb-2" for="attendance_c">Comment
                                            <span class="text-danger">(Max. <span id="count_attendance_c">60</span> Character)</span>
                                        </label>
                                        <input type="text" id="attendance_c" name="attendance_c" class="form-control"
                                            placeholder="Your Comment" maxlength="60"
                                            value="{{ old('attendance_c', $evaluation->attendance_c ?? '') }}" {{ $isHrd ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const formType = @json($evaluation->appraisal->form_type);
                                const ATWeight = parseFloat(@json($evaluation->appraisal->attendance));
                                const inputAch = document.getElementById('attendance_s');
                                const inputScore = document.getElementById('attendance_score');
                                const hiddenAT = document.getElementById('TAT_score');
                                function hitungATScore() {
                                    let ach = parseFloat(inputAch.value);
                                    if (isNaN(ach) || ach < 0 || ach > 100) {
                                        inputScore.value = '';
                                        hiddenAT.value = '';
                                    } else {
                                        let score = (formType === 'A') ?
                                            ATWeight * (ach / 100) :
                                            (ATWeight / 100) * (ach / 100) * 100;
                                        inputScore.value = score.toFixed(2);
                                        hiddenAT.value = score.toFixed(2);
                                    }
                                    hiddenAT.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                                inputAch.addEventListener('input', hitungATScore);
                                if (inputAch.value) hitungATScore();
                            });
                        </script>
                    @endif

                    {{-- 4. Total --}}
                    <div class="col-12 p-2">
                        <div class="card">
                            <div class="card-header fw-bold text-center text-uppercase">TOTAL SCORE OF EVALUATION ASPECT</div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-3 text-center">
                                    <label class="fw-semibold fs-6 mb-2">KPI Score</label>
                                    <input type="text" id="TKPI_score_display" class="form-control fw-bold text-center" disabled
                                        value="{{ $evaluation->kpi_score ?? '' }}">
                                </div>
                                <div class="col-md-4 mb-3 text-center">
                                    <label class="fw-semibold fs-6 mb-2">Attitude & Performance Score</label>
                                    <input type="text" id="TAP_score_display" class="form-control fw-bold text-center" disabled
                                        value="{{ $evaluation->ap_s ?? '' }}">
                                </div>
                                <div class="col-md-4 mb-3 text-center">
                                    <label class="fw-semibold fs-6 mb-2">Attendance Score</label>
                                    <input type="text" id="TAT_score_display" class="form-control fw-bold text-center" disabled
                                        value="{{ $evaluation->attendance_score ?? '' }}">
                                </div>
                                <div class="col-md-4 mb-3 text-center">
                                    <label class="required fw-semibold fs-6 mb-2">Minus points</label>
                                    @php $minusOptions = [0, 2, 5, 10, 25, 40]; $selectedMinus = old('minus_poin', $evaluation->minus_poin ?? 0); @endphp
                                    <select id="minus_poin" name="minus_poin" class="form-select" {{ $isHrd ? 'disabled' : 'required' }}>
                                        @foreach ($minusOptions as $value)
                                            <option value="{{ $value }}" {{ $selectedMinus == $value ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 text-center">
                                    <label class="fw-semibold fs-6 mb-2">Total Score</label>
                                    <input type="text" id="total_score" name="total_score"
                                        class="form-control fw-bold text-center" readonly
                                        value="{{ $evaluation->total_score ?? '' }}">
                                </div>
                                <div class="col-md-4 mb-3 text-center">
                                    <label class="fw-semibold fs-6 mb-2">Evaluation Grade</label>
                                    <input type="text" id="grade" name="grade"
                                        class="form-control fw-bold text-center" readonly
                                        value="{{ $evaluation->grade ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const formType = @json($evaluation->appraisal->form_type);
                            const kpiWeight = @json($evaluation->appraisal->kpi_weight);
                            const TKPI = document.getElementById('TKPI_score');
                            const TAP = document.getElementById('TAP_score');
                            const TAT = document.getElementById('TAT_score');
                            const dispTKPI = document.getElementById('TKPI_score_display');
                            const dispTAP = document.getElementById('TAP_score_display');
                            const dispTAT = document.getElementById('TAT_score_display');
                            const minusPoin = document.getElementById('minus_poin');
                            const totalField = document.getElementById('total_score');
                            const gradeField = document.getElementById('grade');
                            function updateTotalAndGrade() {
                                if (dispTKPI) dispTKPI.value = TKPI ? TKPI.value : 0;
                                if (dispTAP) dispTAP.value = TAP ? TAP.value : 0;
                                if (dispTAT) dispTAT.value = TAT ? TAT.value : 0;
                                let valTKPI = (TKPI && !isNaN(parseFloat(TKPI.value))) ? parseFloat(TKPI.value) : 0;
                                let valTAP  = (TAP  && !isNaN(parseFloat(TAP.value)))  ? parseFloat(TAP.value)  : 0;
                                let valTAT  = (TAT  && !isNaN(parseFloat(TAT.value)))  ? parseFloat(TAT.value)  : 0;
                                let minus   = (minusPoin && !isNaN(parseFloat(minusPoin.value))) ? parseFloat(minusPoin.value) : 0;
                                let total = 0;
                                if (formType === 'A') {
                                    total = (kpiWeight == 0) ? (valTAP + valTAT) - minus : (valTKPI + valTAP + valTAT) - minus;
                                } else {
                                    total = ((valTKPI + valTAP) * (valTAT / 100)) - minus;
                                }
                                if (total < 0) total = 0;
                                if (totalField) totalField.value = total.toFixed(2);
                                let grade = total >= 95 ? 'A' : total >= 86 ? 'B' : total >= 72 ? 'C' : total >= 55 ? 'D' : 'E';
                                if (gradeField && gradeField.value !== grade) {
                                    gradeField.value = grade;
                                    gradeField.dispatchEvent(new Event('change'));
                                }
                            }
                            [TKPI, TAP, TAT, minusPoin].forEach(el => {
                                if (el) {
                                    el.addEventListener('input', updateTotalAndGrade);
                                    el.addEventListener('change', updateTotalAndGrade);
                                }
                            });
                            updateTotalAndGrade();
                        });
                    </script>

                    {{-- 5. Comment --}}
                    <div class="col-12 p-2">
                        <div class="card">
                            <div class="card-header fw-bold text-center text-uppercase">COMMENT</div>
                            <div class="card-body row">
                                <h6 class="card-subtitle mb-2 text-center">Please describe the Positive or Weakness Matters of Ratee</h6>
                                <div class="col-md-6 mb-3 text-center">
                                    <label for="positive" class="form-label fw-semibold fs-6 mb-2">{{ Str::title('POSITIVE MATTERS') }}
                                        <span class="text-danger">(Max. <span id="count_positive">189</span> Character)</span>
                                    </label>
                                    <textarea class="form-control" id="positive" name="positive" rows="5" maxlength="189"
                                        placeholder="Describe the employee's positive aspects..."
                                        {{ $isHrd ? 'disabled' : '' }}>{{ old('positive', $evaluation->positive ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3 text-center">
                                    <label for="weakness" class="form-label fw-semibold fs-6 mb-2">{{ Str::title('WEAKNESS MATTERS') }}
                                        <span class="text-danger">(Max. <span id="count_weakness">189</span> Character)</span>
                                    </label>
                                    <textarea class="form-control" id="weakness" name="weakness" rows="5" maxlength="189"
                                        placeholder="Describe the employee's weaknesses for improvement..."
                                        {{ $isHrd ? 'disabled' : '' }}>{{ old('weakness', $evaluation->weakness ?? '') }}</textarea>
                                </div>
                                <div class="col-12 mb-3 text-center">
                                    <label for="note_hrd" class="form-label fw-semibold fs-6 mb-2">Note from HRD</label>
                                    <textarea class="form-control" id="note_hrd" name="note_hrd" rows="3"
                                        {{ $isHrd ? '' : 'disabled' }}>{{ $evaluation->note_hrd ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Decision --}}
                    @if ($evaluation->purpose == 'Employment Status')
                        <div class="col-12 p-2">
                            <div class="card">
                                <div class="card-header fw-bold text-center text-uppercase">DECISION OF EMPLOYMENT STATUS</div>
                                <div class="card-body row">
                                    <h6 class="card-subtitle mb-2 text-center">
                                        <span class="text-danger">*</span> Grade A or Grade B is possible to be proposed as permanent employee or contract extend.
                                    </h6>
                                    <div class="col-12 mb-3 text-center">
                                        @php $decisionOptions = ['Contract extend', 'Assign as permanent employee', 'Terminated']; @endphp
                                        <select id="decision_employment" name="decision_employment" class="form-select" required>
                                            <option value="" disabled
                                                {{ old('decision_employment', $evaluation->decision_employment ?? '') === '' ? 'selected' : '' }}>
                                                Select an option
                                            </option>
                                            @foreach ($decisionOptions as $value)
                                                <option value="{{ $value }}"
                                                    {{ old('decision_employment', $evaluation->decision_employment ?? '') == $value ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="month_extend_container" class="col-md-6 d-none">
                                        <select id="month_extend" name="month_extend" class="form-select" disabled>
                                            <option value="" disabled selected>Select Month Extend</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('month_extend', $evaluation->month_extend ?? '') == $i ? 'selected' : '' }}>
                                                    {{ $i . ' months' }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div id="decision_reason_container" class="col-md-6 d-none">
                                        <input type="text" id="decision_reason" name="decision_reason" class="form-control"
                                            placeholder="Reason for decision... (Max 100 Character)" maxlength="100"
                                            value="{{ $evaluation->decision_reason ?? '' }}">
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const decisionSelect = document.getElementById('decision_employment');
                                            const monthExtendContainer = document.getElementById('month_extend_container');
                                            const monthExtendSelect = document.getElementById('month_extend');
                                            const decisionReasonContainer = document.getElementById('decision_reason_container');
                                            const decisionReasonInput = document.getElementById('decision_reason');
                                            function toggleMonthExtend() {
                                                const grade = document.getElementById('grade').value;
                                                const decision = decisionSelect.value;
                                                if (decision === 'Contract extend') {
                                                    monthExtendContainer.classList.remove('d-none');
                                                    monthExtendSelect.disabled = false;
                                                    monthExtendSelect.setAttribute('required', 'required');
                                                } else {
                                                    monthExtendContainer.classList.add('d-none');
                                                    monthExtendSelect.disabled = true;
                                                    monthExtendSelect.removeAttribute('required');
                                                    monthExtendSelect.value = '';
                                                }
                                                const showReason = (decision === 'Terminated') || 
                                                                (decision === 'Assign as permanent employee') || 
                                                                ((grade === 'C' || grade === 'D' || grade === 'E') && (decision === 'Contract extend'));
                                                if (showReason) {
                                                    decisionReasonContainer.classList.remove('d-none');
                                                    decisionReasonInput.setAttribute('required', 'required');
                                                } else {
                                                    decisionReasonContainer.classList.add('d-none');
                                                    decisionReasonInput.removeAttribute('required');
                                                    decisionReasonInput.value = ''; 
                                                }
                                            }
                                            decisionSelect.addEventListener('change', toggleMonthExtend);
                                            document.getElementById('grade').addEventListener('change', toggleMonthExtend);
                                            document.getElementById('grade').addEventListener('input', toggleMonthExtend);
                                            toggleMonthExtend();
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 7. Attachments --}}
                    <div class="col-12"><hr><h5 class="text-center">Attachments</h5></div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap mb-0" id="attachment-table">
                                <thead class="align-middle">
                                    <tr class="table-active">
                                        <th scope="col" style="width: 5%;">#</th>
                                        <th scope="col" style="width: 45%;">Attachment Name<span class="text-danger">*</span></th>
                                        <th scope="col" style="width: 40%;">File<span class="text-danger">*</span></th>
                                        <th scope="col" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="attachment-list">
                                    @if (isset($evaluation) && $evaluation->attachments && $evaluation->attachments->count() > 0)
                                        @foreach ($evaluation->attachments as $index => $attachment)
                                            <tr class="existing-attachment-row">
                                                <th scope="row" class="attachment-id">{{ $index + 1 }}</th>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="existing_attachment_names[{{ $attachment->id }}]"
                                                        value="{{ $attachment->name }}" required>
                                                </td>
                                                <td>
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                        class="btn btn-primary" target="_blank">View File</a>
                                                </td>
                                                <td class="attachment-removal">
                                                    <button type="button" class="btn btn-danger remove-existing-attachment-btn"
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
                                                class="btn btn-soft-secondary fw-medium">
                                                <i class="ri-add-fill me-1 align-bottom"></i> Add Attachment
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="row">
                    <div class="col-12">
                        @if ($showButtons)
                            <input type="hidden" name="action_type" id="action_type" value="submit">
                            @if ($isHrd)
                                <button type="button"
                                    class="btn btn-danger w-100 fw-bold text-uppercase mb-2"
                                    data-bs-toggle="modal" data-bs-target="#reviceModal">
                                    <i class="ri-edit-line me-1 align-bottom"></i> Revise
                                </button>
                                <button type="button"
                                    class="btn btn-success w-100 fw-bold text-uppercase"
                                    onclick="handleFormSubmission('submit')">
                                    Approve
                                </button>
                            @else
                                @if ($canDraft)
                                    <button type="button" name="draft" value="1"
                                        class="btn btn-secondary w-100 fw-bold text-uppercase mb-2"
                                        onclick="handleFormSubmission('draft')">DRAFT</button>
                                @endif
                                <button type="button"
                                    class="btn btn-success w-100 fw-bold text-uppercase"
                                    onclick="handleFormSubmission('submit')">
                                    Submit Evaluation
                                </button>
                            @endif
                        @else
                            <button type="button" class="btn btn-light w-100 fw-bold text-uppercase" disabled>
                                Evaluation Already Submitted
                            </button>
                        @endif

                        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmSubmitModalLabel">
                                            {{ $isHrd ? 'Approve Evaluation' : 'Submit Evaluation' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to <b>{{ $isHrd ? 'Approve' : 'Submit' }} Evaluation?</b>
                                        @if (!$isHrd && $role === 'approval1' && is_null($evaluation->approval1_date))
                                            <br>Once submitted, this data will be forwarded to the next approval.
                                        @endif
                                        <div class="mt-3">
                                            <textarea class="form-control" id="approval_reason" name="approval_reason" maxlength="255" rows="3" placeholder="Add your comment or notes here..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                                        <button type="button" class="btn btn-success"
                                            onclick="handleFormSubmission('submit'); document.getElementById('evalForm').submit();">
                                            {{ $isHrd ? 'APPROVE' : 'SUBMIT' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Loading Modal --}}
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">
                            <div class="mt-4">
                                <h4 class="mb-3">Please wait...</h4>
                                <h4 class="mb-3">Do not leave this page</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('evalForm');
                    const requiredElements = form.querySelectorAll('[required]');
                    function toggleRequiredForDraft(isDraft) {
                        requiredElements.forEach(el => {
                            if (isDraft) {
                                el.removeAttribute('required');
                                el.setAttribute('data-draft-removed', 'true');
                            } else {
                                if (el.hasAttribute('data-draft-removed')) {
                                    el.setAttribute('required', 'required');
                                    el.removeAttribute('data-draft-removed');
                                }
                            }
                        });
                    }
                    function handleFormSubmission(action) {
                        document.getElementById('action_type').value = action;
                        if (action === 'draft') {
                            toggleRequiredForDraft(true);
                            $('#staticBackdrop').modal('show');
                            form.submit();
                        } else if (action === 'submit') {
                            toggleRequiredForDraft(false);
                            if (form.checkValidity()) {
                                const submitModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                                submitModal.show();
                            } else {
                                form.reportValidity();
                            }
                        }
                    }
                    window.handleFormSubmission = handleFormSubmission;
                    const finalSubmitButton = document.querySelector('#confirmSubmitModal button.btn-success');
                    if (finalSubmitButton) {
                        finalSubmitButton.onclick = function() {
                            // const reasonInput = document.getElementById('approval_reason');
                            // if (reasonInput.value.trim() === '') {
                            //     reasonInput.setAttribute('required', 'required');
                            //     reasonInput.reportValidity();
                            //     return;
                            // }
                            toggleRequiredForDraft(false);
                            document.getElementById('action_type').value = 'submit';
                            $('#confirmSubmitModal').modal('hide');
                            $('#staticBackdrop').modal('show');
                            form.submit();
                        };
                    }
                    window.validateAndShowModal = () => handleFormSubmission('submit');
                    toggleRequiredForDraft(true);
                });
            </script>
        </div>
    </div>
</div>