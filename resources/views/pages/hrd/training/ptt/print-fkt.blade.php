<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Kebutuhan Pelatihan</title>
    <style>
        @page {
            size: A4 landscape;
            size: 287mm 210mm;
            /* margin-top: 0px; */
            /* margin-bottom: 0px; */
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

        h3 {
            text-align: center;
            font-family:"Poppins", sans-serif;
        }

        table.isi {
            border-collapse: collapse;
            width:100%;
            border: 1px solid black;
            padding: 5px;
            font-size: 10px;
        }

        .isi tbody tr:nth-child(5n) 
        {
            /* color: red; */
            page-break-after: always;
        }

        th.isi{
            font-size: 10px;
            border: 1px solid black;
            padding: 5px; 
        }

        td.isi{
            font-size: 10px;
            border: 1px solid black;
            padding: 5px; 
        }

        table.ttd {
            border-collapse: collapse;
            width:100%;
            border: 1px solid black;
            padding: 20px;
            font-size: 10px;
        }

        /* th.ttd{
            font-size: 12px;
            border: 1px solid black;
            padding: 5px; 
        } */

        td.ttd{
            width: 160px;
            font-size: 10px;
            border: 1px solid black;
            padding: 5px; 
            /* text-transform: capitalize; */
        }
        #wrapper {
            margin-top: -10px;
            width: 100%;
            height: auto;
            /* border: 1px dotted gray; */
            position: relative;
        }
        #left {
            position: absolute;
            left:190px;
            right: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            /* border: 1px dotted gray; */
        }
        #right {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin-top: 5px;
        }
        p.ket{
            font-size:7px;
            font-family: "Times New Roman", Times, serif;
            margin-left: 10px;
        }
        .footer { position: fixed; left: 0px; bottom: 0px; right: 10px; height: 0px; }
        .footer.first { bottom: 20px; }
        /* .page-break {
            page-break-after: always;
        }
        .avoid {
            page-break-inside: avoid !important;
            margin: 150px 0 0 0;
        }  */
    </style>
