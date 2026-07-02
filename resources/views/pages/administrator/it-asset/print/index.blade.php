{{-- @dd($selectedAssets) --}}
@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

   <style>
      .sticker-container {
         display: grid;
         grid-template-columns: repeat(3, 50mm);
         grid-template-rows: repeat(10, 18mm);
         gap: 8px 18px;
      }

      @media print {
         .no-print, .no-print * {
            display: none;
         }

         .print-scale-90 {
            transform: scale(0.9);
         }

         .print-fs-5 {
            font-size: 90%;
         }

         .print-fs-6 {
            font-size: 80%;
         }

         .page-break {
            page-break-after: always;
            break-after: page;
         }

         .print-no-border {
            border: none !important;
         }

         .print-area {
            margin-top: -80px;
         }
      }
   </style>
@endsection

@section('content') 
   <div class="row no-print">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Print IT Assets</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
                  <li class="breadcrumb-item active">Print</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="row row-cols-1 g-4 print-area">
      <div class="col">
         <div class="card">
            <div class="card-header align-items-center d-flex justify-content-between gap-1 no-print">
               <h3>Setup</h3>
               <div class="fs-5 align-items-center d-flex gap-2">
                  <label for="inverse" class="m-0">Inverse</label>
                  <input type="checkbox" id="inverse" class="inverse-btn form-check-input m-0">
               </div>
            </div>
            <div class="card-body">
               @php
                  $selectedCount = 0;
                  $totalPaper = 30 * ceil($itAssetCount/30)
               @endphp

               <form action="{{ route('it_asset.print') }}" method="POST">
                  @csrf
                  <div class="setup-container d-flex flex-wrap justify-content-center gap-5 print-scale-90">
                     {{-- @foreach(range(1, ceil($totalPaper/30)) as $paper => $paper) --}}
                     @foreach(range(1, 2) as $paper => $paper)
                        <div class="paper" style="max-width: 1000px">
                           <p class="text-center fs-5 pb-0 no-print">Paper - {{ $paper + 1 }}</p>
                           <div class="sticker-container">
                              @foreach (range(1, 30) as $sticker => $item)
                                 <div class="d-flex align-items-center bg-light rounded print-no-border border rounded position-relative px-2 py-2 h-100">
                                    <input type="checkbox" id="sticker-{{ $item }}" class="sticker-checkbox position-absolute z-10 start-0 end-0 top-0 bottom-0 w-100 opacity-0" name="position[{{ $paper }}][]value" value="{{ ($sticker + ($paper * 30)) }}" checked>
                                    @php
                                       if($itAssetCount > $selectedCount) {
                                          $selectedCount++;
                                       }
                                    @endphp
                                    <div class="zero-state no-print" style="display: flex; align-items: center;">
                                       <span style="white-space: nowrap; font-size: 120%">Sticker {{ ($sticker + ($paper * 30)) + 1 }}</span>
                                    </div>
                                    <div class="filled-state">

                                    </div>
                                 </div>
                              @endforeach
                           </div>
                           <hr class="no-print">
                           <div class="page-break"></div>
                        </div>
                     @endforeach
                  </div>

                  <button type="submit" class="btn btn-primary">Generate Print</button>
               </form>
            </div>
         </div>
      </div>

   </div>
