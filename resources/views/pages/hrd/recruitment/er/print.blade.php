<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Requisition Form</title>
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
            counter-reset: page 1;
        }

        body {
            font-family: 'Calibri', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 97.5%;
            padding: 20px;
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
            /* border: 1px solid black; */
            font-size: 10px;
        }

        .sign-table {
            /* width: 100%; */
            border-collapse: collapse;
            border: 1px solid black;
        }

        .sign-table td {
            padding: 1px 5px;
            border: 1px solid black;
            font-size: 12px;
            text-align: center;
            font-weight: bold;
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
            font-size: 18px;
            padding: 15px 0;
            text-align: center;
            font-weight: bold;
        }

        .no-pengajuan {
            font-size: 9px;
            text-align: center;
            padding-bottom: 4px;
            border-bottom: 1.5px solid black;
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
            right: 30px;
            height: 0px;
        }

        .footer.first {
            bottom: 33px;
        }

        p.ket {
            font-size: 7px;
            margin-left: 32px;
        }

        h6 {
            font-size: 11px;
            text-align: center;
            background-color: #BFBFBF;
            margin: 0px;
            padding: 2px 0px;
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

        img.svg-box {
            height: 15px;
            width: 15px;
            display: inline-block !important;
            vertical-align: bottom !important;
            margin: 0px !important;
            padding: 0px !important;
        }

        .header-page {
            position: fixed;
            left: 0px;
            bottom: 0px;
            right: 30px;
            height: 0px;
        }

        .header-page.first {
            top: 7px;
        }

        .page-number-content:after {
            content: "Halaman " counter(page) " dari 2";
        }
    </style>
    @php
        function getApprovalData($er, $approvalRole)
        {
            for ($i = 1; $i <= 6; $i++) {
                $approvalAsField = 'approval' . $i . '_as';
                $approvalDateField = 'approval' . $i . '_date';
                $approvalEmployeeRelation = 'approval' . $i;
                if (isset($er->{$approvalAsField}) && $er->{$approvalAsField} === $approvalRole) {
                    $approvalDate = $er->{$approvalDateField};
                    $formattedDate = null;
                    if ($approvalDate) {
                        $formattedDate = \Carbon\Carbon::parse($approvalDate)->format('Y-m-d H:i');
                    }
                    return [
                        'role' => $approvalEmployeeRelation,
                        'date' => $formattedDate,
                        'employee' => $er->{$approvalEmployeeRelation},
                    ];
                }
            }
            return ['role' => null, 'date' => null, 'employee' => null];
        }

        function generateApprovalQrCode($er, $role, $employee, $date)
        {
            if ($role && $employee && $date) {
                $token = encrypt($er->id . '|' . $role);
                $qrLink = route('employee-requisition.qrcode.approval', ['token' => $token]);
                $qrCodeSvg = QrCode::size(100)->generate($qrLink);
                $base64Svg = base64_encode($qrCodeSvg);
                return '<img src="data:image/svg+xml;base64,' . $base64Svg . '" height="100" alt="QR Code" />';
            } else {
                return '<div style="height: 100px;"></div>';
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

        $checker = getApprovalData($er, 'Checker');
        $approval = getApprovalData($er, 'Approval');
        $directorApproval = getApprovalData($er, 'Director');
        $presidentApproval = getApprovalData($er, 'President Director');

        $svgs = [
            'check-solid' => public_path('assets/images/check-solid.svg'),
            'square-regular-full' => public_path('assets/images/square-regular-full.svg'),
            'square-check-regular-full' => public_path('assets/images/square-check-regular-full.svg'),
            'square-xmark-regular-full' => public_path('assets/images/square-xmark-regular-full.svg'),
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
    <footer class="header-page first">
        <table class="header">
            <tr>
                <td align="right">
                    <p class="ket page-number-content"></p>
                </td>
            </tr>
        </table>
    </footer>
    <footer class="footer first">
        <table class="header">
            <tr>
                <td align="left">
                    <p class="ket">01/12/2022</p>
                </td>
                <td align="right">
                    <p class="ket">Form HR-PS-01/01 REV.00</p>
                </td>
            </tr>
        </table>
    </footer>
    <div class="main-section">
        <span style="text-decoration: underline;">FORMULIR PERMOHONAN PERMINTAAN KARYAWAN</span>
        <br><span style="font-style: italic;">EMPLOYEE REQUISITION FORM</span>
    </div>
    <div class="no-pengajuan">Nomor Pengajuan : <span
            style="text-decoration: underline; font-weight: bold;">{{ $er->no_pengajuan ?? '' }}</span></div>
    <h6>PEMOHON / <span style="font-style: italic;">APPLICANT</span></h6>
    <table class="info-table">
        <tr>
            <td style="width: 120pt;">Nama Pemohon / <span style="font-style: italic;">Applicant Name</span></td>
            <td style="width: 5pt;">:</td>
            <td>{{ $er->applicant->fullname ?? 'NA' }}</td>
        </tr>
        <tr>
            <td>Jabatan / <span style="font-style: italic;">Position</span></td>
            <td>:</td>
            <td>{{ $er->applicant->position->nama ?? 'NA' }}</td>
        </tr>
        <tr>
            <td>Departemen / <span style="font-style: italic;">Department</span></td>
            <td>:</td>
            <td>{{ $er->applicant->department->name ?? 'NA' }}</td>
        </tr>
    </table>
    <h6>PENEMPATAN KARYAWAN / <span style="font-style: italic;">PLACEMENT OF EMPLOYEE</span></h6>
    <table class="info-table">
        <tr>
            <td style="width: 120pt;">Kebutuhan / <span style="font-style: italic;">Needs</span></td>
            <td style="width: 5pt;">:</td>
            <td style="width: 260pt;">{{ $er->needs ?? 0 }} Orang / <span style="font-style: italic;">Persons</span>
            </td>
            <td style="width: 60pt;">Bagian / <span style="font-style: italic;">Section</span></td>
            <td style="width: 5pt;">:</td>
            <td>{{ $er->section->nama ?? 'NA' }}</td>
        </tr>
        <tr>
            <td style="width: 120pt;">Jabatan / <span style="font-style: italic;">Position</span></td>
            <td style="width: 5pt;">:</td>
            <td style="width: 260pt; height: 12px; overflow: hidden; display: block;">{{ $er->position->nama ?? 'NA' }}</td>
            <td style="width: 60pt;">Area / <span style="font-style: italic;">Area</span></td>
            <td style="width: 5pt;">:</td>
            <td>{{ $er->area->name ?? 'NA' }}</td>
        </tr>
        <tr>
            <td>Departemen / <span style="font-style: italic;">Department</span></td>
            <td>:</td>
            <td colspan="4">{{ $er->department->name ?? 'NA' }}</td>
        </tr>
    </table>
    <h6>ALASAN PERMINTAAN / <span style="font-style: italic;">REASON OF REQUISITION</span></h6>
    <table class="info-table">
        <tr>
            <td colspan="5" rowspan="2" style="vertical-align: top;">
                <span>
                    @if ($er->reason_requisition == 'Tambahan / Additional')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Tambahan / <span style="font-style: italic;">Additional</span>
                </span>
                <br>
                <span>
                    @if ($er->reason_requisition == 'Penggantian / Replacement')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Penggantian / <span style="font-style: italic;">Replacement</span>
                </span>
            </td>
            <td rowspan="5" style="vertical-align: top;">
                <span>
                    @if ($er->reason_replacement == 'Mengundurkan diri / Resign')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Mengundurkan diri / <span style="font-style: italic;">Resign</span>
                </span>
                <br>
                <span>
                    @if ($er->reason_replacement == 'Kontrak Habis / End Contract')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Kontrak Habis / <span style="font-style: italic;">End Contract</span>
                </span>
                <br>
                <span>
                    @if ($er->reason_replacement == 'Pensiun / Pension')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Pensiun / <span style="font-style: italic;">Pension</span>
                </span>
                <br>
                <span>
                    @if ($er->reason_replacement == 'PHK / Dismissal')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    PHK / <span style="font-style: italic;">Dismissal</span>
                </span>
                <br>
                <span>
                    @if ($er->reason_replacement == 'Lainnya / Others')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark" class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Lainnya / <span style="font-style: italic;">Others:
                        {{ $er->reason_replacement_other ?? '' }}</span>
                </span>
            </td>
        </tr>
        <tr></tr>
        <tr style="vertical-align: top;">
            <td rowspan="3" style="width: 120pt; font-size: 9px;">
                <span>Jika penggantian, nama yang diganti /</span>
                <br>
                <span style="font-style: italic; display: inline-block; margin-top: 3px;">If replacement, person
                    replaced</span>
            </td>
            <td rowspan="3" style="width: 5pt;">:</td>
            <td rowspan="3" style="width: 102pt;">{{ $er->person_replace->fullname ?? '...............................................' }}</td>
            <td rowspan="3" style="width: 77pt; font-size: 9px;">
                <span>Alasan Penggantian /</span>
                <br>
                <span style="font-style: italic; display: inline-block; margin-top: 3px;">Reason of Replacement</span>
            </td>
            <td rowspan="3" style="width: 5pt;">:</td>
        </tr>
        <tr></tr>
        <tr></tr>
    </table>
    <h6 style="margin-top: -5px;">STATUS KARYAWAN / <span style="font-style: italic;">EMPLOYEE STATUS</span></h6>
    <table class="info-table">
        <tr>
            <td rowspan="3" style="vertical-align: top; width: 420px;">
                <span>
                    @if ($er->employee_status == 'Percobaan / Probation')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Percobaan / <span style="font-style: italic;">Probation</span>
                </span>
                <br>
                <span>
                    @if ($er->employee_status == 'Kontrak / Contract')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Kontrak / <span style="font-style: italic;">Contract</span>
                </span>
                <br>
                <span>
                    @if ($er->employee_status == 'Alih Daya / Outsourcing')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Alih Daya / <span style="font-style: italic;">Outsourcing</span>
                </span>
            </td>
            <td rowspan="2" style="vertical-align: top;">
                <span>
                    @if ($er->employee_status == 'Magang / Internship')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Magang / <span style="font-style: italic;">Internship</span>
                </span>
            </td>
        </tr>
        <tr></tr>
        <tr style="vertical-align: top;">
            <td style="width: 115pt;">
                Periode Kontrak / <span style="font-style: italic;">Contract Period</span>
            </td>
            <td style="width: 5pt;">:</td>
            <td>{{ $er->contract_period ?? '.....' }} Bulan / <span style="font-style: italic;">Months</span></td>
        </tr>
    </table>
    <h6>PERSYARATAN KARYAWAN / <span style="font-style: italic;">EMPLOYEE REQUIREMENTS</span></h6>
    <table class="info-table">
        <tr>
            <td style="font-weight: bold;">PENDIDIKAN / <span style="font-style: italic;">EDUCATIONAL
                    BACKGROUND</span></td>
            <td style="font-weight: bold;">JENIS KELAMIN / <span style="font-style: italic;">GENDER</span></td>
        </tr>
        <tr>
            {{-- Education --}}
            <td rowspan="5" style="vertical-align: top; width: 420px;">
                <table style="width: 100%; border-collapse: collapse; margin: 0; padding: 0;">
                    <tr style="margin: 0; padding: 0;">
                        <td style="width: 160px; vertical-align: top; padding: 0;">
                            @if ($er->requiresEducation('SMA / MA / SMK'))
                                <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                                    class="svg-box">
                            @else
                                <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                            @endif
                            <span style="position: relative; top: 1px;">SMA / MA / SMK</span>
                        </td>
                        <td style="width: 260px; vertical-align: top; padding: 0;">
                            <table class="info-table" style="width: 100%; margin: 0px; padding: 0px;">
                                <tr>
                                    <td style="width: 75px; padding: 0; vertical-align: top;">
                                        Jurusan / <span style="font-style: italic;">Major</span>
                                    </td>
                                    <td style="width: 5px; padding: 0; vertical-align: top;">:</td>
                                    <td style="width: auto; padding: 0;">
                                        {{ $er->educationalRequirements->firstWhere('name', 'SMA / MA / SMK')?->pivot->major ?? '........................................................' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr style="margin: 0; padding: 0;">
                        <td style="width: 160px; vertical-align: top; padding: 0;">
                            @if ($er->requiresEducation('Diploma / Diploma Degree'))
                                <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                                    class="svg-box">
                            @else
                                <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                            @endif
                            <span style="position: relative; top: 1px;">Diploma / <span style="font-style: italic;">Diploma Degree</span></span>
                        </td>
                        <td style="width: 260px; vertical-align: top; padding: 0;">
                            <table class="info-table" style="width: 100%; margin: 0px; padding: 0px;">
                                <tr>
                                    <td style="width: 75px; padding: 0; vertical-align: top;">
                                        Jurusan / <span style="font-style: italic;">Major</span>
                                    </td>
                                    <td style="width: 5px; padding: 0; vertical-align: top;">:</td>
                                    <td style="width: auto; padding: 0;">
                                        {{ $er->educationalRequirements->firstWhere('name', 'Diploma / Diploma Degree')?->pivot->major ?? '........................................................' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr style="margin: 0; padding: 0;">
                        <td style="width: 160px; vertical-align: top; padding: 0;">
                            @if ($er->requiresEducation('Sarjana / Bachelor Degree'))
                                <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                                    class="svg-box">
                            @else
                                <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                            @endif
                            <span style="position: relative; top: 1px;">Sarjana / <span style="font-style: italic;">Bachelor Degree</span></span>
                        </td>
                        <td style="width: 260px; vertical-align: top; padding: 0;">
                            <table class="info-table" style="width: 100%; margin: 0px; padding: 0px;">
                                <tr>
                                    <td style="width: 75px; padding: 0; vertical-align: top;">
                                        Jurusan / <span style="font-style: italic;">Major</span>
                                    </td>
                                    <td style="width: 5px; padding: 0; vertical-align: top;">:</td>
                                    <td style="width: auto; padding: 0;">
                                        {{ $er->educationalRequirements->firstWhere('name', 'Sarjana / Bachelor Degree')?->pivot->major ?? '........................................................' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr style="margin: 0; padding: 0;">
                        <td style="width: 160px; vertical-align: top; padding: 0;">
                            @if ($er->requiresEducation('Profesi / Profession Program'))
                                <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                                    class="svg-box">
                            @else
                                <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                            @endif
                            <span style="position: relative; top: 1px;">Profesi / <span style="font-style: italic;">Profession Program</span></span>
                        </td>
                        <td style="width: 260px; vertical-align: top; padding: 0;">
                            <table class="info-table" style="width: 100%; margin: 0px; padding: 0px;">
                                <tr>
                                    <td style="width: 75px; padding: 0; vertical-align: top;">
                                        Jurusan / <span style="font-style: italic;">Major</span>
                                    </td>
                                    <td style="width: 5px; padding: 0; vertical-align: top;">:</td>
                                    <td style="width: auto; padding: 0;">
                                        {{ $er->educationalRequirements->firstWhere('name', 'Profesi / Profession Program')?->pivot->major ?? '........................................................' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr style="margin: 0; padding: 0;">
                        <td colspan="2" style="width: 160px; vertical-align: top; padding: 0;">
                            @if ($er->requiresEducation('Lainnya / Others'))
                                <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                                    class="svg-box">
                            @else
                                <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                            @endif
                            <span style="position: relative; top: 1px;">Lainnya / <span style="font-style: italic;">Others</span> : 
                            {{ $er->educationalRequirements->firstWhere('name', 'Lainnya / Others')?->pivot->major ?? '' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            {{-- Gender --}}
            <td rowspan="2" style="vertical-align: top;">
                <span style="width: 130px; display: inline-block;">
                    @if ($er->genderRequirements->contains('gender_name', 'Pria / Male'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Pria / <span style="font-style: italic;">Male</span>
                </span>
                <span style="display: inline-block;">
                    Usia / <span style="font-style: italic;">Age</span> :
                    <span style="width: 50px;">
                        {{ $er->genderRequirements->firstWhere('gender_name', 'Pria / Male')?->start_age ?? '....' }}
                    </span>
                    s/d
                    <span style="width: 50px;">
                        {{ $er->genderRequirements->firstWhere('gender_name', 'Pria / Male')?->end_age ?? '....' }}
                    </span>
                    tahun / <span style="font-style: italic;">years</span>
                </span>
                <br>
                <span style="width: 130px; display: inline-block;">
                    @if ($er->genderRequirements->contains('gender_name', 'Wanita / Female'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Wanita / <span style="font-style: italic;">Female</span>
                </span>
                <span style="display: inline-block;">
                    Usia / <span style="font-style: italic;">Age</span> :
                    <span style="width: 50px;">
                        {{ $er->genderRequirements->firstWhere('gender_name', 'Wanita / Female')?->start_age ?? '....' }}
                    </span>
                    s/d
                    <span style="width: 50px;">
                        {{ $er->genderRequirements->firstWhere('gender_name', 'Wanita / Female')?->end_age ?? '....' }}
                    </span>
                    tahun / <span style="font-style: italic;">years</span>
                </span>
            </td>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold;">PENGALAMAN BEKERJA / <span style="font-style: italic;">WORK
                    EXPERIENCE</span></td>
        </tr>
        <tr>
            {{-- Experience --}}
            <td rowspan="2" style="vertical-align: top;">
                <span style="width: 179px; display: inline-block;">
                    @if ($er->work_experience == 'Dibutuhkan / Required')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Dibutuhkan / <span style="font-style: italic;">Required</span>
                </span>
                <span style="display: inline-block;"> :
                    <span style="width: 50px;">
                        {{ $er->duration_work_experience ?? '.....' }}
                    </span>
                    tahun / <span style="font-style: italic;">years</span>
                </span>
                <br>
                <span style="display: inline-block;">
                    @if ($er->work_experience == 'Tidak dibutuhkan / Not Required (Freshgraduate)')
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Tidak dibutuhkan / <span style="font-style: italic;">Not Required (Freshgraduate)</span>
                </span>
                <span style="display: inline-block;"></span>
            </td>
        </tr>
        <tr></tr>
        <tr></tr>
    </table>
    <h6 style="margin-top: -5px;">KUALIFIKASI DAN PERSYARATAN KETERAMPILAN KHUSUS / <span
            style="font-style: italic;">QUALIFICATION AND SPECIAL SKILLS REQUIREMENTS</span></h6>
    <table class="info-table">
        <tr>
            <td style="height: 85px; overflow: hidden; display: block;">
                @php
                    $isJapanese = preg_match('/[^\x00-\x7F]/u', $er->qualification);
                    $commentClass = $isJapanese ? 'japanese-comment' : '';
                @endphp
                <span class="{{ $commentClass }}">{!! nl2br($er->qualification) !!}</span>
            </td>
        </tr>
    </table>
    <h6>TANGGAL MULAI BEKERJA / <span style="font-style: italic;">EMPLOYMENT DATE</span></h6>
    <table class="info-table">
        <tr>
            <td style="font-weight: bold; width: 270px;">TANGGAL MULAI BEKERJA / <span
                    style="font-style: italic;">EMPLOYMENT DATE</span></td>
            <td style="font-weight: bold; width: 5px;">:</td>
            <td style="font-weight: bold;">
                {{ $er->employment_date?->format('d F Y') ?? '........................................' }}</td>
        </tr>
    </table>
    <h6>INFORMASI TINDAK LANJUT / <span style="font-style: italic;">FOLLOW-UP INFORMATION</span></h6>
    <table class="info-table" style="border-bottom: 1px solid black;">
        <tr>
            <td style="font-weight: bold;">Sumber Rekrutmen / <span style="font-style: italic;">Recruitment
                    Source</span></td>
        </tr>
        <tr>
            {{-- Recruitment Source --}}
            <td rowspan="4" style="vertical-align: top;">
                <span style="display: inline-block;">
                    @if ($er->requiresRecruitment('Manual Source / Job Submission'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Manual Source / <span style="font-style: italic;">Job Submission</span>
                </span>
                <br>
                <span style="display: inline-block;">
                    @if ($er->requiresRecruitment('Internet / Job Posting'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Internet / <span style="font-style: italic;">Job Posting</span>
                </span>
                <br>
                <span style="display: inline-block;">
                    @if ($er->requiresRecruitment('Head Hunter / Talent Search'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Head Hunter / <span style="font-style: italic;">Talent Search</span>
                </span>
                <br>
                <span style="display: inline-block;">
                    @if ($er->requiresRecruitment('Others'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    <span style="font-style: italic;">Others</span> :
                    {{ $er->recruitmentSources->firstWhere('name', 'Others')?->pivot->other_detail ?? '' }}
                </span>
            </td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        {{-- Decision --}}
        {{-- <tr>
            <td style="font-weight: bold;">Keputusan / <span style="font-style: italic;">Decision</span></td>
        </tr>
        <tr>
            <td rowspan="3" style="vertical-align: top;">
                <span style="width: 120pt; display: inline-block;">
                    @if (strtoupper($er->decision) === strtoupper('Approved'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Disetujui / <span style="font-style: italic;">Approved</span>
                </span>
                <span style="display: inline-block;"> :
                    @if (strtoupper($er->decision) === strtoupper('Approved'))
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $er->decision_comment);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span
                            class="{{ $commentClass }}">{{ $er->decision_comment ?? '..........................................................................................' }}</span>
                    @else
                        {{ '..........................................................................................' }}
                    @endif
                </span>
                <br>
                <span style="width: 120pt; display: inline-block;">
                    @if (strtoupper($er->decision) === strtoupper('Pending'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Ditunda / <span style="font-style: italic;">Pending</span>
                </span>
                <span style="display: inline-block;"> :
                    @if (strtoupper($er->decision) === strtoupper('Pending'))
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $er->decision_comment);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span
                            class="{{ $commentClass }}">{{ $er->decision_comment ?? '..........................................................................................' }}</span>
                    @else
                        {{ '..........................................................................................' }}
                    @endif
                </span>
                <br>
                <span style="width: 120pt; display: inline-block;">
                    @if (strtoupper($er->decision) === strtoupper('Disapproved'))
                        <img src="{{ $base64Svgs['square-xmark-regular-full'] }}" alt="Square Xmark"
                            class="svg-box">
                    @else
                        <img src="{{ $base64Svgs['square-regular-full'] }}" alt="Square" class="svg-box">
                    @endif
                    Ditolak / <span style="font-style: italic;">Disapproved</span>
                </span>
                <span style="display: inline-block;"> :
                    @if (strtoupper($er->decision) === strtoupper('Disapproved'))
                        @php
                            $isJapanese = preg_match('/[^\x00-\x7F]/u', $er->decision_comment);
                            $commentClass = $isJapanese ? 'japanese-comment' : '';
                        @endphp
                        <span
                            class="{{ $commentClass }}">{{ $er->decision_comment ?? '..........................................................................................' }}</span>
                    @else
                        {{ '..........................................................................................' }}
                    @endif
                </span>
            </td>
        </tr>
        <tr></tr>
        <tr></tr> --}}
    </table>

    <div class="container">
        <table class="sign-table">
            <tr>
                <td>Pemohon,</td>
                <td>Diperiksa oleh,</td>
                <td>Disetujui oleh,</td>
            </tr>
            <tr>
                <td style="font-style: italic; padding: 0px 30px;">Applicant</td>
                <td style="font-style: italic; padding: 0px 30px;">Checked by</td>
                <td style="font-style: italic; padding: 0px 30px;">Approved by</td>
            </tr>
            <tr>
                <td style="padding-top: 5px; padding-bottom: 5px;">
                    {!! generateApprovalQrCode($er, 'applicant', 'applicant', $er->submit_date) !!}
                </td>
                <td style="padding-top: 5px; padding-bottom: 5px;">
                    @php
                        $data = getApprovalData($er, 'Checker');
                    @endphp
                    {!! generateApprovalQrCode($er, $data['role'], $data['employee'], $data['date']) !!}
                </td>
                <td style="padding-top: 5px; padding-bottom: 5px;">
                    @php
                        $data = getApprovalData($er, 'Approval');
                    @endphp
                    {!! generateApprovalQrCode($er, $data['role'], $data['employee'], $data['date']) !!}
                </td>
            </tr>
            <tr>
                <td style="font-size: 9px; text-align: left;">
                    Name : {{ formatNameForDisplay($er->applicant?->fullname ?? '', 30) }}
                    <br>
                    Date : {{ $er->submit_date ? \Carbon\Carbon::parse($er->submit_date)->format('Y-m-d H:i') : '' }}
                </td>
                <td style="font-size: 9px; text-align: left;">
                    Name : {{ formatNameForDisplay($checker['employee']?->fullname ?? '', 30) }}
                    <br>
                    Date : {{ $checker['date'] ?? '' }}
                </td>
                <td style="font-size: 9px; text-align: left;">
                    Name : {{ formatNameForDisplay($approval['employee']?->fullname ?? '', 30) }}
                    <br>
                    Date : {{ $approval['date'] ?? '' }}
                </td>
            </tr>
        </table>
        <table class="sign-table"
            style="border: none; background-color: #FFFF00; color: #FF0000; margin-left: -20px;">
            <tr>
                <td style="border: none; font-size: 9px; text-align: left;">Catatan/<span
                        style="font-style: italic;">Note</span> : Harus ditandatangani/ <span
                        style="font-style: italic;">Signed is a must.</span></td>
            </tr>
        </table>

        {{-- Page 2 --}}
        <div style="page-break-after: always;">
        </div>
        <div style="position: relative; height: 100%;">
            <div style="position: absolute; bottom: 0; width: 100%;">
                <table class="sign-table" style="margin-bottom: 10px;">
                    <tr>
                        <td>Mengetahui,</td>
                        <td>Mengetahui,</td>
                    </tr>
                    <tr>
                        <td style="font-style: italic; padding: 0px 20px;">Acknowledge by,</td>
                        <td style="font-style: italic; padding: 0px 20px;">Acknowledge by,</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 5px; padding-bottom: 5px;">
                            @php
                                $data = getApprovalData($er, 'Director');
                            @endphp
                            {!! generateApprovalQrCode($er, $data['role'], $data['employee'], $data['date']) !!}
                        </td>
                        <td style="padding-top: 5px; padding-bottom: 5px;">
                            @php
                                $data = getApprovalData($er, 'President Director');
                            @endphp
                            {!! generateApprovalQrCode($er, $data['role'], $data['employee'], $data['date']) !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 9px; text-align: left;">
                            Director
                            {{-- Name : {{ $directorApproval['employee']?->fullname ?? '' }} --}}
                            <br>
                            Date : {{ $directorApproval['date'] ?? '' }}
                        </td>
                        <td style="font-size: 9px; text-align: left;">
                            President Director
                            {{-- Name : {{ $presidentApproval['employee']?->fullname ?? '' }} --}}
                            <br>
                            Date : {{ $presidentApproval['date'] ?? '' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
