<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Kebutuhan Training</title>
    <style>
        @page {
            /* size: A4 landscape;
            size: 287mm 210mm; */
            /* margin-top: 0px; */
            /* margin-bottom: 0px; */
            size: 21cm 30cm; 
        }
        .garis_tepi {
            border: 1px solid black;
            padding: 5px;
            margin: -20px -20px -20px -20px;
        }

        table.header{
            border-collapse: collapse;
            width: 100%;
        }

        h5 {
            text-align: center;
            font-family:"Poppins", sans-serif;
        }

        table.isi {
            border-collapse: collapse;
            width:100%;
            border: 1px solid black;
            padding: 5px;
            font-size: 9px;
        }

        .isi tbody tr:nth-child(5n) 
        {
            /* color: red; */
            page-break-after: always;
        }

        th.isi{
            font-size: 9px;
            border: 1px solid black;
            padding: 5px; 
            font-family: "Times New Roman", Times, serif;
        }

        td.isi{
            font-size: 9px;
            border: 1px solid black;
            padding: 5px; 
            font-family: "Times New Roman", Times, serif;
        }
        p.konten{
            font-size: 9px;
            padding: 5px; 
            font-family: "Times New Roman", Times, serif;
        }   
        table.konten {
            border-collapse: collapse;
            width:100%;
            padding: 5px; 
            border: 1px solid black;
            font-size: 9px;
        }  
        td.konten{
            font-size: 9px;
            border: 1px solid black;
            padding: 5px; 
            font-family: "Times New Roman", Times, serif;
        }   
        p.ket{
            font-size:7px;
            font-family: "Times New Roman", Times, serif;
            margin-left: 10px;
        }
        table.ttd {
            border-collapse: collapse;
            width:100%;
            border: 1px solid black;
            padding: 5px;
            font-size: 8px;
        }
        th.ttd{
            font-size: 8px;
            border: 1px solid black;
            padding: 5px; 
            font-family: "Times New Roman", Times, serif;
        }

        td.ttd{
            font-size: 8px;
            border: 1px solid black;
            padding: 5px; 
            font-family: "Times New Roman", Times, serif;
        }
        .footer { position: fixed; left: 0px; bottom: 0px; right: 10px; height: 0px; }
        .footer.first { bottom: 0px; }
        /* .page-break {
            page-break-after: always;
        }
        .avoid {
            page-break-inside: avoid !important;
            margin: 150px 0 0 0;
        }  */
        #wrapper {
            margin-top: -10px;
            width: 100%;
            height: auto;
            /* border: 1px dotted gray; */
            position: relative;
        }
        #right {
            position: absolute;
            left: 0;
            right: 160;
            top: 0;
            bottom: 0;
        }
        #left {
            position: absolute;
            left: 390;
            right: 0;
            top: 0;
            bottom: 0;
        }
    </style>
