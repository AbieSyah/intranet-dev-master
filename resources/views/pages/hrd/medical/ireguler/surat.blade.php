<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$title}}</title>
    <style>
        header img {
            position: absolute;
            top: -1.2cm;
            left: -1.2cm;
            height: 5.62cm;
        }
        table.lab {
            margin-top: 180px;
            margin-bottom: 0px;
            margin-right: 0;
            margin-left: 0;
            border-collapse: collapse;
            width:100%;
        }
        td.kosong {
            width:60%;
        }
        p{
            line-height: 28px;
        }
        table.isi {
            border-collapse: collapse;
            width:70%;
            border: 1px solid black;
        }
        th.isi{
           border: 1px solid black; 
        }
        td.isi{
            border: 1px solid black; 
            padding: 5px;
        }
        table.jadwal {
            border-collapse: collapse;
            width:100%;
        }
        td.spasi {
            width:7%;
        }
        #wrapper {
            width: 100%;
            height: 190px;
            /* border: 1px dotted gray; */
            position: relative;
        }
        #left {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            /* border: 1px dotted gray; */
        }
        #right {
            position: absolute;
            left: 0;
            right: 210px;
            top: 0;
            bottom: 0;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ $kop_surat }}" alt="">
    </header>  

    <table class="lab">
        <tr>
            <td class="kosong"></td>
            <td>Kepada Yth :</td>
        </tr>
        <tr>
            <td class="kosong"></td>
            <td>Bpk / Ibu Pimpinan</td>
        </tr>
        <tr>
            <td class="kosong"></td>
            <td>Laboratorium "PARAHITA"</td>
        </tr>
        <tr>
            <td class="kosong"></td>
            <td>Jl. Buncit Raya No.150, RT.5/RW.2, 
                Duren Tiga, Kec. Pancoran, Kota Jakarta Selatan
            </td>
        </tr>
    </table>
    <br>
    <table style="width:100%">
        <tr>
            <td style="width:12%">No.</td>
            <td style="width:2%">:</td>
            <td>{{$nomor_surat}}</td>
        </tr>
        <tr>
            <td style="width:12%">Perihal</td>
            <td style="width:2%">:</td>
            <td>Surat Pengantar MCU</td>
        </tr>
    </table>
    <p> 
        Sesuai dengan prosedur penerimaan karyawan baru di PT Hisamitsu Pharma Indonesia, mohon 
        dilakukan pemeriksaan medical check-up kepada calon karyawan kami yang bernama:
    </p>
    <table class="isi">
        <tr>
            <th class="isi">NO</th>
            <th class="isi">NAMA LENGKAP</th>
            <th class="isi">AREA</th>
        </tr>
        <tr>
            <td class="isi" style="text-align: center;">1</td>
            <td class="isi">{{$nama}}</td>
            <td class="isi" style="text-align: center;">{{$area}}</td>
        </tr>
    </table>
    <br>
    <table class="jadwal">
        <tr>
            <td class="spasi"></td>
            <td style="width:15%">Hari</td>
            <td style="width:2%">:</td>
            <td><b>{{$nama_hari}}</b></td>            
        </tr>
        <tr>
            <td class="spasi"></td>
            <td style="width:15%">Tanggal</td>
            <td style="width:2%">:</td>
            <td><b>{{$tanggal}}</b></td>           
        </tr>
        <tr>
            <td class="spasi"></td>
            <td style="width:15%">Pukul</td>
            <td style="width:2%">:</td>
            <td><b>08:00 WIB</b></td>           
        </tr>
    </table>
    <br>
    <em>Note :</em>
    <table style="width:100%">
        <tr>
            <td style="width:4%"></td>
            <td style="width:3%; vertical-align: top;"><em>1.</em></td>
            <td><em>Nama harap disesuaikan dengan KTP Asli.</em></td>
        </tr>
        <tr>
            <td style="width:4%"></td>
            <td style="width:3%; vertical-align: top;"><em>2.</em></td>
            <td><em>Jenis pemeriksaan yang digunakan adalah jenis pemeriksaan <b>Paket {{$paket}}</b> (detail dibawah).</em></td>
        </tr>
    </table>
    <p>
        Demikian surat pengantar dari kami. Terima kasih atas perhatian dan kerjasamanya.
    </p>
    <br>
    <br>
    <div id="wrapper">
        <div id="right" style="margin-top: 30px;">
            <table style="width: 40%; border: 1px solid black; font-size:12px;">
                <tr>
                    <td>Jenis Pemeriksaan:</td>
                </tr>
            </table>
            <table style="width: 40%; border: 1px solid black; margin-top: 1px; font-size:12px;">
                <tr>
                    <td>1. Pemeriksaan Fisik (K3)</td>
                </tr>
                <tr>
                    <td>2. Faal Hati; SGOT & SGPT</td>
                </tr>
                <tr>
                    <td>3. Hepatitis B; HBs Ag</td>
                </tr>
                <tr>
                    <td>4. Photo Thorax DIGITAL (FCH)</td>
                </tr>
            </table>
        </div>
        <div id="left">
            <table style="width: 100%;">
                <tr>
                    <td>Sidoarjo, {{$tanggal_surat}}</td>
                </tr>                
            </table>
            <table style="width: 100%; margin-top: 120px;">
                <tr>
                    <td><b><u>WAWAN SUPRIYANTO</u></b></td>
                </tr>
                <tr>
                    <td>HRD & GA GENERAL MANAGER</td>
                </tr>              
            </table>
        </div>
    </div>
    
</body>
</html>