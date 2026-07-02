<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pelaksanaan Pelatihan</title>
    <style>
        @page {
            size: A4 landscape;
            size: 287mm 230mm;
            /* margin-top: 0px; */
            /* margin-bottom: 0px; */
        }
        .garis_tepi {
            border: 1px solid black;
            padding: 5px;
            margin: -20px -20px -20px -20px;
        }

        table.header, th.header, td.header{
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
            font-size: 10px;
            padding: 5px;
        }

        h5 {
            text-align: left;
            font-family:"Poppins", sans-serif;
            padding: 5px;
            margin: 0 0 0 0;
        }

        #wrapper-header {
            width: 100%;
            height: auto;
            /* border: 1px dotted gray; */
            position: relative;
        }
        #header-kiri {
            position: absolute;
            left:0;
            right: 0;
            top: 0;
            bottom: 0;
            width: 60%;
            /* border: 1px dotted gray; */
        }
        #header-kanan {
            position: absolute;
            left: 613px;
            right: 0;
            top: 0;
            bottom: 0;
            /* border: 1px dotted gray; */
        }

        table.isi {
            border-collapse: collapse;
            width:100%;
            border: 1px solid black;
            padding: 5px;
            font-size: 10px;
            position: relative;
            top: 135px;
        }

        .isi tbody tr:nth-child(11n) 
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
            text-align: center;
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
            width: 153px;
            font-size: 10px;
            border: 1px solid black;
            padding: 5px; 
            /* text-transform: capitalize; */
        }
        #wrapper {
            margin-top: -30px;
            width: 100%;
            height: auto;
            /* border: 1px dotted gray; */
            position: relative;
        }
        #left {
            position: absolute;
            left:0;
            right: 0;
            top: 150px;
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
    <!-- <footer class="footer first">
        <table class="header">
            <tr>
                <td align="left"><p class="ket">01/01/2023</p></td>
                <td align="right"><p class="ket">Form HR-PS-02/02 REV.00</p></td>
            </tr>
        </table>
    </footer> -->

    <!-- No Form -->
    <table style="float:right; font-size: 10px; padding: 5px;">
        <tr>
            <td style="width:5%">No. Form</td>
            <td style="width:1%">:</td>
            <td>{{$data['fpkt']->fkt->kode ?? $data['fpkt']->kode_fpkt}}</td>
        </tr>
    </table>
    <!-- judul -->
    <h5>{{$data['title']}}</h5>
    
    <!-- header -->
    <div id="wrapper-header">
        <div id="header-kiri">
            <table style="width:100%; font-size: 10px; padding: 5px;">
                <tr>
                    <td style="width:30%">Usulan Topik Training</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->judul_fpkt ?? '-'}}</td>
                </tr>
                <tr>
                    <td style="width:30%">Rekomendasi Jenis Training</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->jenis_fpkt ?? '-'}}</td>
                </tr>
                <tr>
                    <td style="width:30%">Rekomendasi Vendor Training</td>
                    <td style="width:1%">:</td>
                    @if(!empty($data['fpkt']->id_vendor == null))
                    <td>{{$data['fpkt']->nama_vendor}}</td>
                    @else
                    <td>{{$data['fpkt']->vendor->nama}}</td>
                    @endif
                </tr>
                <tr>
                    <td style="width:30%">Nama Peserta Training</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->peserta->fullname ?? '-'}}</td>
                </tr>
                <tr>
                    <td style="width:30%">Nomor Induk Karyawan (NIK)</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->peserta->nik ?? '-'}}</td>
                </tr>
                <tr>
                    <td style="width:30%">Departemen / Bagian</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->peserta->department->name ?? '-'}}</td>
                </tr>
                <tr>
                    <td style="width:30%">Jabatan</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->peserta->position->nama ?? '-'}}</td>
                </tr>
                <tr>
                    <td style="width:30%">Nama Atasan Langsung</td>
                    <td style="width:1%">:</td>
                    <td>{{$data['fpkt']->atasan->fullname ?? '-'}}</td>
                </tr>
            </table>
        </div>
        <div id="header-kanan">
            <table class="header">
                <thead>
                    <tr>
                        <td class="header"><b>Latar Belakang Usulan Training :</b><br><i>(Penjelasan mengenai keterkaitan antara usulan topik training dengan pekerjaan saat ini).</i></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if($data['fpkt'] != '')
                        <td class="header">{{$data['fpkt']->latar_belakang}}</td>
                        @else
                        <td class="header"></td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <table class="isi">
        <thead>
            <tr>
                <th class="isi" style="width: 200px;">Tujuan training</th>
                <th class="isi" style="width: 200px;">Kompetensi yang Diharapkan</th>
                <th class="isi">Skill / Knowledge</th>
                <th class="isi">Level Skill Knowledge (diisi oleh peserta)</th>
                <th class="isi">Level Skill Knowledge (diisi oleh atasan langsung)</th>
                <th class="isi">Rata - rata Level skill / Knowledge</th>
                <th class="isi">Kebutuhan training</th>
            </tr>
        </thead>
        <tbody>
            {{-- <tr>
                <td style="border: 1px solid black;" align="center"><i>Tuliskan Tujuan yang ingin dicapai setelah mengikuti training!</i></td>
                <td style="border: 1px solid black;" align="center"><i>Tuliskan Kompetensi apa saja yang dapat menunjang dalam mencapai tujuan training ini!</i></td>
                <td style="border: 1px solid black;" align="center"><i>Sebutkan minimal 3 komponen Skill / Knowledge yang saat ini dimiliki oleh karyawan dan diperlukan untuk mempresentasikan kompetensi yang diharapkan!</i></td>
                <td style="border: 1px solid black;" align="center"><i>Tingkat Skill / Knowledge menurut penilaian diri sendiri (skala 1-5).</i></td>
                <td style="border: 1px solid black;" align="center"><i>Tingkat Skill / Knowledge menurut penilaian atasan langsung (skala 1-5).</i></td>
                <td style="border: 1px solid black;" align="center"><i>Rata - rata tingkat Skill / Knowledge menurut penilaian diri sendiri dan atasan langsung.</i></td>
                <td style="border: 1px solid black;" align="center"><i>Tingkat Kebutuhan Training</i></td>
            </tr> --}}
            @if(isset($data['arr_fpkt']))
                @foreach ($data['arr_fpkt'] as $key => $value)  
                <tr>
                    @if ($key === 0 || $data['arr_fpkt'][$key]['tujuan'] !== $data['arr_fpkt'][$key - 1]['tujuan'])
                        <td rowspan="{{ $data['jumlah'] }}" class="isi">{{ $value['tujuan'] }}</td>
                        <td rowspan="{{ $data['jumlah'] }}" class="isi">{{ $value['kompetensi'] }}</td>
                    @endif
                    <td class="isi">{{$value['skill']}}</td>
                    <td class="isi">{{$value['level_peserta']}}</td>
                    <td class="isi">{{$value['level_atasan']}}</td>
                    <td class="isi">{{$value['level_rata']}}</td>
                    <td class="isi">{{$value['level_kebutuhan']}}</td>
                </tr>
                @endforeach
            @else
            <tr>
                <td class="isi"></td>
                <td class="isi"></td>
                <td class="isi"></td>
                <td class="isi"></td>
                <td class="isi"></td>
                <td class="isi"></td>
                <td class="isi"></td>
            </tr>
            @endif
            <tr>
                <td class="isi" style="text-align: right;" colspan="3">Skor Kebutuhan Training</td>
                <td class="isi">{{$data['skor']}}</td>
                @if($data['skor'] == '1')
                <td colspan="3" class="isi">Kebutuhan Training Sangat Tinggi</td>
                @elseif($data['skor'] == '2')
                <td colspan="3" class="isi">Kebutuhan Training Tinggi</td>
                @elseif($data['skor'] == '3')
                <td colspan="3" class="isi">Kebutuhan Training Sedang</td>
                @elseif($data['skor'] == '4')
                <td colspan="3" class="isi">Kebutuhan Training Rendah</td>
                @else
                    @if($data['skor'] != '')
                    <td colspan="3" class="isi">Kebutuhan Training Sangat Rendah</td>
                    @else
                    <td colspan="3" class="isi"></td>
                    @endif
                @endif
            </tr>
            <tr>
                <td colspan="7" class="isi" style="text-align: left;">Catatan dari Atasan<br><i>(diisi jika skor kebutuhan training 5 atau 4) :</i> {{$data['fpkt']->catatan}}</td>
                {{-- @if($data['fpkt'] != '')
                <td class="isi" colspan="6" style="text-align: left;">{{$data['fpkt']->catatan}}</td>
                @else
                <td class="isi" colspan="6" style="text-align: left;"></td>
                @endif --}}
            </tr>
        </tbody>
    </table>  
    <div id="wrapper">
        <div id="right">
        </div>
        <div id="left">
            <table class="ttd">
                <tr>
                    <td class="ttd" style="text-align: center;">Peserta Training,</td>
                    <td class="ttd" style="text-align: center;">Atasan Langsung,</td>
                    <td class="ttd" style="text-align: center;">Atasan Departemen,</td>
                    <td class="ttd" style="text-align: center;">Direktur Produksi,</td>
                    <td class="ttd" style="text-align: center;">Presiden Direktur,</td>
                    <td class="ttd" style="text-align: center;">Diverifikasi Oleh (HRD),</td>
                </tr>
                <tr>
                    @if(!empty($data['link_qr_peserta']))
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($data['link_qr_peserta'])) !!} "></td>
                    @else
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"></td>
                    @endif
                    @if(!empty($data['link_qr_atasan']))
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($data['link_qr_atasan'])) !!} "></td>
                    @else
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"></td>
                    @endif
                    @if(!empty($data['link_qr_dept']))
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($data['link_qr_dept'])) !!} "></td>
                    @else
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"></td>
                    @endif
                    @if(!empty($data['link_qr_bod1']))
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($data['link_qr_bod1'])) !!} "></td>
                    @else
                        @if($data['fpkt']->status == '5' || $data['fpkt']->status == '12')
                        <td class="ttd" style="border-bottom: 0px;" height="60" align="center">NA</td>
                        @else
                        <td class="ttd" style="border-bottom: 0px;" height="60" align="center"></td>
                        @endif
                    @endif
                    @if(!empty($data['link_qr_bod2']))
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($data['link_qr_bod2'])) !!} "></td>
                    @else
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"></td>
                    @endif
                    @if(!empty($data['link_qr_hrd']))
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"><img src="data:image/png;base64, {!! base64_encode(QrCode::size(80)->generate($data['link_qr_hrd'])) !!} "></td>
                    @else
                    <td class="ttd" style="border-bottom: 0px;" height="60" align="center"></td>
                    @endif
                </tr>
                <tr>
                    @if($data['fpkt'] != '')
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : {{$data['fpkt']->peserta->fullname ?? '-'}}</td>
                    @else
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : {{$data['fpkt']->atasan->fullname ?? '-'}}</td>
                    @else
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : {{$data['fpkt']->atasan_dept->fullname ?? '-'}}</td>
                    @else
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : -</td>
                    @endif
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : MIZUKAMI, Tatsuhiro</td>
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : SATO, Ryo</td>
                    @if($data['fpkt'] != '')
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : {{$data['fpkt']->hrd->fullname ?? '-'}}</td>
                    @else
                    <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Nama : -</td>
                    @endif
                </tr>
                <tr>
                    @if($data['fpkt'] != '')
                        @if(!empty($data['fpkt']->date_peserta))
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : {{date('d F Y', strtotime($data['fpkt']->date_peserta)) ?? '-'}}</td>
                        @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                        @endif
                    @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                        @if(!empty($data['fpkt']->date_atasan))
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : {{date('d F Y', strtotime($data['fpkt']->date_atasan))}}</td>
                        @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                        @endif
                    @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                        @if(!empty($data['fpkt']->date_dept_head))
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : {{date('d F Y', strtotime($data['fpkt']->date_dept_head))}}</td>
                        @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                        @endif
                    @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                        @if(!empty($data['fpkt']->date_bod1))
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : {{date('d F Y', strtotime($data['fpkt']->date_bod1))}}</td>
                        @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                        @endif
                    @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                        @if(!empty($data['fpkt']->date_bod2))
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : {{date('d F Y', strtotime($data['fpkt']->date_bod2))}}</td>
                        @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                        @endif
                    @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                    @endif
                    @if($data['fpkt'] != '')
                        @if(!empty($data['fpkt']->date_hrd))
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : {{date('d F Y', strtotime($data['fpkt']->date_hrd))}}</td>
                        @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                        @endif
                    @else
                        <td style="border-right: 1px solid black; border-top: 0px; font-size: 9px;">Tanggal : -</td>
                    @endif
                </tr>
            </table>
        </div>
    </div> 
</body>
</html>