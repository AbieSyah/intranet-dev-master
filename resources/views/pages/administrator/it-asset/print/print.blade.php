<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>Cetak Label T&J 107</title>
   <style>
      /* Pengaturan Ukuran Kertas T&J 107 */
      @page {
         size: 165mm 205mm; /* Ukuran total kertas label */
         margin: 0;         /* Margin ditangani oleh wrapper body */
      }

      body {
         margin: 0;
         padding: 0;
         font-family: Arial, sans-serif;
         background-color: #fff;
      }

      /* * {
         outline: 1px solid red;
      } */

      .paper {
         padding: 4.4mm 0;
      }
      
      table {
         width: 100%;
         margin: auto;
      }

      .sticker {
         width: 50mm;
         height: 18mm;
         padding: .5mm 2mm;
      }

      .sticker > div {
         padding: 0 1.5mm;
      }

      .sticker .label {
         font-size: 0.7rem;
         margin: 0;
      }

      .sticker .label .asset-sku {
         font-size: 0.6rem;
      }

      .sticker .qr-image {
         width: 11mm;
         height: 11mm;
      }
   </style>
</head>
<body>
   <div class="setup-container d-flex flex-wrap justify-content-center gap-5 print-scale-90">
      @foreach(range(0, count($selectedAssets) - 1) as $paper)
         <div class="paper">
            <table>
               @foreach (collect(range(0 + ($paper * 30), 29 + ($paper * 30)))->chunk(3) as $row)
                  <tr>
                     @foreach ($row as $sticker)
                        <td class="sticker">
                           <div>
                              @isset($selectedAssets[$paper][$sticker])
                                 <table>
                                    <tr>
                                       <td>
                                          <div class="label">
                                             <div class="company-title">PT Hisamitsu Pharma Indonesia</div>
                                             <div class="asset-sku">{{ $selectedAssets[$paper][$sticker]['asset_code'] ?? '-' }}</div>
                                          </div>
                                       </td>
                                       <td>
                                          <img src="data:image/svg+xml;base64,{!! base64_encode($selectedAssets[$paper][$sticker]['qr_code'] ?? '') !!}" alt="QR" class="qr-image" loading="lazy">
                                       </td>
                                    </tr>
                                 </table>
                              @endif
                           </div>
                        </td>
                     @endforeach
                  </tr>
               @endforeach
            </table>
         </div>
      @endforeach
   </div>
   {{-- @foreach (range(0, $selectedAssets->count() - 1) as $page)
      <div class="page">
         @foreach (range(0, 29) as $label)
            <div class="label-box">
               
               <div class="qr-placeholder">

               </div>

               <div class="asset-code">
                  {{ $asset['asset_code'] ?? '-' }}
               </div>

            </div>
         @endforeach
      </div>
   @endforeach --}}
   {{-- @foreach ($selectedAssets as $pageItems)
      <div class="page">
         @foreach ($pageItems as $asset)
            <div class="label-box">
               
               <div class="qr-placeholder">
                  [QR CODE]
               </div>

               <div class="asset-code">
                  {{ $asset['asset_code'] ?? '-' }}
               </div>

            </div>
         @endforeach

         @if ($pageItems->count() < 6)
            @for ($i = 0; $i < (6 - $pageItems->count()); $i++)
               <div class="label-box" style="border: none;"></div>
            @endfor
         @endif
      </div>
   @endforeach --}}
</body>
</html>