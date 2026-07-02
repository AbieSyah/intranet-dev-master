<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Appraisal Form</title>
    <style>
        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-Thin.ttf') }}") format("truetype");
            font-weight: 100;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-ExtraLight.ttf') }}") format("truetype");
            font-weight: 200;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-Light.ttf') }}") format("truetype");
            font-weight: 300;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-Regular.ttf') }}") format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-Medium.ttf') }}") format("truetype");
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-SemiBold.ttf') }}") format("truetype");
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-Bold.ttf') }}") format("truetype");
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-ExtraBold.ttf') }}") format("truetype");
            font-weight: 800;
            font-style: normal;
        }

        @font-face {
            font-family: "NotoSansJP";
            src: url("{{ public_path('assets/fonts/NotoSansJP-Black.ttf') }}") format("truetype");
            font-weight: 900;
            font-style: normal;
        }

        @page {
            size: A4 portrait;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Calibri', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 97.5%;
            padding: 10px;
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
        }

        .header-table td,
        .header-table th {
            border: 1px solid black;
            vertical-align: top;
            font-size: 9px;
        }

        .header-table th {
            text-align: center;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            padding: 3px;
        }

        .info-table td {
            padding: 0;
            border: none;
            font-size: 9px;
        }

        .employee-info p {
            font-size: 9px;
            font-style: italic;
            margin-top: -3px;
            margin-left: 5px;
        }

        .evaluation-info p {
            font-size: 9px;
            margin-top: 0px;
        }

        .main-section {
            font-size: 15px;
            border: 1px solid black;
            padding: 7px 0;
            text-align: center;
            font-weight: bold;
            margin-top: 3px;
        }

        .sub-text {
            font-size: 10px;
            font-weight: normal;
            margin-top: 2px;
        }

        .garis_tepi {
            border: 1px solid black;
            margin: 12px;
            padding: 0;
            box-sizing: border-box;
        }

        table.header {
            border-collapse: collapse;
            width: 100%;
        }

        .footer {
            position: fixed;
            left: 0px;
            bottom: 0px;
            right: 10px;
            height: 0px;
        }

        .footer.first {
            bottom: 20px;
        }

        p.ket {
            font-size: 7px;
            margin-left: 12px;
        }

        h6 {
            font-size: 11px;
            margin: 7px 5px 0px 5px;
        }

        p.sub-title {
            font-size: 8px;
            font-weight: normal;
            margin-top: 2px;
            margin-left: 17px;
            margin-bottom: 2px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
            margin-bottom: 5px;
            text-align: center;
            font-weight: bold;
        }

        .content-table td,
        .content-table th {
            border: 1px solid black;
            vertical-align: middle;
            font-size: 11px;
        }

        td.content-table-text {
            font-weight: normal;
            font-size: 9px;
        }

        td.content-table-total {
            padding: 3px 0;
        }

        .japanese-comment {
            font-family: "NotoSansJP", 'Calibri', sans-serif;
            line-height: 7px;
            /* font-size: 6px !important; */
        }

        .comment-left {
            text-align: left !important;
            padding-left: 3px;
            font-size: 9px !important;
        }

        .inner-page-border {
            border: 1px solid black;
            padding: 10px;
            box-sizing: border-box;
        }

        .sign-page-2 th,
        .sign-page-2 td {
            /* border: 1px solid white !important; */
        }
    </style>
    @php
        function getApprovalData($evaluation, $approvalRole)
        {
            for ($i = 1; $i <= 6; $i++) {
                $approvalAsField = 'approval' . $i . '_as';
                $approvalDateField = 'approval' . $i . '_date';
                $approvalEmployeeRelation = 'approval' . $i;
                if (
                    isset($evaluation->{$approvalAsField}) &&
                    $evaluation->{$approvalAsField} === $approvalRole
                ) {
                    $approvalDate = $evaluation->{$approvalDateField};
                    $formattedDate = null;
                    if ($approvalDate) {
                        $formattedDate = \Carbon\Carbon::parse($approvalDate)->format('Y-m-d H:i');
                    }
                    return [
                        'role' => $approvalEmployeeRelation,
                        'date' => $formattedDate,
                        'employee' => $evaluation->{$approvalEmployeeRelation},
                    ];
                }
            }
            return ['role' => null, 'date' => null, 'employee' => null];
        }

        function generateApprovalQrCode($evaluation, $role, $employee, $date)
        {
            if ($role && $employee && $date) {
                $token = encrypt($evaluation->id . '|' . $role);
                $qrLink = route('evaluation.qrcode.approval', ['token' => $token]);
                $qrCodeSvg = QrCode::size(62)->generate($qrLink);
                $base64Svg = base64_encode($qrCodeSvg);
                return '<img src="data:image/svg+xml;base64,' . $base64Svg . '" height="62" alt="QR Code" />';
            } else {
                return '<div style="height: 62px;"></div>';
            }
        }

        function formatNameForDisplay($fullName, $maxLength = 20)
        {
            if (mb_strlen($fullName) > $maxLength) {
                $nameParts = explode(' ', $fullName);
                $limitedName = '';
                foreach ($nameParts as $part) {
                    if (mb_strlen($limitedName . ' ' . $part) <= $maxLength) {
                        $limitedName .= ($limitedName ? ' ' : '') . $part;
                    } else {
                        break;
                    }
                }
                return $limitedName;
            } else {
                return $fullName;
            }
        }

        $firstEvaluator = getApprovalData($evaluation, '1st Evaluator');
        $secondEvaluator = getApprovalData($evaluation, '2nd Evaluator');
        $thirdEvaluator = getApprovalData($evaluation, '3rd Evaluator');
        $hrdApproval = getApprovalData($evaluation, 'HRD Approval');
        $directorApproval = getApprovalData($evaluation, 'Director');
        $presidentApproval = getApprovalData($evaluation, 'President Director');
    
        $svgs = [
            'check-solid' => public_path('assets/images/check-solid.svg'),
            'square-regular-full' => public_path('assets/images/square-regular-full.svg'),
            'square-check-regular-full' => public_path('assets/images/square-check-regular-full.svg'),
        ];
        $base64Svgs = [];
        foreach ($svgs as $key => $path) {
            if (file_exists($path)) {
                $svgContent = file_get_contents($path);
                $base64Svgs[$key] = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
            } else {
                $base64Svgs[$key] = null;
            }
        }
    @endphp