</head>
<body class="garis_tepi">
    <footer class="footer first">
        <table class="header">
            <tr>
                <td align="left"><p class="ket">01/01/2023</p></td>
                <!-- <td align="center" class="halaman" style="width: 650px;">Hal <span class="pagenum"></span></td> -->
                <td align="right"><p class="ket">Form HR-PS-02/02 REV.00</p></td>
            </tr>
        </table>
    </footer>
    <!-- judul -->
    <h3 class="mb-3">{{$title}}</h3>
    
    <!-- pemohon -->
    <table style="width:100%; font-size: 10px; padding: 5px;">
        <tr>
            <td style="width:15%">Nama pemohon</td>
            <td style="width:1%">:</td>
            <td>{{$fkt->pemohon->fullname ?? '-'}}</td>
        </tr>
        <tr>
            <td style="width:15%">Departemen</td>
            <td style="width:1%">:</td>
            <td>{{$fkt->pemohon->department->name ?? '-'}}</td>
        </tr>
        <tr>
            <td style="width:15%">Tahun usulan program</td>
            <td style="width:1%">:</td>
            <td>{{$fkt->tahun_usulan ?? '-'}}</td>
        </tr>
        <tr>
            <td style="width:15%">Tahun rencana pelaksanaan</td>
            <td style="width:1%">:</td>
            <td>{{$fkt->tahun_pelaksanaan ?? '-'}}</td>
        </tr>
        <tr>
            <td style="width:15%">Tujuan usulan program</td>
            <td style="width:1%">:</td>
            <td>
                Program Pelatihan Tahunan
            </td>
        </tr>
    </table>
    <table class="isi">
        <thead>
            <tr>
                <th class="isi" colspan="8">Data Pengajuan Program Pelatihan</th>
            </tr>
            <tr>
                <th class="isi" style="width:4%">No</th>
                <th class="isi">Nama</th>
                <th class="isi" style="width:5%">NIK</th>
                <th class="isi" style="width:8%">Jabatan</th>
                <th class="isi" style="width:10%">Nama Pelatihan</th>
                <th class="isi" >Sifat Pelatihan *)</th>
                <th class="isi" style="width:15%">Alasan</th>
                <th class="isi" style="width:5%">Bulan Pelaksanaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arr_fkt as $qry_fkt)
            <tr>
                <td class="isi" style="text-align: center;">{{$loop->iteration}}</td>
                <td class="isi">{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                <td class="isi" style="text-align: center;">{{$qry_fkt->peserta->nik ?? '-'}}</td>
                <td class="isi">{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                <td class="isi">{{$qry_fkt->judul ?? '-'}}</td>
                <td class="isi">{{$qry_fkt->sifat ?? '-'}}</td>
                <td class="isi">{{$qry_fkt->alasan ?? '-'}}</td>
                <td class="isi" style="text-align: center;">{{\Carbon\Carbon::create()->month($qry_fkt->bulan_pelaksanaan)->format('F') ?? '-'}}</td>
                @if($qry_fkt->tipe == 'pti')
                    @if(!empty($qry_fkt->provider->nama))
                    <td class="isi">{{$qry_fkt->provider->nama ?? '-'}}</td>
                    @else
                        @if(!empty($qry_fkt->nama_vendor))
                        <td class="isi">{{$qry_fkt->nama_vendor}}</td>
                        @else
                        <td class="isi">-</td>
                        @endif
                    @endif
                    <td class="isi" style="text-align: right;">Rp {{number_format($qry_fkt->biaya_fkt,0,",",".") ?? '-'}}</td>
                    <td class="isi">
                        <span>a) Menginap :</span>
                        <b>Ya</b>
                        <br>
                        <br>
                        <span>b) Transportasi yang digunakan :</span>
                        <b>Ya</b>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    <div id="wrapper">
        <div id="right">
            <p class="ket">*) Keterangan:</p>
            <p class="ket">- Skill Training</p>
            <p class="ket">- Re-Training</p>
            <p class="ket">- Cross Functional Training</p>
            <p class="ket">- Team Training</p>
        </div>
        <div id="left">
            <table class="ttd">
                <tr>
                    <td class="ttd" style="text-align: center;">Pemohon</td>
                    <td class="ttd" style="text-align: center;">Diperiksa oleh</td>
                    <td class="ttd" style="text-align: center;">Diverifikasi oleh</td>
                </tr>
                <tr>
                    @if(!empty($link_qr_pemohon))
                    <td class="ttd" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_pemohon)) !!} "></td>
                    @else
                    <td class="ttd" height="60" align="center"></td>
                    @endif
                    @if(!empty($link_qr_checker))
                    <td class="ttd" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_checker)) !!} "></td>
                    @else
                    <td class="ttd" height="60" align="center"></td>
                    @endif
                    @if(!empty($link_qr_verified))
                    <td class="ttd" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($link_qr_verified)) !!} "></td>
                    @else
                    <td class="ttd" height="60" align="center"></td>
                    @endif
                </tr>
                <tr>
                    <td class="ttd" style="text-align: center;">{{$pemohon_ttd}}</td>
                    <td class="ttd" style="text-align: center;">{{$checker_ttd}}</td>
                    <td class="ttd" style="text-align: center;">{{$verified_ttd}}</td>
                </tr>
                <tr>
                    <td class="ttd" style="text-align: center; text-transform: uppercase;">{{$pos_pemohon_ttd}}</td>
                    <td class="ttd" style="text-align: center; text-transform: uppercase;">{{$pos_checker_ttd}}</td>
                    <td class="ttd" style="text-align: center; text-transform: uppercase;">{{$pos_verified_ttd}}</td>
                </tr>
            </table>
        </div>
    </div>
    <script type="text/php">
    if ( isset($pdf) ) { 
        $pdf->page_script('
            if ($PAGE_COUNT > 1) {
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 6;
                $pageText = "Hal " . $PAGE_NUM . " / " . $PAGE_COUNT;
                $y = 580;
                $x = 400;
                $pdf->text($x, $y, $pageText, $font, $size);
            } 
        ');
    }
    </script>
</body>
</html>