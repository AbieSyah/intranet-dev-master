@props([
   'ticket',
   'role',
   'employees',
   'risk_registers',
])

@php
   $priorityMetricChunk = App\Models\PriorityMetric::get()->sortBy('score')->groupBy('type');

   $categories = App\Models\ServiceCatalog::all()->groupBy('category')->map(fn($item) => $item->pluck('service_catalog'));

   $itAssets = App\Models\ITAsset::get()->load('employee');
@endphp

{{-- ----------------------- TRIAGE SECTION ----------------------- --}}
<div class="card shadow-sm mb-4 border-start border-4 border-primary">
   <div class="card-header bg-white fw-bold">IT Analysis Center</div>
   <div class="card-body">
      <form id="analysisForm" action="{{ route('service-ticket.verify', encrypt($ticket->id)) }}" method="POST" class="d-flex flex-column justify-content-between h-100">
         @csrf
         <div id="step1">
            {{-- <input type="hidden" name="report_for" value="{{ $ticket->report_for }}"> --}}
            {{-- Identifikasi asset --}}
            <div class="mb-3">
               <label class="fw-bold small text-uppercase">CC (Carbon Copy)</label>
               <select name="ccs[]" id="verifyCC" class="form-control select2" 
                     data-placeholder="Select CC..." multiple>
                  <option value=""></option>
                  @foreach($employees as $emp)
                     <option value="{{ encrypt($emp->id) }}" {{ $ticket->ccs->firstWhere('employee_id', $emp->id) ? 'selected' : '' }}>
                        {{ $emp->nik }} - {{ $emp->fullname }}
                     </option>
                  @endforeach
               </select>
            </div>

            @if ($ticket->type == 'it_initiative')
               <div class="mb-3">
                  <label class="fw-bold small text-uppercase">Report For</label>
                  <select name="report_for_id" id="report_for_id" class="form-control select2" 
                        data-placeholder="Select Employee..." disabled>
                     <option>{{ $ticket->reportFor->nik }} - {{ $ticket->reportFor->fullname }}</option>
                     {{-- @foreach($employees as $emp)
                        <option value="{{ encrypt($emp->id) }}" {{ $ticket->reportFor->id == $emp->id ? 'selected' : '' }}>
                           {{ $emp->nik }} - {{ $emp->fullname }}
                        </option>
                     @endforeach --}}
                  </select>
               </div>
            @endif

            <div class="mb-3">
               <label class="fw-bold small text-uppercase">Categories<span class="text-danger">*</span></label>
               <select name="category" id="category" class="form-control select2" 
                     data-placeholder="Select Category..." required>
                  <option value=""></option>
                  @foreach($categories as $category => $catalogs)
                     <option value="{{ $category }}" {{ $ticket->category == $category ? 'selected' : '' }} class="text-capitalize" required>
                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                     </option>
                  @endforeach
               </select>
            </div>

            <div class="mb-3">
               <label class="fw-bold small text-uppercase">Catalogs<span class="text-danger">*</span></label>
               <select name="catalog" id="catalog" class="form-control select2" 
                     data-placeholder="Select Catalog..." required>
                  <option value=""></option>
               </select>
            </div>

            <div class="mb-3">
               <label class="fw-bold small text-uppercase">Type<span class="text-danger">*</span></label>
               <select name="type" class="form-control select2" id="type" data-placeholder="Select Type..." required>
                  @if ($ticket->type == 'it_initiative')
                  <option value="it_initiative">IT INITIATIVE</option>
                  @else
                  <option value="incident" {{ $ticket->type == 'incident' ? 'selected' : '' }}>INCIDENT</option>
                  <option value="request" {{ $ticket->type == 'request' ? 'selected' : '' }}>REQUEST</option>
                  @endif
               </select>
            </div>

            <div class="mb-3">
               <label class="small fw-bold text-uppercase">Update Status<span class="text-danger">*</span></label>
               <select name="status" class="form-select select2" required>
                  <option value="process" {{ $ticket->current_status == 'process' ? 'selected' : '' }}>PROCESS</option>
                  <option value="hold" {{ $ticket->current_status == 'hold' ? 'selected' : '' }}>HOLD</option>
               </select>
            </div>

            <div class="row mb-3">
               <hr class="col-12">
               <div class="col-12 text-center">
                  <h6 class="small fw-bold text-uppercase">Risk Assessment</h6>
               </div>
               <div class="d-flex flex-wrap gap-2">
                  @foreach ($priorityMetricChunk as $type => $priorityMetrics)
                     @php
                        $popoverContent = $priorityMetrics->map(fn($metric) => $metric->definition." = ".$metric->description)->implode('<br><br>');
                     @endphp
                     <div class="flex-1" style="min-width: 150px">
                        <label 
                           class="small fw-bold text-uppercase"
                           data-bs-toggle="popover" 
                           data-bs-placement="left"
                           data-bs-title="{{ ucfirst($type) }} Level Guide" 
                           data-bs-content="{{ $popoverContent }}" 
                           data-bs-trigger="hover focus"
                           data-bs-html="true">
                              {{ ucfirst($type) }}<span class="ri ri-information-line" data-bs-popup="true" data-bs-content=""></span><span class="text-danger">*</span>
                        </label>
                        <select name="{{ $type }}" class="form-select" required>
                           <option value="">{{ ucfirst($type) }} Level</option>
                           @foreach ($priorityMetrics as $metric)
                              <option value="{{ $metric->score }}" {{ $ticket->$type == $metric->score ? 'selected' : '' }}>
                                 {{ $metric->definition }}(Score: {{ $metric->score }})
                              </option>
                           @endforeach
                        </select>
                     </div>
                  @endforeach
               </div>

               <div id="riskMatrix" class="col-12 mt-3" >
                  <label 
                     class="small fw-bold text-uppercase" 
                     data-bs-toggle="popover" 
                     data-bs-placement="left"
                     data-bs-title="Risk Register" 
                     data-bs-content="
                        I = Impact<br>
                        P = Probability<br>
                        S = Score (I x P) <br><br>
                        
                        <strong>Impact : </strong><br>
                        1(low) = Permintaan bersifat administratif, bantuan penggunaan (how-to), atau perbaikan perangkat personal yang masih bisa menyala.	Operasional <br>
                        2(medium) = Gangguan pada sebagian fitur atau kinerja lambat. Pekerjaan terhambat tapi masih bisa berjalan (ada solusi sementara).	Operasional, Reputasi <br>
                        3(high) = Layanan kritis mati total, menghambat operasional inti perusahaan, atau risiko kehilangan data Perusahaan	Kerugian Financial, Reputasi, Operasional <br><br>

                        <strong>Probability : </strong><br>
                        1(Jarang / Hampir tidak pernah) = tingkat terjadi 1 tahun sekali <br>
                        2(Medium) = tingkat terjadi 1 bulan sekali <br>
                        3(Sering Terjadi) = tingkat terjadi setiap minggu

                     " 
                     data-bs-trigger="hover focus"
                     data-bs-html="true">
                        Risk Register<span class="ri ri-information-line" data-bs-popup="true" data-bs-content=""></span>
                  </label>
                  <select name="risk_register_id" id="risk_register_id" class="form-select select2">
                     <option value="">Risk Level</option>
                     @foreach ($riskRegisters as $riskRegister)
                        <option value="{{ encrypt($riskRegister->id) }}" {{ $ticket->risk_register_id == $riskRegister->id ? 'selected' : '' }}>
                           {{ $riskRegister->risk_id }} - {{ $riskRegister->name }} (I: {{ $riskRegister->impact }}, P: {{ $riskRegister->probability }}, S: {{ $riskRegister->score }})
                        </option>
                     @endforeach
                  </select>
               </div>
            </div>

            <div class="mb-3">
               <hr>
               <h6 class="small fw-bold text-uppercase text-center mb-2">Related Asset</h6>
               <div id="asset-container">
                  @if ($ticket->itAssets->count() > 0)
                     @foreach ($ticket->itAssets as $index => $selectedAsset)
                        <div class="asset-row mb-4" data-index="{{ $index + 1 }}">
                           <div class="d-flex justify-content-between align-items-center">
                              <label class="fw-bold small text-uppercase text-primary">Asset {{ $index + 1 }}</label>
                              <button type="button" class="btn btn-link btn-sm remove-asset-btn" style="color: red">
                                 <i class="ri-delete-bin-line"></i> Remove
                              </button>
                           </div>
                           <div class="ms-2 ps-4 pb-2 border-start border-primary">
                              <div class="mb-2">
                                 <label class="fw-bold small text-uppercase text-muted">IT Asset</label>
                                 <select name="assets[{{ $index }}]" id="asset-{{ $index + 1 }}" class="form-control select2" data-placeholder="Select Asset...">
                                    <option value=""></option>
                                    @foreach ($itAssets as $asset)
                                       <option value="{{ encrypt($asset->id.'|'.$asset->employee->id) }}" {{ $asset->id == $selectedAsset->id ? 'selected' : '' }}>
                                          {{ $asset->asset_code }} - {{ $asset->brand }} ({{ $asset->employee->fullname }})
                                       </option>
                                    @endforeach
                                 </select>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  @else
                     <div class="asset-row mb-4" data-index="1">
                        <div class="d-flex justify-content-between align-items-center">
                           <label class="fw-bold small text-uppercase text-primary">Asset 1</label>
                        </div>
                        <div class="ms-2 ps-4 pb-2 border-start border-primary">
                           <div class="mb-2">
                              <label class="fw-bold small text-uppercase text-muted">IT Asset</label>
                              <select name="assets[0]" id="asset-1" class="form-control select2" data-placeholder="Select Asset...">
                                 <option value=""></option>
                                 @foreach ($itAssets as $asset)
                                    <option value="{{ encrypt($asset->id.'|'.$asset->employee->id) }}">
                                       {{ $asset->asset_code }} - {{ $asset->brand }} ({{ $asset->employee->fullname }})
                                    </option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                     </div>
                  @endif
               </div>
               
               <div class="text-center">
                  <button type="button" id="add-asset-btn" class="btn btn-outline-secondary w-50 shadow-sm">
                        <i class="ri-user-add-line"></i> Add Asset +
                  </button>
               </div>
            </div>
         </div>
         <div class="card-footer bg-transparent border-0 d-flex gap-2">
            <button id="submit-btn" type="submit" form="analysisForm" class="btn btn-success flex-fill shadow">
               <i class="ri-check-double-line"></i> <span class="text">Save Analysis</span>
            </button>
         </div>
      </form>
   </div>
