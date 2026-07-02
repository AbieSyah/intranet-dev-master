<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Form Tamu - {{ $form->nama }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style>
        @page {
            margin: 0.2cm;
            font-size: 9px;
        }

        table,
        tr,
        th,
        td {
            border-collapse: collapse;
            border: 1px solid black;
            padding-bottom: 2px;
            padding-top: 2px;
            padding-left: 4px;
            padding-right: 4px;
        }

        .image-container {
            text-align: center;
        }

        .k3-images {
            height: 0.83cm;
            display: inline-block;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <th rowspan="2" style="width: 15%"><img src="{{ $logohisamitsu }}" alt="logo" style="width: 88%"></th>
            <th rowspan="2" style="width: 25%; font-size: 11px">
                Formulir Kunjungan Tamu
            </th>
            <th rowspan="2"><img src="{{ $logok3 }}" alt="logo" style="width: 1cm"></th>
            <th style="width: 50%; text-align: center" colspan="4">MATERI INDUKSI PERATURAN BAGI VISITOR/
                TAMU SAAT MEMASUKI
                PT HISAMITSU
                PHARMA INDONESIA
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="width: 3%" class="text-center">1</td>
                <td style="width: 32%">Menunjukkan kartu identitas dan mengambil kartu visitor di Pos Security </td>
                <td class="image-container" style="width: 10%"><img src="{{ $tamu1 }}" alt="logo"
                        class="k3-images">
                </td>
                <td style="width: 3%; text-align:center">Yes</td>
            </tr>
            <tr>
                <td colspan="3" rowspan="6" style="vertical-align: top; text-align: left; padding: 0; margin: 0;">
                    <div style="height: 5cm; overflow: auto; padding: 0; margin: 0;">

                        <table style="width: 100%">
                            <tr>
                                <td style="width: 29.3%">Tanggal / Hari</td>
                                <td> {{ $form->created_at->format('d F, Y') }}</td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td> {{ $form->nama }}</td>
                            </tr>
                            <tr>
                                <td>Nama Perusahaan</td>
                                <td>{{ $form->perusahaan }}</td>
                            </tr>
                            <tr>
                                <td>Bertemu Dengan</td>
                                <td>{{ $form->employee?->fullname ?? $form->nama_pic }}</td>
                            </tr>
                            <tr>
                                <td>Keperluan</td>
                                <td>{{ $form->tujuan_kunjungan }}</td>
                            </tr>
                            <tr>
                                <td>Lama Keperluan</td>
                                <td>{{ $form->lama_kunjungan }}</td>
                            </tr>
                        </table>

                        <table>
                            <td style="text-align: justify;">Dengan ini saya menyatakan bahwa telah mengerti dan
                                memahami peraturan yang ditetapkan oleh PT
                                Hisamitsu Pharma Indonesia. Saya akan mematuhi segala peraturan selama berada di
                                lingkungan PT Hisamitsu
                                Pharma Indonesia. Apabila saya melakukan pelanggaran, maka saya bersedia dikenakan
                                sanksi sesuai dengan
                                peraturan yang berlaku di PT Hisamitsu Pharma Indonesia.</td>
                        </table>
                    </div>

                </td>
                <td class="text-center">2</td>
                <td>Parkir kendaraan pada tempat parkir yang telah disediakan </td>
                <td class="image-container"><img src="{{ $tamu2 }}" alt="logo" class="k3-images"></td>
                <td class="text-center">Yes</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Menggunakan APD (Alat Pelindung Diri) </td>
                <td class="image-container"><img src="{{ $tamu3 }}" alt="logo" class="k3-images"></td>
                <td class="text-center">Yes</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Menjaga kebersihan dan membuang sampah pada tempat sampah yang telah disediakan </td>
                <td class="image-container"><img src="{{ $tamu4 }}" alt="logo" class="k3-images"></td>
                <td class="text-center">Yes</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Area terbatas merokok, merokok hanya di Smoking Area yang telah disediakan </td>
                <td class="image-container"><img src="{{ $tamu5 }}" alt="logo" class="k3-images"></td>
                <td class="text-center">Yes</td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Dalam keadaan sehat dan tidak terpengaruh obat-obatan terlarang serta alkohol </td>
                <td class="image-container"><img src="{{ $tamu6 }}" alt="logo" class="k3-images"></td>
                <td class="text-center">Yes</td>
            </tr>
            <tr>
                <td class="text-center">7</td>
                <td>Menggunakan jalur pedestrian bagi pejalan kaki </td>
                <td class="image-container"><img src="{{ $tamu7 }}" alt="logo" class="k3-images"></td>
                <td class="text-center">Yes</td>
            </tr>

            <tr>
                <td colspan="7" style="text-align: center; padding: 1px;">
                    <table style="width: 50%; margin: 0 auto;">
                        <tr>
                            <td style="text-align: center; width: 50%; padding: 0">Yang Mengajukan </td>
                            <td style="text-align: center; width: 50%; padding: 0">Mengetahui</td>
                        </tr>

                        <tr>
                            <td style="height: 1.2cm; text-align: center">
                                <img style="height: 1.2cm;position: static; padding-top: 0; padding-bottom: 0"
                                    src="data:image/svg;base64, {!! $qrcode1 !!}" />
                            </td>
                            <td style="height: 1.2cm; text-align: center">
                                @if ($form->waktu_bertemu)
                                    <img style="height: 1.2cm;position: static; padding-top: 0; padding-bottom: 0"
                                        src="data:image/svg;base64, {!! $qrcode2 !!}" />
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: center; padding: 0; text-transform: uppercase;">{{ $form->nama }}
                            </td>
                            <td style="text-align: center; padding: 0; text-transform: uppercase;">
                                {{ $form->employee?->fullname ?? $form->nama_pic }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