@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <!-- Select2 -->
   <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
   <script>
      $(document).ready(function() {
         $('.select2').select2()
      });

      // ------------- Initialize Flatpickr (Matching the YYYY-MM-DD format in your table) -------------
      $("#reg-date").flatpickr({
         allowInput: true,
         altInput: false,
         dateFormat: "Y-M-d",
         defaultDate: new Date(),
      });
      // ------------- End Initialize Flatpickr (Matching the YYYY-MM-DD format in your table) -------------
   </script>

   <script>
      $(document).ready(function() {
         // const totalSelectedAsset = "{{ $itAssetCount }}"
         let selectedAssets = @json($selectedAssets);
         const totalSelectedAsset = {{ $itAssetCount }}

         updateState();
         updatePreview();

         $(document).on('change', '.sticker-checkbox', function() {
            if (!$(this).is(':checked')) {
               resetPreview($(this).parent());
            }
            updateState();
            updatePreview();
         });

         $('.inverse-btn').on('change', function() {
            $('.sticker-checkbox').each(function() {
               $(this).prop('checked', !$(this).prop('checked'))

            })
            
            updateState();
            updatePreview();
         });

         $('.setup-container').on('click', '.remove-asset-btn', function() {
            const sticker = $(this).closest('.d-flex');
            const checkbox = sticker.find('.sticker-checkbox');

            selectedAssets = selectedAssets.filter(asset => asset.asset_code !== $(this).data('asset-code'));
            
            updateState();
            updatePreview();
         });
   
         function updateState(shouldCheckPaper = true) {
            $('.sticker-checkbox').each(function() {
               if ($(this).is(':checked')) {
                  $(this).closest('.d-flex').addClass('border-primary bg-primary bg-opacity-25');
               } else {
                  $(this).closest('.d-flex').removeClass('border-primary bg-primary bg-opacity-25');
                  $(this).closest('.d-flex').removeClass('label-active');
               }
            });

            if (shouldCheckPaper) {
               const availableSticker = $('.sticker-checkbox').filter(':checked').length;
               if (availableSticker < totalSelectedAsset) {
                  console.log('Need more papers');
                  const less = totalSelectedAsset - availableSticker;
                  for (let i = 0; i < Math.ceil(less/30); i++) {
                     createNewPaper();
                  }
               }

               const exeededPaper = Math.floor((availableSticker - totalSelectedAsset)/30)

               if (exeededPaper > 0) {
                  console.log('Need less papers');
                  for (let i = 0; i < exeededPaper; i++) {
                     removeLastPaper();
                  }
               }
            }
         }
   
         function updatePreview() {
            const selectedAssetsTemp = selectedAssets;
            let availableSticker = $('.sticker-checkbox:checked').get();
            
            availableSticker.forEach((sticker, index) => {
               // console.log('Available sticker index: ', sticker, index);   
               const asset = selectedAssetsTemp[index];
               if (asset) {
                  fillPreview($(sticker).parent(), asset);
               } else {
                  resetPreview($(sticker).parent());
               }
            });
         }

         function fillPreview(sticker, asset) {
            // <input> type="hidden" name="position[${$('.paper').index(sticker.closest('.paper'))}][sticker_position]" value="${sticker.find('.sticker-checkbox').val()}">
                  // <input type="hidden" name="assets[${$('.paper').index(sticker.closest('.paper'))}][${sticker.find('.sticker-checkbox').val()}][qr_code]" value='${asset.qr_code}'>

            const template = `
            <div class="gap-2">
               <div class="d-flex align-items-center px-1">
                  <input type="hidden" name="assets[${$('.paper').index(sticker.closest('.paper'))}][${sticker.find('.sticker-checkbox').val()}]" value="${asset.asset_code}">
                  <div class="d-flex flex-column flex-grow-1 justify-content-between">
                     <span class="print-fs-5 m-0 p-0" style="font-size: .6rem;">PT Hisamitsu Pharma Indonesia</span>
                     <span class="print-fs-6" style="font-size: .5rem;">${asset.asset_code}</span>
                  </div>
                  <img src="{{ asset('assets/images/dummy-qrcode.jpg') }}" alt="QR" style="width: 30%;" loading="lazy">
               </div>
            </div>
            <button class="btn no-print btn-sm btn-danger remove-asset-btn position-absolute top-0 end-0 m-2" data-asset-code="${asset.asset_code}">
               <i class="ri-delete-bin-line" style="font-size: .6rem;"></i>
            </button>
            `;
                  // <img src="data:image/svg+xml;base64,${asset.qr_code}" class="qr-code" alt="QR" style="width: 25%;">



            $(sticker).find('.zero-state').hide();
            $(sticker).find('.filled-state').html(template);
         }

         function resetPreview(sticker) {
            $(sticker).find('.zero-state').show();
            $(sticker).find('.filled-state').html('');
         }

         function createNewPaper() {
            const newPaperNumber = $('.sticker-container').length;
            const newPaperHtml = `
               <div class="paper" style="max-width: 1000px">
                  <p class="text-center fs-5 pb-0 no-print">Paper - ${newPaperNumber + 1}</p>
                  <div class="sticker-container">
                     ${[...Array(30)].map((_, index) => `
                        <div class="d-flex align-items-center bg-light rounded print-no-border border rounded position-relative px-2 py-2 h-100">
                           <input type="checkbox" id="sticker-${index}" class="sticker-checkbox position-absolute z-10 start-0 end-0 top-0 bottom-0 w-100 opacity-0" name="position[${newPaperNumber}][]value" value="${index + ((newPaperNumber) * 30)}" checked>
                           <div class="zero-state no-print h-100" style="display: flex; align-items: center;">
                              <span style="white-space: nowrap; font-size: 120%">Sticker ${index + ((newPaperNumber) * 30) + 1}</span>
                           </div>
                           <div class="filled-state">

                           </div>
                        </div>
                     `).join('')}
                  </div>
                  <hr class="no-print">
                  <div class="page-break"></div>
               </div>
            `;
            $('.setup-container').append(newPaperHtml);
            updateState(false);
         }
         
         function removeLastPaper() {
            if ($('.paper').length > 0) {
               $('.paper').last().prev('p').remove(); // Remove the paper title
               $('.paper').last().remove(); // Remove the sticker container
            }
         }
      });

   </script>

   <script>
      @if(Session::has('success'))
         toastr.options = {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
         }
         toastr.success("{{ session('success') }}");
      @endif
   </script>
@endsection