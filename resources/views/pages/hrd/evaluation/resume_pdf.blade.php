<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Evaluation</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Calibri', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
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

        .sub-text {
            font-size: 10px;
            font-weight: normal;
            margin-top: 2px;
        }

        .garis_tepi {
            border: 1px solid black;
            margin: 12px;
            padding: 12px;
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
            padding: 3px 5px;
        }

        td.content-table-total {
            padding: 3px 0;
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
    </style>
</head>
<body class="garis_tepi">
    <div class="container">
        <table class="content-table">
            <thead>
                <tr>
                    <th colspan="15" style="font-size: 15px; padding: 10px;">RESUME EVALUATION</th>
                </tr>
                <tr>
                    <th>No.</th>
                    <th>NIK</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Employee</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Position</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Department</th>
                    <th>Purpose</th>
                    <th>Period</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Decision</th>
                    <th>Reason</th>
                    <th>Note HRD</th>
                    <th>KPI</th>
                    <th>Attitude & Performance</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Attendance</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Score</th>
                    <th style="padding-left: 5px; padding-right: 5px;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations as $index => $evaluation)
                <tr>
                    @php
                        $start = optional(\Carbon\Carbon::parse($evaluation->eval_start))->format('d M Y') ?? '-';
                        $end = optional(\Carbon\Carbon::parse($evaluation->eval_end))->format('d M Y') ?? '-';
                        $period = "{$start} - {$end}";
                    @endphp
                    <td class="content-table-text">{{ $index + 1 }}</td>
                    <td class="content-table-text">{{ $evaluation->employee->nik ?? '-' }}</td>
                    <td class="content-table-text comment-left">{{ $evaluation->employee->fullname ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->appraisal_position->nama ?? '-' }}</td>
                    <td class="content-table-text">{{ strtoupper($evaluation->employee->department->name) ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->purpose ?? '-' }}</td>
                    <td class="content-table-text">{{ $period ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->decision_employment ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->decision_reason ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->note_hrd ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->kpi_sc ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->ap_sc ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->attendance_sc ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->total_score ?? '-' }}</td>
                    <td class="content-table-text">{{ $evaluation->grade ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
