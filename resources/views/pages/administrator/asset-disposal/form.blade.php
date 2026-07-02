@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

   <style>
      .ctimeline { position: relative; padding-left: 28px; list-style: none; }
      .ctimeline-item { position: relative; width: 100%; padding: 0 0 .75rem 0 !important }
      .ctimeline-icon { 
         position: absolute; left: -32px; top: .5rem; width: 24px; height: 24px; 
         border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;
      }
   </style>

@endsection

@section('content') 
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dispose IT Asset</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
                  <li class="breadcrumb-item active">Dispose</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="col-lg-12">
      <div class="card">
         <div class="card-header align-items-center d-flex justify-content-between gap-1">
            <div></div>
            <div>
               <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
            </div>
         </div>
         <div class="card-body">
            <form method="post" id="disposal-form" enctype="multipart/form-data">
               @csrf
               @isset($isRevision)
                  <div>   
                     <h5 class="text-center mb-4">Revision</h5>
                     <div class="ctimeline-item">
                        <div class="p-2 border-4 rounded bg-light border-start border-warning">
                           <strong>
                              @if ($latestLog->status == 'waiting')
                                 <span class="text-uppercase">{{ $latestLog->status }}</span>
                                 for {{ $latestLog->approvalPath->role_name }}'s Response
                              @else
                                 {{ $latestLog->approvalPath->role_name }} 
                                 <span class="text-uppercase">{{ $latestLog->status }}</span>
                              @endif
                           </strong>
                           @if ($latestLog->status == 'revision')
                              <div class="py-2 text-small">
                                 {{ $latestLog->comments?? 'N/A' }}
                              </div>
                           @endif
                           <div class="small text-muted">{{ $latestLog->created_at->format('d-m-y, h:i A') }}</div>
                        </div>
                     </div>
                  </div>
               @endisset

               <div class="row">
                  <div class="col-lg-6">
                     <hr class="mt-5">
                     <h5 class="text-center mb-4">Buyer Information</h5>
                     @isset($isRevision)
                        <input type="hidden" name="asset_disposal" value="{{ encrypt($assetDisposal->id) }}">
                     @endisset

                     <div class="row g-3 pb-3">
                        <div class="col-12 col-md-6">
                           <label class="form-label fw-bold text-muted">BUYER NAME</label>
                           <input type="text" required value="{{ $assetDisposal->buyer_name?? '' }}" name="buyer_name" class="form-control required" placeholder="e.g. John Doe">
                        </div>
                        
                        <div class="col-12 col-md-6">
                           <label class="form-label fw-bold text-muted">PHONE NUMBER</label>
                           <input type="number" inputmode="numeric" required value="{{ $assetDisposal->buyer_phone?? '' }}" name="buyer_phone" class="form-control required" placeholder="0812xxxx">
                        </div>

                        <div class="col-12 col-md-6">
                           <label class="form-label fw-bold text-muted">EMAIL ADDRESS</label>
                           <input type="email" required value="{{ $assetDisposal->buyer_email?? '' }}" name="buyer_email" class="form-control required" placeholder="buyer@example.com">
                        </div>

                        <div class="col-12 col-md-6">
                           <label class="form-label fw-bold text-muted">OFFICE / HOME ADDRESS</label>
                           <input type="text" required value="{{ $assetDisposal->buyer_address?? '' }}" name="buyer_address" class="form-control required" placeholder="Street Name, City...">
                        </div>
                     </div>
                  </div>
                  
                  <div class="col-lg-6">
                     <hr class="mt-5">
                     <h5 class="text-center mb-4">Reason</h5>
                     <label class="form-label fw-bold text-muted">REASONS</label>
                     <textarea name="reason" class="form-control" rows="5" id="reason" required placeholder="Reason for Disposal">{{ $assetDisposal->reason?? null }}</textarea>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                     @if(isset($assetDisposal) && $assetDisposal->file_path)
                        <div id="existing-file">
                           @php $extension = pathinfo($assetDisposal->file_path, PATHINFO_EXTENSION); @endphp
                           
                           @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'pdf']))
                              <a href="{{ asset('storage/' . $assetDisposal->file_path) }}" target="_BLANK">
                                 <img src="{{ asset('storage/' . $assetDisposal->file_path) }}" class="img-thumbnail" style="max-height: 100px;">
                              </a>
                           @else
                              <div class="p-3">
                                 <i class="ri-file-pdf-fill text-danger" style="font-size: 2rem;"></i>
                                 <p class="mb-0 small text-truncate">{{ basename($assetDisposal->file_path) }}</p>
                              </div>
                           @endif
                        </div>
                     @endif
                     <label for="disposal_file" class="form-label fw-bold text-muted">Disposal Document (Image/PDF)</label>
                     <input type="file" class="form-control" id="disposal_file" name="disposal_file" accept=".jpeg,.png,.jpg,.pdf">
                     <div class="form-text">Leave empty if you don't want to change the existing file.</div>
                  </div>
               </div>

               <hr class="mt-5">
               <h5 class="text-center mb-4">Disposal Asset List</h5>

               <div class="overflow-auto">
                  <table class="table table-responsive table-responsive table-striped bordered" id="table_it_asset">
                     <thead>
                        <tr>
                           <th scope="col" style="text-align:center">Code</th>
                           <th scope="col" style="text-align:center">Brand</th>
                           <th scope="col" style="text-align:center">Asset Type</th>
                           <th scope="col" style="text-align:center">Status</th>
                           {{-- <th scope="col" style="text-align:center">Status</th> --}}
                           {{-- <th scope="col" style="text-align:center">Year Registered</th> --}}
                           {{-- <th scope="col" style="text-align:center">Age</th> --}}
                           <th scope="col" style="text-align:center">Owner</th>
                           {{-- <th scope="col" style="text-align:center">Area</th>
                           <th scope="col" style="text-align:center">Department</th> --}}
                           <th scope="col" style="text-align:center">Buy Price</th>
                           <th scope="col" style="min-width: 150px; text-align:center">Sale Price</th>
                           <th scope="col" style="min-width: 200px; text-align:center">Reason</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse (isset($isRevision) ? $assetDisposal->disposalItems : $itAssets as $index => $item)
                           <tr>
                              <td>{{ isset($isRevision) ? $item->itAsset->asset_code : $item->asset_code }}</td>
                              <td>{{ isset($isRevision) ? $item->itAsset->brand : $item->brand }}</td>
                              <td>{{ isset($isRevision) ? $item->itAsset->assetType->name : $item->assetType->name }}</td>
                              <td>{{ isset($isRevision) ? $item->current_status : $item->status }}</td>
                              <td>{{ isset($isRevision) ? $item->itAsset->employee->fullname.' - '.$item->itAsset->employee->department->name?? 'N/A' : $item->employee->fullname.' - '.$item->employee->department->name?? 'N/A' }}</td>
                              <td>Rp.{{ number_format(isset($isRevision) ? $item->itAsset->price : $item->price, 0) }}</td>
                              <td>
                                 <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    @if (isset($isRevision))
                                       <input type="hidden" name="itAsset[{{ $index }}][disposalItemId]" value="{{ encrypt($item->id) }}">
                                       <input type="hidden" name="itAsset[{{ $index }}][id]" value="{{ encrypt($item->itAsset->id) }}">
                                       <input type="text" name="itAsset[{{ $index }}][sale_price]" value="{{ number_format($item->sale_price) }}" class="form-control required sale-price-form" placeholder="0.00" required inputmode="numeric">
                                    @else
                                       <input type="hidden" name="itAsset[{{ $index }}][id]" value="{{ encrypt($item->id) }}">
                                       <input type="text" class="form-control sale-price-form" placeholder="0.00" required inputmode="numeric" name="itAsset[{{ $index }}][sale_price]">
                                    @endif
                                 </div>
                              </td>
                              <td>
                                 <textarea name="itAsset[{{ $index }}][reason]" class="form-control" cols="30" rows="3" maxlength="250" placeholder="Reason...">{{ isset($isRevision) ? $item->reason : '' }}</textarea>
                              </td>
                           </tr>
                        @empty
                           <tr>
                              <td colspan="8" class="text-center">No assets found</td>
                           </tr>
                        @endforelse

                        <tr>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td >Total: Rp. <span id="sale-price-total">{{ isset($isRevision)? number_format($assetDisposal->disposalItems->sum('sale_price'), 0) : 0 }}</span></td>
                           <td></td>
                        </tr>
                     </tbody>
                  </table>
               </div>

               <div class="col-12">
                  <hr class="mt-5">
                  <h5 class="text-center mb-4">Approval Information</h5>

                  @php
                     $options = [
                        '1st Approver',
                        '2nd Approver',
                        '3rd Approver',
                     ];

                     // $options = [
                     //    '1st Evaluator',
                     //    '2nd Evaluator',
                     //    '3rd Evaluator',
                     //    'HRD Approval',
                     //    'Director',
                     //    'President Director',
                     // ];
                     // $defaultApprovals = [
                     //    0 => '1st Evaluator',
                     //    1 => '2nd Evaluator',
                     //    2 => 'HRD Approval',
                     //    3 => 'Director',
                     //    4 => 'President Director',
                     // ];
                  @endphp

                  @isset($isRevision)
                     @foreach ($assetDisposal->approvalPaths as $i => $approvalPath)
                        <div class="row row-cols-4 approval-group approval-group-{{ $i }}">
                           <div class="col p-2">
                              <label class="required fw-semibold fs-6 mb-2">{{ $approvalPath->role_name }}</label>
                              {{-- <input disabled type="text"
                                 id="approval{{ $i }}_name"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approver->fullname?? '-' }}" /> --}}

                              <select id="approval{{ $i }}_name" name="approver[{{ $i }}][id]" class="select2 select-approver" required disabled>
                                 <option value="" selected>{{ $approvalPath->employee->fullname ?? '-' }}</option>
                              </select>
                           </div>

                           <div class="col p-2">
                              <label class="fw-semibold fs-6 mb-2">Position</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_position"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approvalPath->position ?? '-' }}" disabled />
                           </div>

                           <div class="col p-2">
                              <label class="fw-semibold fs-6 mb-2">Department</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_department"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approvalPath->department ?? '-' }}" disabled />
                           </div>
                           <div class="col p-2">
                              <label class="fw-semibold fs-6 mb-2">Email</label>
                              <input type="text"
                                 id="approval{{ $i }}_email"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approvalPath->email }}" disabled/>
                           </div>
                        </div>
                     @endforeach
                  @else
                     @foreach (range(1, 3) as $i)
                        <div class="row row-cols-4 approval-group approval-group-{{ $i }}">
                           <input type="hidden" name="approver[{{ $i }}][role]" id="approval{{ $i }}_id" value="{{ $options[$i - 1] }}">
                           <div class="col p-2">
                              <label class="required fw-semibold fs-6 mb-2">{{ $options[$i - 1] }}</label>
                              {{-- <input disabled type="text"
                                 id="approval{{ $i }}_name"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approver->fullname?? '-' }}" /> --}}

                              <select id="approval{{ $i }}_name" name="approver[{{ $i }}][id]" class="select2 select-approver" required>
                                 <option value="" selected>Select an approver</option>
                                 @foreach ($employees as $item)
                                    <option value="{{ encrypt($item->id) }}" data-position="{{ $item->position->nama ?? '-' }}" data-department="{{ $item->department->name ?? '-' }}" data-email="{{ $item->user->email ?? '-' }}">
                                       {{ $item->fullname }} - {{ $item->position->nama?? 'N/A' }}({{ $item->department->name?? 'N/A' }})
                                    </option>
                                 @endforeach
                              </select>
                           </div>

                           <div class="col p-2">
                              <label class="fw-semibold fs-6 mb-2">Position</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_position"
                                 class="form-control form-control-solid mb-3 mb-lg-0" />
                           </div>

                           <div class="col p-2">
                              <label class="fw-semibold fs-6 mb-2">Department</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_department"
                                 class="form-control form-control-solid mb-3 mb-lg-0"/>
                           </div>
                           <div class="col p-2">
                              <label class="fw-semibold fs-6 mb-2">Email</label>
                              <input type="text"
                                 id="approval{{ $i }}_email"
                                 name="approver[{{ $i }}][email]"
                                 class="form-control form-control-solid mb-3 mb-lg-0"/>
                           </div>
                        </div>

                        {{-- old version where its still uses line approval --}}
                        {{-- <div class="row approval-group approval-group-{{ $i }}">
                           <div class="col-lg-3 col-sm-6 p-2">
                              <input type="hidden" name="approver[{{ $i }}][id]" value="{{ encrypt($approver->id) }}">
                              <label class="required fw-semibold fs-6 mb-2">Line Approval
                                 {{ $i }}</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_name"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approver->fullname?? '-' }}" />
                           </div>

                           <div class="col-lg-3 col-sm-6 p-2">
                              <label class="fw-semibold fs-6 mb-2">Position</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_position"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approver->position->nama?? '-' }}" />
                           </div>

                           <div class="col-lg-3 col-sm-6 p-2">
                              <label class="fw-semibold fs-6 mb-2">Email</label>
                              <input disabled type="text"
                                 id="approval{{ $i }}_email"
                                 class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $approver->email }}" />
                           </div>

                           <div class="col-lg-3 col-sm-6 p-2">
                              <label class="required fw-semibold fs-6 mb-2">Sign {{ $i }}
                                 As</label>
                              <select id="approval{{ $i }}_as"
                                 name="approver[{{ $i }}][role]" class="select2" required>
                                 <option value="" disabled selected>Select an option</option>
                                 @foreach ($options as $value)
                                    @php
                                       $defaultValue = $defaultApprovals[$i] ?? null;
                                       $isSelected = $defaultValue == $value;
                                    @endphp
                                    <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                                       {{ $value }}
                                    </option>
                                 @endforeach
                              </select>
                           </div>
                        </div> --}}
                     @endforeach
                  @endisset
               </div>

               <div class="text-center">
                  <button type="submit" data-text="Import File" class="btn mb-0 btn-primary mt-4 btn-label" type="submit">
                     <i class="{{ isset($isRevision)? "ri-save-line" : "ri-file-reduce-line" }} label-icon align-middle fs-16 me-2"> </i> <span class="text">
                        {{ isset($isRevision)? "Submit Revision" : "Propose Disposal" }}
                     </span>
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>

@endsection

@section('script')
   <!-- Datatables -->
   <script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="/assets/js/pages/datatables.init.js"></script>
   <!-- Select2 -->
   <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
   <script>
      $('.select2').select2({
         theme: 'bootstrap-5',
      })

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
         $(".sale-price-form").on("input", function() {
            let value = $(this).val();
            value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
            if (value) {
               value = parseInt(value).toLocaleString('id-ID'); // Format as Indonesian Rupiah
            }
            $(this).val(value);
            $(this).attr('data-raw-value', value.replace(/[^0-9]/g, '')); // Store raw numeric value in data attribute
            $("#sale-price-total").text(calculateTotalSalePrice()); // Update total sale price
         });

         function calculateTotalSalePrice() {
            let total = 0;
            $(".sale-price-form").each(function() {
               const rawValue = $(this).attr('data-raw-value') || '0';
               total += parseInt(rawValue);
            });
            return total.toLocaleString('id-ID');
         }

         $('.select-approver').change(function() {
            const selectedOption = $(this).find('option:selected');
            const position = selectedOption.data('position') || '-';
            const department = selectedOption.data('department') || '-';
            const email = selectedOption.data('email') || '-';
            const index = $(this).attr('id').match(/\d+/)[0]; // Extract number from id

            // Set corresponding position and email fields
            $(`#approval${index}_position`).val(position);
            $(`#approval${index}_department`).val(department);
            $(`#approval${index}_email`).val(email);
         });

         $('#disposal-form').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            

            formData.entries().forEach(element => {
               const [key, value] = element;
               if (key.includes('[sale_price]')) {
                  const cleanNumber = value.replace(/[^0-9]/g, ''); 
                  formData.set(key, cleanNumber);
               }
            });

            // formData.entries().forEach(element => {
            //    console.log(element);
            //    return
            // });
            

            // Confirmation Dialog
            Swal.fire({
               title: `{{ 
                  !isset($isRevision)?
                     'Confirm Disposal?' :
                        'Confirm Revision?'
               }}`,
               text: `{{ 
                  !isset($isRevision)? 
                     "Are you sure you want to propose these assets for disposal? This will start the approval workflow." :
                        "You are requesting a revision. Please note that this will reset xthe entire approval workflow, and all previous approvals will need to be resubmitted. Do you wish to proceed?"
               }}`,
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, Submit!',
               showLoaderOnConfirm: true,
               preConfirm: () => {
                  // Show Loading State
                  Swal.fire({
                     title: 'Processing...',
                     text: 'Please wait while we register your request.',
                     allowOutsideClick: false,
                     allowEscapeKey: false,
                     showConfirmButton: false,
                     didOpen: () => {
                        Swal.showLoading();
                     }
                  });

                  // Execute AJAX
                  return $.ajax({
                     url: "{{ route('asset-disposal.store') }}", // Update with your actual route
                     method: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                  })
                  .then(response => {
                     return response; // Pass data to next .then()
                  })
                  .catch(error => {
                     Swal.hideLoading()
                     // Provide a specific error message if available
                     let errorMsg = error.responseJSON?.message || "Something went wrong with the request.";
                     // Swal.showValidationMessage(`Request failed: ${errorMsg}`);
                     Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: errorMsg,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                     })
                  });
               },
            }).then((result) => {
               // Handle Success
               console.log(result);
               
               if (result.isConfirmed && result.value.status == 'success') {
                  Swal.fire({
                     icon: 'success',
                     title: 'Submitted!',
                     text: result.value.message || 'Your disposal request has been sent for approval.',
                     timer: 2000,
                     showConfirmButton: false
                  }).then(() => {
                     // Redirect or Reset
                     window.location.href = "{{ $redirectLink }}"; 
                  });
               }
            });
         });
      });
   </script>
@endsection