</head>
<body class="garis_tepi">
    <footer class="footer first">
        <table class="header">
            <tr>
                <td align="left"><p class="ket">01/12/2022</p></td>
                <!-- <td align="center" class="halaman" style="width: 650px;">Hal <span class="pagenum"></span></td> -->
                <td align="right"><p class="ket">Form HR-PS-02/06 REV.00</p></td>
            </tr>
        </table>
    </footer>
    <!-- judul -->
    <h5>{{$title}}</h5>
    
    <!-- pemohon -->
    <table style="width:100%; font-size: 9px; padding: 5px;">
        <tr>
            <td style="width:15%"><b>Tanggal Laporan</b></td>
            <td style="width:1%"><b>:</b></td>
            <td>{{date('d F Y', strtotime($record->tgl_laporan))}}</td>
            <td></td>
        </tr>
        <tr>
            <td style="width:15%"><b>Nama</b></td>
            <td style="width:1%"><b>:</b></td>
            <td>{{$record->employee->fullname}}</td>
            <td></td>
        </tr>
        <tr>
            <td style="width:15%"><b>Bagian</b></td>
            <td style="width:1%"><b>:</b></td>
            <td>{{$record->employee->position->nama ?? '-'}}</td>
            <td><b>Departemen :</b> {{$record->employee->department->name ?? '-'}}</td>
        </tr>
    </table>
    <table class="isi">
        <thead>
            <tr>
                <th class="isi" align="left">Nama Program Pelatihan:</th>
                <th class="isi" align="left">Tanggal Pelaksanaan:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="isi" align="left">{{$record->judul}}</td>
                <td class="isi" align="left">{{date('d F Y', strtotime($record->start_date))}} s.d {{date('d F Y', strtotime($record->end_date))}}</td>
            </tr>
        </tbody>
    </table>
    <p class="konten"><b>1. Isi Pelatihan</b></p>
    <table class="konten" style="margin-top:-15px;">
        <tbody>
            <tr>
                <td class="konten" align="left" height="80">
                    <div style="position: absolute;">
                        {{$record->isi_pelatihan}}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="konten"><b>2. Apa yang dipelajari</b></p>
    <table class="konten" style="margin-top:-15px;">
        <tbody>
            <tr>
                <td class="konten" align="left" height="80">
                    <div style="position: absolute;">
                        {{$record->dipelajari}}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="konten"><b>3. Bagaimana Anda mengimplementasikan materi training dalam pekerjaan</b></p>
    <table class="konten" style="margin-top:-15px;">
        <tbody>
            <tr>
                <td class="konten" align="left" height="80">
                    <div style="position: absolute;">
                        {{$record->implementasi}}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="konten"><b>4. Kolom supervisor (atasan langsung) <br> (Setelah menerima laporan hasil pelatihan, harapan atasan terhadap bawahannya untuk menindaklanjuti di masa mendatang)</b></p>
    <table class="konten" style="margin-top:-15px;">
        <tbody>
            <tr>
                <td class="konten" align="left" height="80">
                    <div style="position: absolute;">
                        {{$record->hasil}}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="konten" style="margin-top:-2px;"><b>Alur Pelaporan :</b></p>
    <p class="konten" style="margin-top:-15px;">
        Karyawan yang mengikuti pelatihan > Atasan Langsung > Manager > General Manager > Production Director / Jr. Director > President Director > HRD PIC Pelatihan.
    </p>
    <p class="konten" style="margin-top:-15px;">
        # Laporan diserahkan kepada atasan langsung dalam waktu 3 hari setelah pelatihan, dan laporan harus diserahkan kepada President Director dalam kurun waktu 1 minggu setelah pelatihan.
    </p>
    <div id="wrapper">
        <div id="right">
            <table class="ttd">
                <thead>
                    <tr>
                        <th class="ttd">President Director</th>
                        <th class="ttd">Production Director /<br>Jr. Director</th>
                        <th class="ttd">General Manager</th>
                        <th class="ttd">Manager</th>
                        <th class="ttd">Atasan Langsung</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if(!empty($link_qr_code_ttd))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                        @if(!empty($link_qr_code_ttd2))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd2)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                        @if(!empty($link_qr_code_ttd3))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd3)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                        @if(!empty($link_qr_code_ttd4))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd4)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                        @if(!empty($link_qr_code_ttd5))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd5)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd1))}}</td>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd2))}}</td>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd3))}}</td>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd4))}}</td>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd5))}}</td>
                    </tr>
                    <tr>
                        <td class="ttd">Tanggal : {{$tgl_ttd1}}</td>
                        <td class="ttd">Tanggal : {{$tgl_ttd2}}</td>
                        <td class="ttd">Tanggal : {{$tgl_ttd3}}</td>
                        <td class="ttd">Tanggal : {{$tgl_ttd4}}</td>
                        <td class="ttd">Tanggal : {{$tgl_ttd5}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="left">
            <table class="ttd">
                <thead>
                    <tr>
                        <th class="ttd">HRD & GA<br>General Manager</th>
                        <th class="ttd">HRD<br>PIC Pelatihan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if(!empty($link_qr_code_ttd6))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd6)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                        @if(!empty($link_qr_code_ttd7))
                        <td class="ttd" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_code_ttd7)) !!} "></td>
                        @else
                        <td class="ttd" align="center"></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd6))}}</td>
                        <td class="ttd">Nama : {{Str::title(Str::lower($nama_ttd7))}}</td>
                    </tr>
                    <tr>
                        <td class="ttd">Tanggal : {{$tgl_ttd6}}</td>
                        <td class="ttd">Tanggal : {{$tgl_ttd7}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>