</div>
{{-- ----------------------- END TRIAGE SECTION ----------------------- --}}

@pushOnce('scripts')
   <script>
      // --------------------------------- TRIAGE STEPPER LOGIC ---------------------------------
      // handle category change -> update catalog options
      const ticketType = "{{ $ticket->type }}"
      const selectedCatalog = "{{ $ticket->catalog }}"
      const serviceCategories = @json($categories);
      function inputCatalogOptions(el) {
         const val = $(el).val();
         console.log(val);
         
         $("#catalog").empty()
         $('#catalog').append(new Option('', ''));
         serviceCategories[val].forEach(catalog => {
            $('#catalog').append(new Option(catalog.toUpperCase(), catalog));
         });
         $('#catalog').append(new Option('Lain-lain', 'lain-lain'));

         if (ticketType === "it_initiative") {
            const selectedCatalog = "{{ $ticket->catalog }}";
            if (selectedCatalog) {
               $('#catalog').val(selectedCatalog).trigger('change');
            }
         }
      }
      $('#category').change(function(e) {
         inputCatalogOptions(this);

      })
      
      $(document).ready(function() {
         $('#category').trigger('change');
         $("#catalog").val(selectedCatalog);

         let currentStep = 1;
         const totalSteps = 2;

         // Fungsi Navigasi Utama
         function updateStepper(direction) {
            // Validasi jika arahnya ke depan (Next)
            if (direction === 'next') {
               let isValid = true;
               $(`#step${currentStep} [required]`).each(function() {
                  if ($(this).val() === "" || $(this).val() === null) {
                     isValid = false;
                     $(this).addClass('is-invalid');
                     toastr.info(`Please complete all required fields in Step ${currentStep}`);
                     return false;
                  } else {
                     $(this).removeClass('is-invalid');
                  }
               });

               if (!isValid) return; // Stop jika tidak valid
            }

            // Transisi antar Step
            $(`#step${currentStep}`).fadeOut(200, function() {
               if (direction === 'next') {
                  currentStep++;
                  // addStep2();
               } else {
                  currentStep--;
                  // removeStep2();
               }

               $(`#step${currentStep}`).fadeIn(200);
               updateButtons();
            });
         }

         // Fungsi Mengatur Visibilitas Tombol
         function updateButtons() {
            // Logika Tombol Back (Sembunyi di Step 1)
            if (currentStep === 1) {
                  $('#back-btn').addClass('d-none');
            } else {
                  $('#back-btn').removeClass('d-none');
            }

            // Logika Tombol Next vs Submit
            if (currentStep === totalSteps) {
                  $('#next-btn').addClass('d-none');
                  // $('#submit-btn').removeClass('d-none');
            } else {
                  $('#next-btn').removeClass('d-none');
                  // $('#submit-btn').addClass('d-none');
            }
            
            // Opsional: Update Progress Bar/Indicator jika ada
            console.log("Current Step: " + currentStep);
         }

         $('#make_request_approval').change(function() {
            const requestElement = `
               <div class="mb-3">
                  <label class="fw-bold small text-uppercase text-muted">Direct Supervisor<span class="text-danger">*</span></label>
                  <select name="supervisor" id="supervisor-1" class="form-control select2" data-placeholder="Search Supervisor..." required>
                        <option value=""></option>
                        @foreach($employees as $emp)
                           <option value="{{ encrypt($emp->id) }}" {{ ($ticket->asset_id == $emp->id) ? 'selected' : '' }}>
                              {{ $emp->fullname }} - {{ ($emp->position->nama ?? 'N/A') }} ({{ $emp->department->name ?? 'N/A' }})
                           </option>
                        @endforeach
                  </select>
               </div>
               <div class="mb-3">
                  <label class="fw-bold small text-uppercase text-muted">Dept Head<span class="text-danger">*</span></label>
                  <select name="dept_head" id="dept-head-1" class="form-control select2" data-placeholder="Search Department Head..." required>
                        <option value=""></option>
                        @foreach($employees as $emp)
                           <option value="{{ encrypt($emp->id) }}" {{ ($ticket->asset_id == $emp->id) ? 'selected' : '' }}>
                              {{ $emp->fullname }} - {{ ($emp->position->nama ?? 'N/A') }} ({{ $emp->department->name ?? 'N/A' }})
                           </option>
                        @endforeach
                  </select>
               </div>
            `

            if ($(this).is(':checked')) {
               $('#otherCatalogBox').html(requestElement);
               $('#otherCatalogBox').show();
               $('#supervisor-1, #dept-head-1').attr('required', true);
               initSelect2($('#supervisor-1'));
               initSelect2($('#dept-head-1'));
            } else {
               $('#otherCatalogBox').hide();
               $('#otherCatalogBox').html('');
               $('#supervisor-1, #dept-head-1').attr('required', false).val('').trigger('change');
            }
         })

         // Toggle Change Management Box
         $('#changeToggle').on('change', function() {
            if($(this).is(':checked')) {
               $('#replacementBox').slideDown();
            } else {
               $('#replacementBox').slideUp();
            }
         });


         // Fungsi untuk menginisialisasi Select2 pada elemen tertentu
         function initSelect2(element) {
            element.select2({
               // theme: 'bootstrap-5', // Sesuaikan dengan tema Anda
               allowClear: true,
            });
         }

         function initAssetAjax(userId, assetId) {
            userId.on('change', function() {
               const assetId = $(this).val();
               
               // Reinitialize assetSelect with AJAX
               assetId.select2({
                  allowClear: true,
                  ajax: {
                     url: `/administrator/service-desk/employee/${assetId}/assets`,
                     dataType: 'json',
                     processResults: function(data) {
                        return {
                           results: data.map(a => ({
                              id: a.encrypted_id,
                              text: `${a.asset_code} - ${a.brand}`
                           }))
                        };
                     },
                  },
                  placeholder: '-- Choose Asset --'
               });
            }).trigger('change'); 
         }

         // Inisialisasi awal untuk baris pertama
         initSelect2($('.select2'));

         // -------- handle step 2 user choices ----------
         let assetCount = {{ $ticket->itAssets->count() ?? 1 }};

         // Event Klik Tombol Add +
         $('#add-asset-btn').on('click', function() {
            assetCount++;
            
            // Template baris baru
            let newRow = `
            <div class="asset-row mb-4" data-index="${assetCount}" style="display:none;">
               <div class="d-flex justify-content-between align-items-center">
                  <label class="fw-bold small text-uppercase text-primary">Asset ${assetCount}</label>
                  <button type="button" class="btn btn-link btn-sm remove-asset-btn" style="color: red">
                     <i class="ri-delete-bin-line"></i> Remove
                  </button>
               </div>
               <div class="ms-2 ps-4 pb-2 border-start border-primary">
                  <div class="mb-2">
                     <label class="fw-bold small text-uppercase text-muted">IT Asset</label>
                     <select name="assets[${assetCount}]" id="asset-${assetCount}" class="form-control select2" data-placeholder="Select Asset...">
                        <option value=""></option>
                        @foreach ($itAssets as $asset)
                           <option value="{{ encrypt($asset->id.'|'.$asset->employee->id) }}">
                              {{ $asset->asset_code }}{{ $asset->brand }} ({{ $asset->employee->fullname }})
                           </option>
                        @endforeach
                     </select>
                  </div>
               </div>
            </div>`;

            // Append ke kontainer
            $('#asset-container').append(newRow);
            
            // Animasi muncul dan inisialisasi Select2 baru
            let $newRowElement = $(`.asset-row[data-index="${assetCount}"]`);
            $newRowElement.fadeIn(300);
            initSelect2($newRowElement.find('.select2'));
            // initAssetAjax($newRowElement.find(`#user-${assetCount}`), $newRowElement.find(`#asset-${assetCount}`))
         });

         // Event Klik Tombol Remove
         $(document).on('click', '.remove-asset-btn', function() {
            $(this).closest('.asset-row').fadeOut(300, function() {
               $(this).remove();
            });
         });



         // ----------------- form handling -----------------
         $('#analysisForm').on('submit', function(e) {
            console.log(true);
            
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);

            function clearErrors() {
               $('.is-invalid').removeClass('is-invalid');
               $('.select2-container').removeClass('is-invalid');
               $('.text-danger').remove();
            }

            Swal.fire({
               title: 'Confirm Analysis Submission?',
               text: "Make sure all information is correct before submitting.",
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, submit it',
               showLoaderOnConfirm: true,
               reverseButtons: true,
               preConfirm: () => {
                  clearErrors();

                  return $.ajax({
                     url: url,
                     type: 'POST',
                     data: formData,
                     dataType: 'json',
                     contentType: false,
                     processData: false,
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                  }).then(
                     (response) => {
                        return response;
                     },
                     (jqXHR) => {
                        if (jqXHR.status === 422) {
                           const errors = jqXHR.responseJSON.errors;
                           $.each(errors, function(key, value) {
                              // Handle array inputs (users.0.user -> users[0][user])
                              let nameAttr = key.split('.').map((s, i) => i > 0 ? `[${s}]` : s).join('');
                              let input = $(`[name="${key}"], [name="${nameAttr}"]`);
                              
                              if (input.hasClass('select2-hidden-accessible')) {
                                 input.next('.select2-container').addClass('is-invalid');
                                 input.closest('div').append('<small class="text-danger d-block">' + value[0] + '</small>');
                              } else {
                                 input.addClass('is-invalid');
                                 input.after('<small class="text-danger d-block">' + value[0] + '</small>');
                              }
                           });
                           Swal.showValidationMessage('Please fix the validation errors and try again. Error: ' + jqXHR.message);
                        } else {
                           Swal.showValidationMessage(`Error ${jqXHR.status}: ${jqXHR.responseJSON.message || 'Server Error'}`);
                        }
                        return false; // Mencegah modal tertutup karena error
                     }
                  );
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed && result.value) {
                  Swal.fire({
                     title: 'Berhasil!',
                     text: 'Tiket telah diverifikasi.',
                     icon: 'success',
                     timer: 1500,
                     showConfirmButton: false
                  }).then(() => {
                     window.location.href = "{{ route('service-management.workspace', ['id' => encrypt($ticket->id), 'role' => encrypt('it')]) }}";
                  });
               }
            });
         });
         // --------------------------------- END STEPPER LOGIC ---------------------------------
      });
   </script>
@endPushOnce