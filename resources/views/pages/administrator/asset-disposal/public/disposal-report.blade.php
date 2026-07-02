<!DOCTYPE html>
<html>
<head>
   <style>
      body { 
         font-family: 'Arial', sans-serif;
         font-size: 12px;
         line-height: 1.6;
      }
      .header { 
         text-align: center;
         font-weight: bold;
         text-decoration: underline;
         margin-bottom: 5px;
         font-size: 16px;
      }
      .sub-header { 
         text-align: center;
         font-weight: bold;
         margin-bottom: 20px;
         font-size: 14px;
      }
      .section-title { 
         font-weight: bold;
         margin-top: 15px;
      }
      .table { 
         width: 100%;
         border-collapse: collapse;
         margin-top: 10px;
         font-size: 11px
      }
      .table th, .table td { 
         border: 1px solid black;
         padding: 8px;
         text-align: left;
      }
      .footer-table { 
         width: 100%;
         margin-top: 30px;
         border: none;
      }
      .footer-table td { 
         width: 25%;
         text-align: center;
         border: none;
         vertical-align: top;
      }
      .page-break { 
         page-break-after: always;
      }
      .signature-space { 
         height: 60px;
      }
      @page {
         margin: 230px 50px 50px 50px;
      }
      header {
         position: fixed;
         top: -230px; /* Tarik ke atas mendekati tepi kertas */
         left: -60px;
         right: -60px;
         text-align: center;
      }
      .kop-image {
         width: 100%; /* Agar lebar gambar mengikuti lebar kertas */
         height: auto;
      }
      footer {
         width: 100%;
         position: fixed;
         bottom: 0;
         font-size: 10px;
      }
   </style>
</head>
<body>
   <span class="pagenum"></span>

   <header>
      <img src="{{ public_path('assets/images/kop-surat.jpg') }}" class="kop-image">
   </header>

   <div class="header">BERITA ACARA</div>
   <div class="sub-header">PEMUSNAHAN ASET IT</div>
   <div style="text-align: center; margin-bottom: 20px;">No: {{ $transaction['transaction_number'] }}</div>

   <p>
      Pada hari ini, <strong>{{ now()->translatedFormat('l') }}</strong> tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>
      bertempat di PT. HISAMITSU PHARMA INDONESIA Headquarters/Factory Jl. H.R. Moch. Mangundiprojo Buduran, Sidoarjo.
   </p>

   <div class="section-title">BAHWA-</div>
   <p>Telah dimusnahkan Aset IT, yaitu berupa :</p>

   <table class="table"> 
      <thead>
         <tr>
               <th style="width: 5%; text-align: center;">No</th>
               <th style="text-align: center;">Kode</th>
               <th style="text-align: center;">Brand</th>
               <th style="text-align: center;">Hardware</th>
               <th style="text-align: center;">Software</th>
               <th style="width: 10%; text-align:center">Status</th>
               <th style="width: 18%; text-align:center">Tahun Pengadaan</th>
               <th style="width: 16%; text-align: center;">Harga Jual</th>
         </tr>
      </thead>
      <tbody>
         @foreach($transaction['disposal_items'] as $index => $disposalItem)
         <tr>
               <td style="text-align: center;">{{ $index + 1 }}</td>
               {{-- Accessing nested array keys for relationships --}}
               {{-- <td>{{ $disposalItem['it_asset']['asset_type']['name'] }}</td> --}}
               <td style="white-space: nowrap">{{ $disposalItem['it_asset']['asset_code'] }}</td>
               <td>{{ $disposalItem['it_asset']['brand'] }}</td>
               <td>{{ $disposalItem['it_asset']['specification'] }}</td>
               <td>{{ $disposalItem['it_asset']['software'] }}</td>
               <td style="text-align: center">{{ $disposalItem['current_status'] }}</td>
               <td>{{ \Carbon\Carbon::parse($disposalItem['it_asset']['year_registered'])->format('d/m/Y') }}</td>
               <td style="text-align: right">Rp {{ number_format($disposalItem['sale_price'], 0, ',', '.') }}</td>
         </tr>
         @endforeach
         <tr>
               <td style="border: 0; "></td>
               {{-- Accessing nested array keys for relationships --}}
               <td style="border: 0; "></td>
               <td style="border: 0; "></td>
               <td style="border: 0; "></td>
               <td style="border: 0; "></td>
               <td style="border: 0; "></td>
               <td style="border: 0; text-align: right">Total:</td>
               <td style="border: 0; text-align: right">Rp {{ number_format($totalSalePrice, 0, ',', '.') }}</td>
         </tr>
      </tbody>
   </table>

   <div class="section-title">Catatan IT:</div> 
   <ol>
      <li>Pemusnahan aset dilakukan dengan cara dijual sesuai Prosedur Tetap No.HPI-IT-DK-09.</li> 
      <li>Seluruh data dan informasi perusahaan telah dihapus secara permanen.</li> 
      <li>Perangkat yang disebutkan dinyatakan layak untuk dijual.</li> 
   </ol>

   <p>Demikian Berita Acara ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p> 

   <table class="footer-table">
      <tr>
         <td>Diterbitkan<br>({{ $signatures[0]['position'] }})</td> 
         <td>Diperiksa<br>({{ $signatures[1]['position'] }})</td> 
         <td>Disetujui<br>({{ $signatures[2]['position'] }})</td> 
         <td>Disetujui<br>({{ $signatures[3]['position'] }})</td> 
      </tr>
      <tr>
         @foreach ($signatures as $signature)
            <td class="signature-space">
               <img src="data:image/svg;base64, {!! $signature['qrcode'] !!} ">
               <div style="font-size: 8px;">Scan to Verify</div>
            </td>
         @endforeach
      </tr>
      <tr>
         @foreach ($signatures as $signature)
            <td><strong>{{ $signature['name'] }}</strong><br>{{ $signature['date'] }}</td> 
         @endforeach
      </tr>
   </table>

   {{-- <div class="page-break"></div> --}}
   <br><br>

   <div class="header">PIHAK PEMBELI</div> 
   <table class="table">
      <tr>
         <td>
            <div>
               <p><strong>Nama :</strong> {{ $transaction['buyer_name'] }}</p> 
               <p><strong>Alamat :</strong> {{ $transaction['buyer_address'] }}</p> 
               <p><strong>Email :</strong> {{ $transaction['buyer_email'] }}</p> 
               <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($transaction['validated_at'])->format('d/m/Y') }}</p> 
            </div>
         </td>
         <td>
            <div style=" text-align: right; padding-right: 50px;">
               <p>Tanda Tangan Pembeli,</p>
               <div class="signature-space">
                  <img src="data:image/svg;base64, {!! $buyerQrcode !!} ">
                  <div style="font-size: 8px;">Scan to Verify</div>
               </div>
               <p><strong>( {{ $transaction['buyer_name'] }} )</strong></p>
            </div>
         </td>
      </tr>
   </table>
   <footer>
      Printed from INTRANET - IT Service Management System by {{ Auth::user()->employee->fullname }} - {{ Auth::user()->employee->nik }} {{ now()->format('d/m/Y H:i:s') }}
   </footer>
</body>
</html>