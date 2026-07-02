@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="/assets/css/bootstrap-duallistbox.css" rel="stylesheet" type="text/css" />
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
.moveall, .removeall, .move, .remove {
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
            <h4 class="mb-sm-0">Create Account</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Multiple</a></li>
                    <li class="breadcrumb-item active">User</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="form" action="{{route('user.multiple.store')}}" method="POST">
                @csrf
                <div class="card-header align-items-center">
                    <div class="row">
                        <div class="col-lg-12">                            
                            <div class="mb-3 float-end">
                                <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
                <div class="card-body">
                    <select multiple="multiple" name="employee[]" title="employee" class="demo2">
                        @foreach($employees as $emp)
                            <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->area->kode}} -- {{$emp->Department->name}} -- {{$emp->fullname}}</option>
                        @endforeach
                    </select>
                    <br>
                    <br>
                    <button type="submit" id="btn-save" class="btn btn-primary float-end">Submit</button>
                    <br>
                    <br>
                </div>
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="/assets/js/jquery.bootstrap-duallistbox.js"></script>
@endsection

@section('javascript')
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