@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<!-- <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" /> -->
<link href="/assets/css/bootstrap-duallistbox.css" rel="stylesheet" type="text/css" />
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- plugin -->
<!-- <link rel="stylesheet" type="text/css" href="https://www.virtuosoft.eu/code/bootstrap-duallistbox/bootstrap-duallistbox/v3.0.2/bootstrap-duallistbox.css"> -->
<style>
    .moveall,
.removeall, .move, .remove {
  border: 1px solid #ccc !important;
  
  &:hover {
    background: #efefef;
  }
}

/* Only included because button labels aren't showing  */

.moveall::after {
  content: attr(title);
  
}

.removeall::after {
  content: attr(title);
}

.form-control option {
    padding: 10px;
    border-bottom: 1px solid #efefef;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Reguler</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Medical Check Up</a></li>
                    <li class="breadcrumb-item active">Reguler</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="form" action="{{route('reguler.store')}}" method="POST">
                @csrf
                <div class="card-header align-items-center">
                    <div class="row">
                        <div class="col-lg-12">                            
                            <div class="mb-3 float-end">
                                <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">                            
                            <div class="mb-3">
                                <label>Tanggal Periksa</label>
                                <div class="input-group">
                                    <input type="text" name="date_range" id="date_range"
                                        class="form-control @error('date_range') is-invalid @enderror"
                                        placeholder="Pilih Tanggal" value="{{ old('date_range') }}" required>
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="mb-3">
                                <label class="form-label">Laboratorium</label>
                                <select class="form-control js-example-basic-multiple @error('vendor') is-invalid @enderror" name="vendor"
                                    id="vendor" data-placeholder="--Pilih Laboratorium--" required>
                                    <option selected="true" disabled="true"></option>
                                    @if(Request::old('vendor') != NULL)
                                        <option value="{{Request::old('vendor')}}" selected>{{$vendors->where('id', intval(Request::old('vendor')))->first()->nama}}</option>
                                    @else
                                        @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div><!--end col-->
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mt-3">
                                <h5>Manage employee</h5>
                                <p class="text-muted">Generate employee.</p>
                            </div>
                            <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#modalemployee" class="btn btn-soft-primary waves-effect waves-light">Add employee</button> -->
                            <button type="submit" id="btn-generate" name="action" value="generate" class="btn btn-soft-primary waves-effect waves-light">Generate</button>
                        </div><!--end col-->
                    </div>
                </div><!-- end card header -->
                <div class="card-body">
                    <?php $employees = Session::get('employees'); ?>
                    <select multiple="multiple" name="employee[]" title="employee" class="demo2">
                        @if(isset($employees))
                            @foreach($employees as $emp)
                                @if(!empty($emp))
                                    <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->area->kode}} -- {{$emp->fullname}}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <br>
                    <br>
                    <?php $employees2 = Session::get('employees2'); ?>
                    <select multiple="multiple" name="employee2[]" title="employee2" class="demo2">
                        @if(isset($employees2))
                        @foreach($employees2 as $emp2)
                            <option value="{{$emp2->id}}">{{$emp2->nik}} -- {{$emp2->area->kode}} -- {{$emp2->fullname}}</option>
                        @endforeach
                        @endif
                    </select>
                    <br>
                    <br>
                    <button type="submit" id="btn-save" name="action" value="save"class="btn btn-primary float-end">Save</button>
                    <br>
                    <br>
                </div>
                <!-- Employee Modal -->
                {{--<div class="modal zoomIn" id="modalemployee" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-right modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fs-14">Select employee</h5>
                                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="col-lg-12">
                                    <div class="mt-4 mt-lg-0">
                                        <!-- <form id="demoform"> -->
                                            <select multiple="multiple" name="employee[]" title="employee" class="demo2">
                                                @foreach($employees as $emp)
                                                    <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->fullname}}</option>
                                                @endforeach
                                            </select>
                                            
                                            <!-- <br>
                                            <div class="row">
                                                <div class="col-md-6 offset-md-6">
                                                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                                                </div>
                                            </div> -->
                                        <!-- </form> -->
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button> -->
                                <button type="submit" id="btn-save" class="btn btn-primary ">Save</button>
                            </div>
                        </div>
                    </div>
                </div>--}}
            </form>
        </div>
    </div>
</div>
<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="/assets/images/loading.gif" style="width:120px;height:120px">                    
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Datatables -->
<!-- <script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script> -->
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="/assets/js/jquery.bootstrap-duallistbox.js"></script>
@endsection

@section('javascript')
<script>
    $('#date_range').flatpickr({
        mode: "range",
        allowInput: true,
        altInput: true,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
</script>
<script>
    var demo2 = $('.demo2').bootstrapDualListbox({
        nonSelectedListLabel: 'Available Employees',
        selectedListLabel: 'Selected Employees',
        // preserveSelectionOnMove: 'moved',
        moveOnSelect: false,
        moveAllLabel: '',
        removeAllLabel: ''
    });
</script>
<script>
    $( "#btn-save" ).click(function() {
        $("#form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });

    $(function () {
        $('#vendor').select2();
        $('#employee').select2();
    });
</script>
<script>
    @if(Session::has('success'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.success("{{ session('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection