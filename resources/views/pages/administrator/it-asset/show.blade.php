@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

   <style>
      /* Basic Vertical Timeline CSS */
      .vertical-timeline { position: relative; border-left: 2px solid #e9ecef; margin-left: 20px; }
      .timeline-item { position: relative; padding-left: 30px; }
      .timeline-icon { 
         position: absolute; left: -16px; top: 0; width: 30px; height: 30px; 
         border-radius: 50%; display: flex; align-items: center; justify-content: center; 
      }
   </style>
@endsection

@section('content')
   <div class="col">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0">History IT Asset</h4>

         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
               <li class="breadcrumb-item active">History</li>
            </ol>
         </div>
      </div>
      
      <div class="container-fluid">
         <div class="card">
            <div class="card-header d-flex justify-content-between">
               <div>
                  <table>
                     <tr>
                        <td>
                           <h5>Asset Code</h5>
                        </td>
                        <td class="ps-3">
                           <h5>: {{ $asset->asset_code }}</h5>
                        </td>
                     </tr>
                     <tr>
                        <td>Asset Brand</td>
                        <td class="ps-3">: {{ $asset->brand }}</td>
                     </tr>
                     <tr>
                        <td>Type</td>
                        <td class="ps-3">: {{ $asset->assetType?->name ?? '-' }}</td>
                     </tr>
                     <tr>
                        <td>Current Owner</td>
                        <td class="ps-3">: {{ $asset->employee?->fullname ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="d-flex flex-column align-items-between justify-content-between gap-2">
                  {{-- <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a> --}}
                  <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>

                  <span class="badge bg-secondary fs-6 text-capitalize">{{ $asset->status }}</span>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-hover align-middle">
                     <thead class="table-light">
                        <tr>
                           <th>Date</th>
                           <th style="white-space: nowrap">Ticket Number</th>
                           <th>Type</th>
                           <th>Description</th>
                           <th>Details</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($histories as $history)
                           <tr>
                              <td>{{ $history['created_at']->format('d M Y H:i') }}</td>
                              <td style="white-space: nowrap">
                                 @isset($history['ticket_number'])
                                    {{ $history['ticket_number'] }}
                                 @else
                                    <span class="text-muted">N/A</span>
                                 @endisset
                              </td>
                              <td>{{ $history['action_type'] }}</td>
                              <td>{{ $history['description'] }}</td>
                              <td>
                                 @if(isset($history['url']))
                                    <a href="{{ $history['url'] }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                       <i class="ri-eye-line"></i>
                                    </a>
                                 @else
                                    <span class="text-muted">No details available</span>
                                 @endif
                              </td>
                           </tr>
                           {{-- <tr>
                              <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                              <td>
                                 @if($log->reference_type === \App\Models\Movement::class)
                                       <span class="badge badge-soft-info">Asset Movement</span>
                                 @elseif($log->reference_type === \App\Models\AssetDisposal::class)
                                       <span class="badge badge-soft-danger">Asset Disposal</span>
                                 @elseif($log->reference_type === \App\Models\Maintenance::class)
                                       <span class="badge badge-soft-warning">Maintenance/Ticket</span>
                                 @else
                                       <span class="badge badge-soft-secondary">General Log</span>
                                 @endif
                              </td>
                              <td>{{ $log->description }}</td>
                              <td>
                                 @if($log->reference)
                                    @if($log->reference_type === \App\Models\Movement::class)
                                       <small class="text-muted">To: {{ $log->reference->to_value }}</small>
                                    @elseif($log->reference_type === \App\Models\AssetDisposal::class)
                                       <a href="{{ route('asset-disposal.show', ['id' => encrypt($log->reference->id)]) }}" class="btn btn-success btn-sm" title="Asset Disposal">
                                          <i class="ri-eye-line"></i>
                                       </a>
                                    @elseif($log->reference_type === \App\Models\Maintenance::class)
                                       <small class="text-muted">Ticket: #{{ $log->reference->ticket_no }}</small>
                                    @endif
                                 @else
                                       <span class="text-danger small">Reference Data Missing</span>
                                 @endif
                              </td>
                           </tr> --}}
                        @empty
                           <tr>
                              <td colspan="5" class="text-center">No history recorded for this asset.</td>
                           </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
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
   
@endsection