</head>
<body class="garis_tepi">
    <footer class="footer first">
        <table class="header">
            <tr>
                <td align="left">
                    <p class="ket">01/12/2022</p>
                </td>
                <td align="right">
                    <p class="ket">Form HR-PS-03/01 REV.00</p>
                </td>
            </tr>
        </table>
    </footer>
    <div class="container">
        <table class="header-table">
            <thead>
                <tr>
                    <th colspan="3" style="font-size: 24px; padding: 15px 0; text-decoration: underline;">PERFORMANCE
                        APPRAISAL FORM</th>
                </tr>
                <tr>
                    <th style="width: 217pt !important; word-break: break-all; overflow-wrap: break-word;">Employee Information</th>
                    <th style="width: 167pt !important; word-break: break-all; overflow-wrap: break-word;">Evaluator Information</th>
                    <th style="width: 170pt !important; word-break: break-all; overflow-wrap: break-word;">Evaluation Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="employee-info" style="max-width: 217pt;">
                        <table class="info-table">
                            <tr>
                                <td style="width: 65pt;">Name (Ratee)</td>
                                <td style="width: 3pt;">:</td>
                                <td>{{ formatNameForDisplay($evaluation->employee->fullname ?? '', 30) }}</td>
                            </tr>
                            <tr>
                                <td>Employee ID (NIK)</td>
                                <td>:</td>
                                <td>{{ $evaluation->employee->nik }}</td>
                            </tr>
                            <tr>
                                <td>Department / Section</td>
                                <td>:</td>
                                <td>{{ $evaluation->employee->department->name }}
                                    {{ $evaluation->employee->section ? '/ ' . $evaluation->employee->section->nama : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>{{ $evaluation->appraisal_status }}</td>
                            </tr>
                            <tr>
                                <td>Position</td>
                                <td>:</td>
                            </tr>
                        </table>
                        <p style="min-height: 20px; max-height: 20px;">{{ $evaluation->appraisal_position->nama }}</p>
                    </td>
                    <td class="employee-info" style="max-width: 167pt;">
                        <table class="info-table">
                            <tr>
                                <td style="width: 25pt;">Name</td>
                                <td style="width: 3pt;">:</td>
                                <td style="max-width: 100pt; word-break: break-all; overflow-wrap: break-word;">{{ formatNameForDisplay($evaluation->approval1?->fullname ?? '', 25) }}</td>
                            </tr>
                            <tr>
                                <td style="width: 25pt;">Position</td>
                                <td style="width: 3pt;">:</td>
                                <td style="max-width: 100pt; word-break: break-all; overflow-wrap: break-word;">{{ $evaluation->approval1?->position->nama ?? '' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="evaluation-info" style="max-width: 170pt;">
                        <table class="info-table">
                            <tr>
                                <td>Evaluation Period</td>
                                <td>:</td>
                                <td>{{ \Carbon\Carbon::parse($evaluation->eval_start)->format('d/m/Y') }}
                                    until
                                    {{ \Carbon\Carbon::parse($evaluation->eval_end)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>Evaluation Purpose<br>
                                    <span style="font-size: 7px;">
                                        (mark with 
                                        <img src="{{ $base64Svgs['check-solid'] }}" alt="Checkmark" style="height: 7px;"> 
                                        in selected one)
                                    </span>
                                </td>
                                <td>:</td>
                                <td>
                                    <div style="margin-bottom: 0; line-height: 1;">
                                        @if ($evaluation->purpose == 'Yearly Evaluation')
                                            <img src="{{ $base64Svgs['square-check-regular-full'] }}" alt="Square Check" style="height: 17px; vertical-align: middle;">
                                        @else
                                            <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" style="height: 17px; vertical-align: middle;">
                                        @endif
                                        <span style="vertical-align: middle;">Yearly evaluation</span>
                                    </div>
                                    <div style="margin-top: 0; line-height: 1;">
                                        @if ($evaluation->purpose == 'Employment Status')
                                            <img src="{{ $base64Svgs['square-check-regular-full'] }}" alt="Square Check" style="height: 17px; vertical-align: middle;">
                                        @else
                                            <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" style="height: 17px; vertical-align: middle;">
                                        @endif
                                        <span style="vertical-align: middle;">Employment status</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="main-section">
            EVALUATION ASPECT
            <div class="sub-text">(Decision is a Rights & Authority Board Of Directors)</div>
        </div>
        <h6>1. KEY PERFORMANCE INDICATOR (KPI)</h6>
        <p class="sub-title">Based on KPI Achievement</p>
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Weight</th>
                    <th style="width: 25%;">Achievement</th>
                    <th style="width: 5%;">Score</th>
                    <th style="width: 45%;">Comment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ ($evaluation->kpi_w ?? 0) > 0 ? $evaluation->kpi_w : 'Not Available' }}
                    </td>
                    <td>
                        {{ ($evaluation->kpi_w ?? 0) > 0 ? number_format($evaluation->kpi_s, 2, ',', '.') . '%' : '' }}
                    </td>
                    <td>
                        {{ ($evaluation->kpi_w ?? 0) > 0 ? number_format($evaluation->kpi_sc, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="comment-left">
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $evaluation->kpi_c);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span class="{{ $commentClass }}">{{ $evaluation->kpi_c }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <h6>2. ATTITUDE & PERFORMANCE</h6>
        <p class="sub-title">Based on Competency Dictionary</p>
        <table class="content-table">
            <thead>
                <tr>
                    <th colspan="2" style="width: 25%;">Weight</th>
                    <th colspan="2" style="width: 20%;">Scoring Scale</th>
                    <th rowspan="2" style="width: 5%; vertical-align: middle;">Achievement</th>
                    <th rowspan="2" style="width: 5%; vertical-align: middle;">Score</th>
                    <th rowspan="2" style="width: 45%; vertical-align: middle;">Comment</th>
                </tr>
                <tr>
                    <td colspan="2">{{ $evaluation->ap_w }}</td>
                    <td class="content-table-text">Min</td>
                    <td class="content-table-text">Max</td>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = [
                        [
                            'label' => 'Managerial',
                            'prefix' => 'ap_managerial',
                            'max' => $evaluation->ap_managerial_w,
                        ],
                        [
                            'label' => 'Ability To Response / "HORENSO"',
                            'prefix' => 'ap_ability_response',
                            'max' => $evaluation->ap_ability_response_w,
                        ],
                        [
                            'label' => 'Leadership',
                            'prefix' => 'ap_leadership',
                            'max' => $evaluation->ap_leadership_w,
                        ],
                        [
                            'label' => 'Accuracy',
                            'prefix' => 'ap_accuracy',
                            'max' => $evaluation->ap_accuracy_w,
                        ],
                        [
                            'label' => 'Capability',
                            'prefix' => 'ap_capability',
                            'max' => $evaluation->ap_capability_w,
                        ],
                        [
                            'label' => 'Initiative',
                            'prefix' => 'ap_initiative',
                            'max' => $evaluation->ap_initiative_w,
                        ],
                        [
                            'label' => 'Kaizen',
                            'prefix' => 'ap_kaizen',
                            'max' => $evaluation->ap_kaizen_w,
                        ],
                        [
                            'label' => 'Responsibility',
                            'prefix' => 'ap_responsibility',
                            'max' => $evaluation->ap_responsibility_w,
                        ],
                        [
                            'label' => 'Discipline',
                            'prefix' => 'ap_discipline',
                            'max' => $evaluation->ap_discipline_w,
                        ],
                        [
                            'label' => 'Cooperation',
                            'prefix' => 'ap_cooperation',
                            'max' => $evaluation->ap_cooperation_w,
                        ],
                    ];
                @endphp

                @foreach ($items as $index => $item)
                    <tr>
                        <td style="width: 3%;" class="content-table-text">{{ $index + 1 }}</td>
                        <td style="width: 22%; text-align: left;" class="content-table-text">
                            {{ strtoupper($item['label']) }}
                        </td>
                        @php
                            $w_key = $item['prefix'] . '_w';
                            $s_key = $item['prefix'] . '_s';
                            $sc_key = $item['prefix'] . '_sc';
                            $c_key = $item['prefix'] . '_c';
                        @endphp
                        <td class="content-table-text">{{ ($evaluation->{$w_key} ?? 0) > 0 ? $evaluation->{$w_key} * 0.2 : 'n/a' }}
                        </td>
                        <td class="content-table-text">{{ ($evaluation->{$w_key} ?? 0) > 0 ? $evaluation->{$w_key} : 'n/a' }}</td>
                        <td class="content-table-text">
                            {{ ($evaluation->{$w_key} ?? 0) > 0 ? intval($evaluation->{$s_key}) . '%' : '' }}</td>
                        <td>
                            {{ ($evaluation->{$w_key} ?? 0) > 0 ? number_format($evaluation->{$sc_key}, 2, ',', '.') : '' }}</td>
                        </td>
                        <td class="content-table-text comment-left">
                            @php
                                $isJapanese = preg_match('/[^\x00-\x7F]/u', $evaluation->{$c_key});
                                $commentClass = $isJapanese ? 'japanese-comment' : '';
                            @endphp
                            <span class="{{ $commentClass }}">{{ $evaluation->{$c_key} }}</span>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5" style="width: 50%;">Total Score of Attitude & Performance</th>
                    <th style="width: 5%;">
                        {{ !is_null($evaluation->ap_sc) && $evaluation->ap_sc > 0 ? number_format($evaluation->ap_sc, 2, ',', '.') : '0,00' }}
                    </th>
                    <th style="border-right: 1px solid white !important; border-bottom: 1px solid white !important;">
                    </th>
                </tr>
                <tr>
                    <th colspan="5" style="width: 50%;">Achievement</th>
                    <th style="width: 5%;">
                        {{ !is_null($evaluation->ap_s) && $evaluation->ap_s > 0 ? number_format($evaluation->ap_s, 2, ',', '.') : '0,00' }}
                    </th>
                    <th style="border-right: 1px solid white !important; border-bottom: 1px solid white !important;">
                    </th>
                </tr>
            </tbody>
        </table>
        <h6>3. ATTENDANCE</h6>
        <p class="sub-title">Based on Attendance Score</p>
        <table class="content-table" style="margin-bottom: 3px;">
            <thead>
                <tr>
                    <th style="width: 25%;">Weight</th>
                    <th style="width: 25%;">Achievement</th>
                    <th style="width: 5%;">Score</th>
                    <th style="width: 45%;">Comment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ ($evaluation->attendance_w ?? 0) > 0 ? $evaluation->attendance_w : 'Not Available' }}
                    </td>
                    <td>
                        {{ ($evaluation->attendance_w ?? 0) > 0 ? number_format($evaluation->attendance_s, 2, ',', '.') . '%' : '' }}
                    </td>
                    <td>
                        {{ ($evaluation->attendance_w ?? 0) > 0 ? number_format($evaluation->attendance_sc, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="comment-left">
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $evaluation->attendance_c);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span class="{{ $commentClass }}">{{ $evaluation->attendance_c }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="content-table" style="margin-bottom: 3px;">
            <thead>
                <tr>
                    <th colspan="6" style="font-size: 15px; padding: 7px 0; font-weight: bold;">TOTAL SCORE OF
                        EVALUATION ASPECT</th>
                </tr>
                <tr>
                    <th style="width: 16%;">KPI Score</th>
                    <th style="width: 16.5%;">Attitude & Performance Score</th>
                    <th style="width: 17%;">Attendance Score</th>
                    <th style="width: 16.5%;">Minus points from warning letter</th>
                    <th style="width: 16.5%;">Total Score</th>
                    <th style="width: 17.5%;">Evaluation Grade</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="content-table-total">
                        {{ !is_null($evaluation->kpi_sc) && $evaluation->kpi_sc > 0 ? number_format($evaluation->kpi_sc, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="content-table-total">
                        {{ !is_null($evaluation->ap_s) && $evaluation->ap_s > 0 ? number_format($evaluation->ap_s, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="content-table-total">
                        {{ !is_null($evaluation->attendance_sc) && $evaluation->attendance_sc > 0 ? number_format($evaluation->attendance_sc, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="content-table-total">
                        @php
                            $minusPoin = $evaluation->minus_poin ?? 0;
                        @endphp
                        {{ $minusPoin > 0 ? number_format($minusPoin, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="content-table-total">
                        {{ !is_null($evaluation->total_score) && $evaluation->total_score > 0 ? number_format($evaluation->total_score, 2, ',', '.') : '0,00' }}
                    </td>
                    <td class="content-table-total" style="font-size: 24px;">{{ $evaluation->grade }}</td>
                </tr>
            </tbody>
        </table>
        <table class="content-table" style="margin-bottom: 3px">
            <thead>
                <tr>
                    <th style="width: 16%; font-size: 9px; font-weight: bold;">COMMENT</th>
                    <th style="width: 42%; font-size: 9px; font-weight: bold;">POSITIVE MATTERS</th>
                    <th style="width: 42%; font-size: 9px; font-weight: bold;">WEAKNESS MATTERS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="content-table-text"><br>Please describe the <br>Positive or Weakness <br>Matters of
                        Ratee<br><br></td>
                    <td class="content-table-text" style="text-align: left; vertical-align: top; padding: 3px; word-break: keep-all;">
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $evaluation->positive);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span class="{{ $commentClass }}">{!! nl2br($evaluation->positive) !!}</span>
                    </td>
                    <td class="content-table-text" style="text-align: left; vertical-align: top; padding: 3px; word-break: keep-all;">
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $evaluation->weakness);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span class="{{ $commentClass }}">{!! nl2br($evaluation->weakness) !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="content-table" style="margin-bottom: 3px;">
            <tbody>
                <tr>
                    <td class="content-table-text" style="width: 16%; font-weight: bold; padding: 5px 0;">Note from
                        <br>HRD
                    </td>
                    <td class="content-table-text" style="width: 84%; text-align: left; vertical-align: top; padding: 3px; word-break: keep-all;">
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $evaluation->note_hrd);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span class="{{ $commentClass }}">{!! nl2br($evaluation->note_hrd) !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="content-table" style="margin-bottom: 3px;">
            <thead>
                <tr>
                    <th colspan="3" style="padding: 3px 0; ">
                        <span style="font-size: 15px; font-weight: bold;">DECISION OF EMPLOYMENT STATUS</span><br>
                        <span style="font-size: 10px; font-weight: normal;">(Filled only for employment status
                            purposes)</span><br>
                        <span style="font-size: 10px; font-weight: normal;">*) Grade A or Grade B is possible to be
                            proposed as permanent employee or contract extend.</span>
                    </th>
                </tr>
                <tr>
                    <th colspan="3"
                        style="font-size: 7px; font-weight: normal; padding-top: 3px; border-bottom: 1px solid white;">
                        (mark with <img src="{{ $base64Svgs['check-solid'] }}" alt="Checkmark" style="height: 7px;"> in selected one)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: normal; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @if ($evaluation->decision_employment == 'Contract extend')
                            <img src="{{ $base64Svgs['square-check-regular-full'] }}" alt="Square Check" style="height: 17px; vertical-align: bottom; margin-bottom: 1px;">
                        @else
                            <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" style="height: 17px; vertical-align: bottom; margin-bottom: 1px;">
                        @endif
                        Contract extend *)
                        @if ($evaluation->decision_employment == 'Contract extend' && $evaluation->month_extend > 0)
                            ({{ $evaluation->month_extend }} months)
                        @endif
                    </td>
                    <td style="font-weight: normal; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @if ($evaluation->decision_employment == 'Assign as permanent employee')
                            <img src="{{ $base64Svgs['square-check-regular-full'] }}" alt="Square Check" style="height: 17px; vertical-align: bottom; margin-bottom: 1px;">
                        @else
                            <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" style="height: 17px; vertical-align: bottom; margin-bottom: 1px;">
                        @endif
                        Assign as permanent employee *)</td>
                    <td style="font-weight: normal; border-bottom: 1px solid white;">
                        @if ($evaluation->decision_employment == 'Terminated')
                            <img src="{{ $base64Svgs['square-check-regular-full'] }}" alt="Square Check" style="height: 17px; vertical-align: bottom; margin-bottom: 1px;">
                        @else
                            <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" style="height: 17px; vertical-align: bottom; margin-bottom: 1px;">
                        @endif
                        Terminated</td>
                </tr>
                <tr>
                    <th colspan="3" style="font-weight: normal; border-top: 1px solid white;"></th>
                </tr>
            </tbody>
        </table>
        {{-- Check 3rd Evaluator --}}
        @php
            $thirdEvaluatorFound = false;
            for ($i = 1; $i <= 6; $i++) {
                $approvalAsField = 'approval' . $i . '_as';
                if (isset($evaluation->{$approvalAsField}) && $evaluation->{$approvalAsField} === '3rd Evaluator') {
                    $thirdEvaluatorFound = true;
                    break;
                }
            }
        @endphp
        @if ($thirdEvaluatorFound)
        {{-- Non Standart (3rd Evaluator) --}}
        <table class="content-table" style="margin-bottom: 3px; text-align: left;">
            <thead>
                <tr>
                    <th colspan="4"
                        style="padding-top: 2.8px; font-weight: normal; border-bottom: 1px solid white;"></th>
                </tr>
                <tr>
                    <th
                        style="width: 24%; text-align: left; padding-top: 7px; padding-left: 15px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Prepared by Evaluator 1<sup style="margin-left: 0.7px;">st</sup></th>
                    <th
                        style="width: 24%; text-align: left; padding-top: 7px; padding-left: 15px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Confirmed by Evaluator 2<sup style="margin-left: 0.9px;">nd</sup></th>
                    <th
                        style="width: 24%; text-align: left; padding-top: 7px; padding-left: 15px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Confirmed by Evaluator 3<sup style="margin-left: 0.9px;">rd</sup></th>
                    <th
                        style="width: 28%; text-align: left; padding-top: 7px; padding-left: 15px; font-size: 9px; border-bottom: 1px solid white;">
                        Approved by HRD & GA Department Head,</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, '1st Evaluator');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>

                    <td style="padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, '2nd Evaluator');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>

                    <td style="padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, '3rd Evaluator');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>

                    <td style="padding-left: 15px; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, 'HRD Approval');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 9px; padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        <u>Name: {{ formatNameForDisplay($firstEvaluator['employee']?->fullname ?? '') }}&nbsp;</u>
                    </td>
                    <td style="font-size: 9px; padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        <u>Name: {{ formatNameForDisplay($secondEvaluator['employee']?->fullname ?? '') }}&nbsp;</u>
                    </td>
                    <td style="font-size: 9px; padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        <u>Name: {{ formatNameForDisplay($thirdEvaluator['employee']?->fullname ?? '') }}&nbsp;</u>
                    </td>
                    <td style="font-size: 9px; padding-left: 15px; border-bottom: 1px solid white;">
                        <u>Name: {{ formatNameForDisplay($hrdApproval['employee']?->fullname ?? '') }}&nbsp;</u>
                    </td>
                </tr>
                <tr>
                    <td
                        style="font-size: 9px; padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Date: {{ $firstEvaluator['date'] ?? '' }}</td>
                    <td
                        style="font-size: 9px; padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Date: {{ $secondEvaluator['date'] ?? '' }}</td>
                    <td
                        style="font-size: 9px; padding-left: 15px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Date: {{ $thirdEvaluator['date'] ?? '' }}</td>
                    <td style="font-size: 9px; padding-left: 15px; border-bottom: 1px solid white;">Date:
                        {{ $hrdApproval['date'] ?? '' }}</td>
                </tr>
                <tr>
                    <th colspan="4" style="padding-bottom: 5px; font-weight: normal; border-top: 1px solid white;">
                    </th>
                </tr>
            </tbody>
        </table>
        {{-- End Non Standart (3rd Evaluator) --}}
        @else
        {{-- Standart (Non 3rd Evaluator) --}}
        <table class="content-table" style="margin-bottom: 3px; text-align: left;">
            <thead>
                <tr>
                    <th colspan="3"
                        style="padding-top: 2.8px; font-weight: normal; border-bottom: 1px solid white;"></th>
                </tr>
                <tr>
                    <th
                        style="width: 33%; text-align: left; padding-top: 7px; padding-left: 25px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Prepared by Evaluator 1<sup style="margin-left: 0.7px;">st</sup></th>
                    <th
                        style="width: 34%; text-align: left; padding-top: 7px; padding-left: 25px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Confirmed by Evaluator 2<sup style="margin-left: 0.9px;">nd</sup></th>
                    <th
                        style="width: 33%; text-align: left; padding-top: 7px; padding-left: 25px; font-size: 9px; border-bottom: 1px solid white;">
                        Approved by HRD & GA Department Head,</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, '1st Evaluator');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>
                    <td style="padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, '2nd Evaluator');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>
                    <td style="padding-left: 25px; border-bottom: 1px solid white;">
                        @php
                            $data = getApprovalData($evaluation, 'HRD Approval');
                        @endphp
                        {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                    </td>
                </tr>
                <tr>
                    <td
                        style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        <u>Name: {{ formatNameForDisplay($firstEvaluator['employee']?->fullname ?? '', 30) }}&nbsp;</u>
                    </td>
                    <td
                        style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        <u>Name: {{ formatNameForDisplay($secondEvaluator['employee']?->fullname ?? '', 30) }}&nbsp;</u>
                    </td>
                    <td style="font-size: 9px; padding-left: 25px; border-bottom: 1px solid white;"><u>Name:
                            {{ formatNameForDisplay($hrdApproval['employee']?->fullname ?? '', 30) }}&nbsp;</u></td>
                </tr>
                <tr>
                    <td
                        style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Date: {{ $firstEvaluator['date'] ?? '' }}</td>
                    <td
                        style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                        Date: {{ $secondEvaluator['date'] ?? '' }}</td>
                    <td style="font-size: 9px; padding-left: 25px; border-bottom: 1px solid white;">Date:
                        {{ $hrdApproval['date'] ?? '' }}</td>
                </tr>
                <tr>
                    <th colspan="3" style="padding-bottom: 5px; font-weight: normal; border-top: 1px solid white;">
                    </th>
                </tr>
            </tbody>
        </table>
        {{-- End Standart (Non 3rd Evaluator) --}}
        @endif

        {{-- Page 2 --}}
        <div style="page-break-after: always;">
        </div>
        <div style="position: relative; height: 100%;">
            <div style="position: absolute; bottom: 0; width: 100%;">
                <table class="content-table sign-page-2" style="margin-bottom: 3px; text-align: left;">
                    <thead>
                        <tr>
                            <th colspan="3" style="padding-top: 2.8px; font-weight: normal; border-bottom: 1px solid white;"></th>
                        </tr>
                        <tr>
                            <th style="width: 33%; text-align: left; padding-top: 7px; padding-left: 25px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">Acknowledge,<br><span style="font-weight: normal;">Director</span></th>
                            <th style="width: 34%; text-align: left; padding-top: 7px; padding-left: 25px; font-size: 9px; border-right: 1px solid white; border-bottom: 1px solid white;">Acknowledge,<br><span style="font-weight: normal;">President Director</span></th>
                            <th style="width: 33%; text-align: left; padding-top: 7px; padding-left: 25px; font-size: 9px; border-bottom: 1px solid white;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                                @php
                                    $data = getApprovalData($evaluation, 'Director');
                                @endphp
                                {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                            </td>
                            <td style="padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">
                                @php
                                    $data = getApprovalData($evaluation, 'President Director');
                                @endphp
                                {!! generateApprovalQrCode($evaluation, $data['role'], $data['employee'], $data['date']) !!}
                            </td>
                            <td style="padding-left: 25px; border-bottom: 1px solid white;"></td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;"><u>Name: {{ $directorApproval['employee']?->fullname ?? '' }}&nbsp;</u></td>
                            <td style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;"><u>Name: {{ $presidentApproval['employee']?->fullname ?? '' }}&nbsp;</u></td>
                            <td style="font-size: 9px; padding-left: 25px; border-bottom: 1px solid white;"></td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">Date: {{ $directorApproval['date'] ?? '' }}</td>
                            <td style="font-size: 9px; padding-left: 25px; border-right: 1px solid white; border-bottom: 1px solid white;">Date: {{ $presidentApproval['date'] ?? '' }}</td>
                            <td style="font-size: 9px; padding-left: 25px; border-bottom: 1px solid white;"></td>
                        </tr>
                        <tr>
                            <th colspan="3" style="padding-bottom: 5px; font-weight: normal; border-top: 1px solid white;"></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
