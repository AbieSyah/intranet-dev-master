<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Purchase Review</title>

   <link href="{{  url('') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
   <div class="container py-5">
      <div class="row justify-content-center">
         <div class="col-lg-9">
               
               <div class="d-flex justify-content-between mb-5 px-5">
                  <div class="text-center">
                     <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-2" style="width:40px; height:40px;">1</div>
                     <small class="fw-bold">Review</small>
                  </div>
                  <div class="flex-grow-1 border-top mt-3 mx-2 border-2"></div>
                  <div class="text-center text-muted">
                     <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-2" style="width:40px; height:40px;">2</div>
                     <small>Confirmation</small>
                  </div>
               </div>

               <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                  <div class="card-body p-0">
                     <div class="p-4 bg-dark text-white d-flex justify-content-between align-items-center">
                        <div>
                           <p class="text-uppercase mb-0 opacity-75 small fw-bold">Transaction Reference</p>
                           <h3 class="mb-0">#{{ $transaction->transaction_number }}</h3>
                        </div>
                        <div class="text-end">
                           <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending Signature</span>
                        </div>
                     </div>

                     <div class="p-4">
                           <h5 class="mb-4 fw-bold"><i class="ri-list-check-2 me-2"></i>Items for Disposal</h5>
                           <div class="table-responsive">
                              <table class="table table-hover align-middle">
                                 <thead class="table-light">
                                       <tr>
                                          <th class="border-0 rounded-start">Asset Detail</th>
                                          {{-- <th class="border-0">Serial Number</th> --}}
                                          <th class="border-0 text-end rounded-end">Sale Price</th>
                                       </tr>
                                 </thead>
                                 <tbody>
                                       @foreach($transaction->disposalItems as $item)
                                       <tr>
                                          <td>
                                             <div class="fw-bold">{{ $item->itAsset->brand }}</div>
                                             {{-- <small class="text-muted">{{ $item->itAsset->category }}</small> --}}
                                          </td>
                                          {{-- <td><code>{{ $item->itAsset->serial_number }}</code></td> --}}
                                          <td class="text-end fw-bold text-dark">Rp {{ number_format($item->sale_price, 0, ',', '.') }}</td>
                                       </tr>
                                       @endforeach
                                 </tbody>
                                 <tfoot>
                                       <tr class="table-active">
                                          <td class="text-end fw-bold py-3">Grand Total:</td>
                                       <td class="text-end fw-bold py-3 fs-5 text-success">Rp {{ number_format($transaction->disposalItems->sum('sale_price'), 0, ',', '.') }}</td>
                                       </tr>
                                 </tfoot>
                              </table>
                           </div>

                           <div class="mt-5 p-4 rounded-4" style="background: #f8f9fa; border: 1px dashed #dee2e6;">
                              <form action="{{ URL::signedRoute('disposal.public-confirm', ['id' => encrypt($transaction->id)]) }}" method="POST">
                                 @csrf
                                 <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="agreement" id="checkAgree" required style="width: 1.5em; height: 1.5em; border: 1px solid gray">
                                    <label class="form-check-label ms-2 pt-1" for="checkAgree">
                                       I confirm that I have inspected the assets listed above and agree to the purchase conditions. This electronic signature carries the same legal weight as a handwritten signature.
                                    </label>
                                 </div>
                                 <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-sm fw-bold">
                                       Confirm and Electronically Sign
                                 </button>
                              </form>
                           </div>
                     </div>
                  </div>
               </div>
         </div>
      </div>
   </div>

   <script src="{{  url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>