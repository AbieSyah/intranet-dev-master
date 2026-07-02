@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
    div.dataTables_wrapper {
      width: 100%;
      /* margin: 0 auto; */
    }
    .white-space-pre-line {
        white-space: pre-line;
    }
</style>
<style>         
    /* We are stopping user from 
    printing our webpage */
    @media print {

        html,
        body {

            /* Hide the whole page */
            display: none;
        }
    }
</style>
<!-- costume css -->
<link rel="stylesheet" type="text/css" href="{{asset('assets/flip/css/flipbook.style.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/flip/css/font-awesome.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Internal Rules</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Internal</a></li>
                    <li class="breadcrumb-item active">Rule</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                @can('hrd.internal-rules.read')
                <li class="nav-item">
                    <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab" href="#pill-rule" role="tab">
                        <i class="ri-file-user-line me-1 align-bottom"></i> Internal Rule
                    </a>
                </li>
                @endcan
                @can('hrd.benefit.read')
                <li class="nav-item">
                    <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-benefit" role="tab">
                        <i class="ri-hand-coin-line me-1 align-bottom"></i> Benefit
                    </a>
                </li>
                @endcan   
                @can('hrd.pkb.read')                
                <li class="nav-item">
                    <a class="nav-link py-3" id="tab-pkb" data-bs-toggle="tab" href="#pill-pkb" role="tab">
                        <i class="ri-book-open-line me-1 align-bottom"></i> e-PKB
                    </a>
                </li>                    
                @endcan   
            </ul>
            <div class="tab-content">
                @can('hrd.internal-rules.read')
                <div class="tab-pane active" id="pill-rule" role="tabpanel">
                    <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                        @can('hrd.internal-rules.create')
                        <button type="button" id="add-rule" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Rule">
                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Rule
                        </button>
                        @endcan
                    </div><!-- end card header -->
                    <div class="card-body">            
                        <table class="table table-striped bordered" id="table_rule">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">NO</th>
                                <th scope="col" style="text-align:center">RULE</th>
                                <th scope="col" style="text-align:center">TGL. BERLAKU</th>
                                <th scope="col" style="text-align:center">ISI</th>
                                <!-- <th scope="col" style="text-align:center">STATUS</th> -->
                                <th scope="col" style="text-align:center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endcan
                @can('hrd.benefit.read')
                <div class="tab-pane" id="pill-benefit" role="tabpanel">
                    <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                        @can('hrd.benefit.create')
                        <button type="button" id="add-benefit" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalBenefit" data-text="Add New Benefit">
                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Benefit
                        </button>
                        @endcan
                    </div><!-- end card header -->
                    <div class="card-body">
                        <table class="table table-striped bordered" id="table_benefit">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">NO</th>
                                <th scope="col" style="text-align:center">BENEFIT</th>
                                <th scope="col" style="text-align:center">RULE</th>
                                <!-- <th scope="col" style="text-align:center">VALUE</th> -->
                                <!-- <th scope="col" style="text-align:center">VALUE TEXTUAL</th> -->
                                <th scope="col" style="text-align:center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endcan
                @can('hrd.pkb.read')
                <div class="tab-pane" id="pill-pkb" role="tabpanel">
                    <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                        @can('hrd.pkb.create')
                        <button type="button" id="add-pkb" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalPKB" data-text="Add New PKB">
                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New PKB
                        </button>
                        @endcan
                    </div><!-- end card header -->
                    <div class="card-body">            
                        <table class="table table-striped bordered" id="table_pkb">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">NO</th>
                                <th scope="col" style="text-align:center">NAMA DOCUMENT</th>
                                <th scope="col" style="text-align:center">PERIODE</th>
                                <th scope="col" style="text-align:center">ISI</th>
                                <th scope="col" style="text-align:center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endcan                
            </div>
        </div>
    </div>
</div>
<!--Modal add/edit-->
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Add/Update Rule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="Form" action="{{ route('internal-rule.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- @method('post') -->
          <div class="row g-3">
            <input type="hidden" id="id" name="id" value="">
            <div class="col-lg-12">
            <div>
                <label for="nama" class="form-label">Nama Rule</label>
                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan Nama Document" value="" required>
            </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <div>
                    <label for="tgl_berlaku" class="form-label">Berlaku Mulai</label>
                    <div class="input-group">
                        <input type="text" name="tgl_berlaku" id="tgl_berlaku"
                            class="form-control @error('tgl_berlaku') is-invalid @enderror"
                            placeholder="Pilih Tanggal" value="" required>
                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                    </div>
                </div>
            </div><!--end col-->    
            <div class="col-lg-12">
                <div>
                    <label for="isi" class="form-label">Isi</label>
                    <textarea class="form-control" name="isi" id="isi" rows="3"></textarea>
                </div>
            </div><!--end col-->
            <div id="btn-preview">                            
                <div class="col-lg-12">
                    <div>
                        <label for="lihat_document" class="form-label">Lihat Document</label>
                        <br>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal-preview" class="btn btn-danger waves-effect waves-light"><i class="ri-file-pdf-line align-bottom"></i> PDF</button>
                    </div>
                </div><!--end col-->
            </div>
            <div class="col-lg-12">
                <div>
                    <label class="form-label">Upload</label>
                    <div class="input-group">
                        <input onchange="uploadValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file" id="file" accept="application/pdf,application/PDF" required>
                        <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearUpload()">Remove</button>
                    </div>
                    <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal revisi-->
<div class="modal fade" id="modal-revisi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Revisi Rule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="Form-revisi" action="{{ route('internal-rule.revisi') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- @method('post') -->
          <div class="row g-3">
            <input type="hidden" id="id_revisi" name="id_revisi" value="">
            <div class="col-lg-12">
            <div>
                <label for="nama" class="form-label">Nama Rule</label>
                <input type="text" class="form-control" name="nama_revisi" id="nama_revisi" placeholder="Masukkan Nama Rule" value="" required>
            </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <div>
                    <label for="tgl_berlaku" class="form-label">Berlaku Mulai</label>
                    <div class="input-group">
                        <input type="text" name="tgl_berlaku_revisi" id="tgl_berlaku_revisi"
                            class="form-control @error('tgl_berlaku_revisi') is-invalid @enderror"
                            placeholder="Pilih Tanggal" value="" required>
                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                    </div>
                </div>
            </div><!--end col-->    
            <div class="col-lg-12">
                <div>
                    <label for="isi" class="form-label">Isi</label>
                    <textarea class="form-control" name="isi_revisi" id="isi_revisi" rows="3"></textarea>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div>
                    <label for="old_rule" class="form-label">Document Sebelumnya</label>
                    <br>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-preview-revisi" class="btn btn-danger waves-effect waves-light"><i class="ri-file-pdf-line align-bottom"></i> PDF</button>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div>
                    <label class="form-label">Upload</label>
                    <div class="input-group">
                        <input onchange="uploadrevisiValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file_revisi" id="file_revisi" accept="application/pdf,application/PDF" required>
                        <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearrevisiUpload()">Remove</button>                        
                    </div>
                    <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-save-revisi" class="btn btn-primary">Revisi</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal Benefit add/edit-->
<div class="modal fade" id="modalBenefit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Add Benefit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="Form-benefit" action="{{ route('benefit.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('put')
          <div class="row g-3">
            <div class="col-lg-12">
                <label for="benefit" class="form-label">Benefit</label>
                <input type="text" class="form-control" name="benefit" id="benefit" placeholder="Masukkan Nama Benefit" value="" required>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <label for="id_rule" class="form-label">Rule</label>
                <select class="form-control" id="id_rule" name="id_rule" data-placeholder="Pilih Rule" required>
                    <option selected="true" disabled="true"></option>
                    <option value="none">None</option>
                    @foreach($query as $rule)
                    <option value="{{$rule->id}}">{{$rule->nama}}</option>
                    @endforeach
                </select>
            </div><!--end col-->    
            {{--<div class="col-lg-12">
                <label for="id_area" class="form-label">Area</label>
                <select class="form-control" id="id_area" name="id_area[]" multiple="multiple" data-placeholder="Pilih Area" required>
                    <option value="all">All</option>
                    @foreach($areas as $area)
                    <option value="{{$area->id}}">{{$area->name}}</option>
                    @endforeach
                </select>
            </div><!--end col-->--}}
            {{--<div class="col-lg-12">
                <label for="nama" class="form-label">Level</label>
                <select class="form-control" id="id_level" name="id_level[]" multiple="multiple" data-placeholder="Pilih Level" required>
                    <option value="all">All</option>
                    @foreach($levels as $level)
                    <option value="{{$level->id}}">{{$level->nama}}</option>
                    @endforeach
                </select>
            </div><!--end col-->
            <div class="col-lg-12">
                <label for="value_nominal" class="form-label">Value Nominal</label>
                <input type="number" class="form-control" name="value_nominal" id="value_nominal" placeholder="Masukkan Value Nominal" value="">
            </div><!--end col-->
            <div class="col-lg-12">
                <label for="value_textual" class="form-label">Value Textual</label>
                <input type="text" class="form-control" name="value_textual" id="value_textual" placeholder="Masukkan Value Textual" value="">
            </div><!--end col-->--}}
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal Benefit settings-->
<div class="modal fade" id="modal-setting-benefit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="judul-benefit"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="Form-setting-benefit" action="{{ route('internal-rule.setting.benefit') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-lg-12">
                <label for="benefit" class="form-label">Benefit</label>
                <input type="text" class="form-control" name="benefit" id="benefit2" placeholder="Masukkan Nama Benefit" value="" style="Background-color: #eff2f7;" readonly required>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <label for="id_rule" class="form-label">Rule</label>
                <select class="form-control" id="id_rule2" name="id_rule" data-placeholder="Pilih Rule" required>
                    <option value="none">None</option>
                    @foreach($query as $rule)
                    <option value="{{$rule->id}}">{{$rule->nama}}</option>
                    @endforeach
                </select>
            </div><!--end col-->    
            {{--<div class="col-lg-12">
                <label for="id_area" class="form-label">Area</label>
                <select class="form-control" id="id_area2" name="id_area" data-placeholder="Pilih Area" required>
                    <!-- <option value="all">All</option> -->
                    @foreach($areas as $area)
                    <option value="{{$area->id}}">{{$area->name}}</option>
                    @endforeach
                </select>
            </div><!--end col-->--}}
            <div class="card-body p-4 border-top border-top-dashed">
                <input type="hidden" id="id_set" name="id_set" value="">                
                <!-- <div class="table-responsive">
                </div> -->
                <!--end table-->
                <table class="table table-borderless" style="table-layout: fixed; width: 100%">
                    <thead class="align-middle">
                        <tr class="table-active">
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 21%;">
                                Level Rules
                            </th>
                            <th scope="col" style="width: 21%;">
                                Employee
                            </th>
                            <th scope="col" style="width: 24%">
                                Areas
                            </th>
                            <th scope="col" style="width: 40%">Value</th>
                            <!-- <th scope="col" style="width: 20%">Value Textual</th> -->
                            <th scope="col" style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody id="newlink">           
                                    
                    </tbody>
                    <tbody>
                        <tr id="newForm" style="display: none;">
                            <td class="d-none" colspan="5">
                                <p>Add New Form</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <a href="javascript:new_link()" id="add-item"
                                    class="btn btn-soft-success"><i
                                        class="ri-add-fill me-1 align-bottom"></i> Add Rule</a>
                            </td>
                        </tr>
                        <tr class="border-top border-top-dashed mt-2">
                            <td colspan="3"></td>
                            <td colspan="2" class="p-0"></td>
                        </tr>
                    </tbody>
                </table>
                {{--<div class="hstack gap-2 d-print-none" style="justify-content: flex-end;">
                    <button type="submit" id="btn-save-setting" class="btn btn-primary">Submit</button>
                </div>--}}
            </div>
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Modal Delete Calendar -->
<form id="Form-delete-benefit" action="{{ route('benefit.destroy') }}" method="POST">
    @csrf
    @method('put')
    <!-- Modal konfirmasi delete -->
    <div id="modal-delete-benefit" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="delete-modal"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" name="id_delete_benefit" id="id_delete_benefit">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal konfirmasi delete -->
</form>
<!--Modal PKB add/edit-->
<div class="modal fade" id="modalPKB" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Add/Update PKB</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="Form-pkb" action="{{ route('pkb.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- @method('post') -->
          <div class="row g-3">
            <input type="hidden" id="id_pkb" name="id_pkb" value="">
            <div class="col-lg-12">
            <div>
                <label for="nama_pkb" class="form-label">Nama Document</label>
                <input type="text" class="form-control" name="nama_pkb" id="nama_pkb" placeholder="Masukkan Nama Document" value="" required>
            </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <div>
                    <label for="periode_pkb" class="form-label">Periode</label>
                    <div class="input-group">
                        <input type="text" name="periode_pkb" id="periode_pkb"
                            class="form-control @error('periode_pkb') is-invalid @enderror"
                            placeholder="Pilih Tanggal" value="" required>
                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                    </div>
                </div>
            </div><!--end col-->    
            <div class="col-lg-12">
                <div>
                    <label for="isi_pkb" class="form-label">Isi</label>
                    <textarea class="form-control" name="isi_pkb" id="isi_pkb" rows="3"></textarea>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div>
                    <label class="form-label">Upload</label>
                    <div class="input-group">
                        <input onchange="uploadPkbValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file_pkb" id="file_pkb" accept="application/pdf,application/PDF" required>
                        <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearPkbUpload()">Remove</button>
                    </div>
                    <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal Mode Baca PKB-->
<div class="modal fade" id="modalbacaPKB" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Read PKB</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <div id="show-preview-pkb">
            </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal Validation Extension File Upload -->
<div class="modal fade" id="validationmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <lord-icon
                    src="https://cdn.lordicon.com/tdrtiskw.json"
                    trigger="loop"
                    colors="primary:#f7b84b,secondary:#405189"
                    style="width:130px;height:130px">
                </lord-icon>
                <div class="mt-0 pt-4">
                    <h4>Whoops, ada yang salah!</h4>
                    <div id="info-validation"></div>
                    <!-- Toogle to second dialog -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal preview rules-->
<div class="modal flip" id="modal-preview" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-target="#modal" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="show-preview">
                </div>  
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!--modal preview revisi rules-->
<div class="modal flip" id="modal-preview-revisi" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-target="#modal-revisi" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="show-preview-revisi">
                </div>  
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!--modal preview pdf rules-->
<div class="modal flip" id="modal-preview-pdf" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="show-preview-pdf">
                </div>  
            </div>
            <div class="modal-footer">
            </div>
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
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<!-- AJAX -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<!-- Include JS -->
<script src="{{asset('assets/flip/js/flipbook.min.js')}}"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
@if(Session::has('pkb'))
<script>
    $('#tab-rule').removeClass('active');
    $('#pill-rule').removeClass('active');
    $('#tab-benefit').removeClass('active');
    $('#pill-benefit').removeClass('active');
    $('#tab-pkb').addClass('active');
    $('#pill-pkb').addClass('active');
</script>
@endif
<script>
    //submit form production
    $("#Form-benefit").submit(function(e) {
        e.preventDefault();

        swalert = Swal.fire({
        title: 'Loading!',
        didOpen: () => {
            Swal.showLoading()
        }
        });

        const formData = new FormData(this);

        $.ajax({
        url: $(this).attr("action"),
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            swalert.hideLoading();
            swalert.update({
            title: "Success",
            text: response.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                popup: 'swal2-noanimation',
                confirmButton: "btn btn-primary"
            }
            });
            // swalert.then(() => window.location.reload() = response.redirect)
            swalert.then(() => $('#table_benefit').DataTable().ajax.reload())
            $("#modalBenefit").modal("hide");
        },
        error: function(xhr, status, error) {
            console.log({
            xhr,
            status,
            error
            });
            handleErrorResponse(xhr.responseJSON);
        }
        });
    });

    function handleErrorResponse(responseJson) {
    let errorMessage = '';

    if (responseJson.message) {
        errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
    }

    if (responseJson.errors) {
        for (const fieldName in responseJson.errors) {
        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
        }
    }

    if (responseJson.responseText) {
        errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;

    }

    if (errorMessage === '') {
        errorMessage += '<p class="text-danger">An error occurred.</p>';
    }

    // Display error message using SweetAlert
    swalert.update({
        title: 'Error',
        html: errorMessage,
        icon: 'error',
        buttonsStyling: false,
        confirmButtonText: 'Ok',
        customClass: {
        confirmButton: 'btn btn-primary'
        }
    });
    }
</script>
<script>
    //submit form production
    $("#Form-setting-benefit").submit(function(e) {
        e.preventDefault();

        swalert = Swal.fire({
        title: 'Loading!',
        didOpen: () => {
            Swal.showLoading()
        }
        });

        const formData = new FormData(this);

        $.ajax({
        url: $(this).attr("action"),
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            swalert.hideLoading();
            swalert.update({
                title: "Success",
                text: response.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    popup: 'swal2-noanimation',
                    confirmButton: "btn btn-primary"
                }
            });
            // swalert.then(() => window.location.reload() = response.redirect)
            swalert.then(() => $('#table_benefit').DataTable().ajax.reload())
            $("#modal-setting-benefit").modal("hide");            
        },
        error: function(xhr, status, error) {
            console.log({
            xhr,
            status,
            error
            });
            handleErrorResponse(xhr.responseJSON);
        }
        });
    });

    function handleErrorResponse(responseJson) {
    let errorMessage = '';

    if (responseJson.message) {
        errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
    }

    if (responseJson.errors) {
        for (const fieldName in responseJson.errors) {
        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
        }
    }

    if (responseJson.responseText) {
        errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;

    }

    if (errorMessage === '') {
        errorMessage += '<p class="text-danger">An error occurred.</p>';
    }

    // Display error message using SweetAlert
    swalert.update({
        title: 'Error',
        html: errorMessage,
        icon: 'error',
        buttonsStyling: false,
        confirmButtonText: 'Ok',
        customClass: {
        confirmButton: 'btn btn-primary'
        }
    });
    }
</script>
<script>
    //submit form delete benefit
    $("#Form-delete-benefit").submit(function(e) {
        e.preventDefault();

        swalert = Swal.fire({
        title: 'Loading!',
        didOpen: () => {
            Swal.showLoading()
        }
        });

        const formData = new FormData(this);

        $.ajax({
        url: $(this).attr("action"),
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            swalert.hideLoading();
            swalert.update({
                title: "Success",
                text: response.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    popup: 'swal2-noanimation',
                    confirmButton: "btn btn-primary"
                }
            });
            // swalert.then(() => window.location.reload() = response.redirect)
            swalert.then(() => $('#table_benefit').DataTable().ajax.reload())
            $("#modal-delete-benefit").modal("hide");
        },
        error: function(xhr, status, error) {
            console.log({
            xhr,
            status,
            error
            });
            handleErrorResponse(xhr.responseJSON);
        }
        });
    });

    function handleErrorResponse(responseJson) {
    let errorMessage = '';

    if (responseJson.message) {
        errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
    }

    if (responseJson.errors) {
        for (const fieldName in responseJson.errors) {
        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
        }
    }

    if (responseJson.responseText) {
        errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;

    }

    if (errorMessage === '') {
        errorMessage += '<p class="text-danger">An error occurred.</p>';
    }

    // Display error message using SweetAlert
    swalert.update({
        title: 'Error',
        html: errorMessage,
        icon: 'error',
        buttonsStyling: false,
        confirmButtonText: 'Ok',
        customClass: {
        confirmButton: 'btn btn-primary'
        }
    });
    }
</script>
<script>
    document.addEventListener("keyup", function (e) {        
        var keyCode = e.keyCode ? e.keyCode : e.which;
        if (keyCode == 44) {
            setTimeout(function(){
                $('#modal-preview-pdf').modal('hide');
            }, 1);
            stopPrntScr();
            // alert("Do not take screenshot of this page");
        }
    });
    function stopPrntScr() {

        var inpFld = document.createElement("input");
        inpFld.setAttribute("value", ".");
        inpFld.setAttribute("width", "0");
        inpFld.style.height = "0px";
        inpFld.style.width = "0px";
        inpFld.style.border = "0px";
        document.body.appendChild(inpFld);
        inpFld.select();
        document.execCommand("copy");
        inpFld.remove(inpFld);
    }
    function AccessClipboardData() {
        try {
            window.clipboardData.setData('text', "Access   Restricted");
        } catch (err) {
        }
    }
    setInterval("AccessClipboardData()", 300);
</script>
<script>
    $('#modal-preview-pdf').on('shown.bs.modal', function (e) {
        document.addEventListener("keyup", function (e) {        
        var keyCode = e.keyCode ? e.keyCode : e.which;
        if (keyCode == 44) {
            setTimeout(function(){
                $('#modal-preview-pdf').modal('hide');
            }, 1);
            stopPrntScr();
            // alert("Do not take screenshot of this page");
        }
    });
    function stopPrntScr() {

        var inpFld = document.createElement("input");
        inpFld.setAttribute("value", ".");
        inpFld.setAttribute("width", "0");
        inpFld.style.height = "0px";
        inpFld.style.width = "0px";
        inpFld.style.border = "0px";
        document.body.appendChild(inpFld);
        inpFld.select();
        document.execCommand("copy");
        inpFld.remove(inpFld);
    }
    function AccessClipboardData() {
        try {
            window.clipboardData.setData('text', "Access   Restricted");
        } catch (err) {
        }
    }
    setInterval("AccessClipboardData()", 300);
    });
</script>
<script>
    $('#periode_pkb').flatpickr({
        mode: "range",
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
    $('#tgl_berlaku').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
    $('#tgl_berlaku_revisi').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
</script>
<script>
    $(function () {
        $('#id_rule').select2({dropdownParent: $('#modalBenefit .modal-content')});
        $('#id_area').select2({dropdownParent: $('#modalBenefit .modal-content')});
        $('#id_level').select2({dropdownParent: $('#modalBenefit .modal-content')});
        // $('#id_dept').select2({dropdownParent: $('#modal-setting .modal-content')});
        // $('#id_level').select2({dropdownParent: $('#modal-setting .modal-content')});
    });
    $( "#btn-save" ).click(function() {
        $("#Form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
        $("#Form-pkb").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
    $( "#btn-save-setting" ).click(function() {
        $("#Form-setting").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
    $( "#btn-save-revisi" ).click(function() {
        $("#Form-revisi").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
    $( "#add-rule" ).click(function() {
        $("#btn-preview").hide();
        document.getElementById("file").required = true;
        $('#id').val('');
        $('#nama').val('');
        $('#tgl_berlaku').val('');
        document.getElementsByName('isi')[0].value = '';
        var upload_file = document.getElementById('file');
        upload_file.value = '';
    });
    $( "#add-pkb" ).click(function() {
        document.getElementById("file").required = true;
        $('#id_pkb').val('');
        $('#nama_pkb').val('');
        $('#tgl_berlaku_pkb').val('');
        document.getElementsByName('isi_pkb')[0].value = '';
        var upload_file = document.getElementById('file_pkb');
        upload_file.value = '';
    });

    function clearUpload(){
        var upload = document.getElementById('file');
        upload.value = '';
    } 

    function clearPkbUpload(){
        var upload = document.getElementById('file_pkb');
        upload.value = '';
    }    

    function uploadValidation(){
        var upload = document.getElementById('file');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }

    function uploadPkbValidation(){
        var upload = document.getElementById('file_pkb');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }

    function clearrevisiUpload(){
        var upload = document.getElementById('file_revisi');
        upload.value = '';
    }

    function uploadrevisiValidation(){
        var upload = document.getElementById('file_revisi');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }
</script>
<script>
    $(document).ready(function () {
        $('#table_rule').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        scrollX: false,
        ajax: "{{ route('internal-rule.index') }}",
            "columnDefs": [
                { "width": "20%", "targets": 4 }
            ],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'nama', name: 'nama' , "className": "text-left"},
                {data: 'tgl_berlaku', name: 'tgl_berlaku' , "className": "text-center"},
                {data: 'isi', name: 'isi' , "className": "text-left"},
                // {data: 'status', name: 'status' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });

        $('#table_rule').on("click", ".edit-btn", function() {
            $("#btn-preview").show();
            document.getElementById("file").required = false;  
            var preview = $(this).closest('tr').find('#preview').val();   

            var ruleId = $(this).data("id");
            $.ajax({
            url: "{{ route('internal-rule.edit') }}",
            method: "GET",
            data: {
                id: ruleId
            },
            success: function(result) {                
                //send to add/edit modal
                $("input[name='id']").val(result.id);
                $("input[name='nama']").val(result.nama);
                $("input[name='tgl_berlaku']").val(result.tgl_berlaku);
                document.getElementsByName('isi')[0].value = result.isi;
                $("#modal").modal("show");

                if(!result.file){
                    $("#show-preview").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
                }else{
                    $("#show-preview").html('<embed src="'+preview+'" frameborder="0" width="100%" height="450px">');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });
        });

        $('#table_rule').on("click", ".revisi-btn", function() {
            var preview = $(this).closest('tr').find('#preview_revisi').val();
            var revisiId = $(this).data("id");

            $.ajax({
                url: "{{ route('internal-rule.edit') }}",
                method: "GET",
                data: {
                    id: revisiId
                },
                success: function(result) {               
                    //send to revisi modal
                    $("input[name='id_revisi']").val(result.id);
                    $("input[name='nama_revisi']").val(result.nama);
                    $("input[name='tgl_berlaku_revisi']").val(result.tgl_berlaku);
                    document.getElementsByName('isi_revisi')[0].value = result.isi;
                    // $("#modal").modal("show");

                    if(!result.file){
                        $("#show-preview-revisi").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
                    }else{
                        $("#show-preview-revisi").html('<embed src="'+preview+'" frameborder="0" width="100%" height="450px">');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr, status, error);
                }
            });
        });
        $('#table_rule').on("click", ".preview-btn", function() {
            var preview = $(this).data("id");
            $("#show-preview-pdf").html('<embed src="'+preview+'" frameborder="0" width="100%" height="450px">');
        });
    });
</script>
<script>
    var count = 100;
    function new_link() {
        count++;
        var e = document.createElement("tr"),
            t = (e.id = count, e.className = "product", 
            '<tr>'+
                '<th scope="row" class="product-id">' + count + '</th>'+
                '<td class="text-start">'+
                    '<input type="hidden" name="no_urut[]" value="'+count+'">'+
                    '<div class="mb-2">'+
                        '<div class="form-group">'+
                            '<select class="form-control js-example-basic-single @error("id_level") is-invalid @enderror" name="id_level-'+count+'[]" id="level-dropdown-' +count +'" data-placeholder="--Pilih Level--" required>'+
                                '<option selected="true" disabled="true"></option>'+
                                '<option value="all">All</option>'+
                                '@foreach($levels as $level)'+
                                    '<option value="{{ $level->id }}">{{ $level->nama }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="mb-2">'+
                        '<div class="form-group">'+
                            '<select class="form-control js-example-basic-single @error("id_employee") is-invalid @enderror" name="id_employee-'+count+'[]" id="employee-dropdown-' +count +'" multiple="multiple" data-placeholder="--Pilih Employee--" required>'+
                                '<option value="all">All</option>'+
                                '@foreach($employees as $employee)'+
                                    '<option value="{{ $employee->id }}">{{ $employee->fullname }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="mb-2">'+
                        '<div class="form-group">'+
                            '<select class="form-control js-example-basic-single @error("id_area") is-invalid @enderror" name="id_area-'+count+'[]" id="area-dropdown-' +count +'" multiple="multiple" data-placeholder="--Pilih Area--" required>'+
                                '<option value="all">All</option>'+
                                '@foreach($areas as $area)'+
                                    '<option value="{{ $area->id }}">{{ $area->name }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="mb-2 input-group">'+
                        '<span class="input-group-text">Rp</span>'+
                        '<input type="text" class="form-control" id="nominal-dropdown-' +count +'" name="value_nominal-'+count+'[]" placeholder="Masukkan Nominal" value="">'+
                    '</div>'+
                    '<div class="mb-0 white-space-pre-line">'+
                        '<textarea class="form-control" id="textual-dropdown-' +count +'" name="value_textual-'+count+'[]" placeholder="Masukkan Keterangan" value="" rows="3"></textarea>'+
                    '</div>'+
                '</td>'+
                '<td class="product-removal">'+
                    '<a class="btn btn-soft-danger">'+
                        '<i class="ri-delete-bin-line"></i>'+
                    '</a>'+
                '</td>'+
            '</tr>'
            ),
            t = (e.innerHTML = document.getElementById("newForm").innerHTML + t, document.getElementById("newlink")
                .appendChild(e), document.querySelectorAll("[data-trigger]"));
        Array.from(t).forEach(function(e) {
            new Choices(e, {
                placeholderValue: "This is a placeholder set in the config",
                searchPlaceholderValue: "This is a search placeholder"
            })
        }), remove(), resetRow()
        //reinitialize the new select box
        $('.js-example-basic-single').select2({dropdownParent: $('#modal-setting-benefit .modal-content')});
        //convert currency
        var rupiah = document.getElementById('nominal-dropdown-'+count+'');
        rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value);
        });
        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? rupiah : "";
        // return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }
    }
    remove();

    function remove() {
        Array.from(document.querySelectorAll(".product-removal a")).forEach(function(e) {
            e.addEventListener("click", function(e) {
                removeItem(e), resetRow()
            })
        })
    }

    function resetRow() {
        Array.from(document.getElementById("newlink").querySelectorAll("tr")).forEach(function(e, t) {
            t += 1;
            e.querySelector(".product-id").innerHTML = t
        })
    }

    function removeItem(e) {
        e.target.closest("tr").remove()
    }
</script>
<script>
    $(document).ready(function () {
        $('#table_benefit').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        scrollX: false,
        ajax: "{{ route('benefit.index') }}",
            "columnDefs": [
                { "width": "20%", "targets": 3 }
            ],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'benefit', name: 'benefit' , "className": "text-center"},
                {data: 'id_internal_rule', name: 'id_internal_rule' , "className": "text-center"},
                // {data: 'value', name: 'value' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });

        $('#table_benefit').on("click", ".setting-benefit-btn", function() {
            //send to modal setting
            $('#id_rule2').select2({dropdownParent: $('#modal-setting-benefit .modal-content')});
            // $('#id_area2').select2({dropdownParent: $('#modal-setting-benefit .modal-content')});
            var benefitName = $(this).data("id");
            $('#judul-benefit').html(benefitName);        
            // $("input[name='benefit']").val(benefitName);
            $('#id_rule2').val(null).trigger('change');
            // $('#id_area2').val(null).trigger('change');
            $('#newlink').html('');
            
            $.ajax({
            url: "{{ route('internal-rule.edit.setting.benefit') }}",
            method: "GET",
            data: {
                id: benefitName
            },
            success: function(result) { 
                // console.log(result.permission)
                $('#benefit2').val(benefitName);
                if(!result.rule){
                    $('#id_rule2').find('option[value="none"]').prop('selected', true);
                    // console.log('kosong')
                }else{
                    $('#id_rule2').find('option[value="'+result.rule+'"]').prop('selected', true);
                }
                // $('#id_area2').find('option[value="'+result.area+'"]').prop('selected', true);
                if(!result.permission){
                    $('#newlink').html(''); 
                    console.log('tidak ada rule')     
                }else{
                    var count = 0;   
                    $.each(result.permission, function(i,e){
                        // console.log(e[0])
                        if(!e.level_id){
                            //kosong
                        }else{
                            count++
                            $('#newlink').append('<tr id="1" class="product">'+
                            '<th scope="row" class="product-id">'+count+'</th>'+                            
                                '<td class="text-start">'+
                                    '<input type="hidden" id="nomor" name="no_urut[]" value="'+count+'">'+
                                    '<div class="mb-2">'+
                                        '<div class="form-group">'+
                                            '<select class="form-control js-example-basic-single @error("id_level") is-invalid @enderror" name="id_level-'+count+'[]" id="level-'+count+'" data-placeholder="--Pilih Level--" required><option value="all">All</option>@foreach($levels as $level)<option value="{{ $level->id }}">{{ $level->nama }}</option>@endforeach</select>'+
                                        '</div>'+
                                    '</div>'+
                                '</td>'+
                                '<td class="text-start">'+
                                    '<div class="mb-2">'+
                                        '<div class="form-group">'+
                                            '<select class="form-control js-example-basic-single @error("id_employee") is-invalid @enderror" name="id_employee-'+count+'[]" id="employee-'+count+'" data-placeholder="--Pilih Employee--" multiple="multiple" required><option value="all">All</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->fullname }}</option>@endforeach</select>'+
                                        '</div>'+
                                    '</div>'+
                                '</td>'+
                                '<td class="text-start">'+
                                    '<div class="mb-2">'+
                                        '<div class="form-group">'+
                                            '<select class="form-control js-example-basic-single @error("id_area") is-invalid @enderror" name="id_area-'+count+'[]" id="area-'+count+'" data-placeholder="--Pilih Area--" multiple="multiple" required><option value="all">All</option>@foreach($areas as $area)<option value="{{ $area->id }}">{{ $area->name }}</option>@endforeach</select>'+
                                        '</div>'+
                                    '</div>'+
                                '</td>'+
                                '<td class="text-start">'+
                                    '<div class="mb-2 input-group">'+
                                        '<span class="input-group-text">Rp</span><input type="text" class="form-control" id="nominal-'+count+'" name="value_nominal-'+count+'[]" placeholder="Masukkan Nominal" value="">'+
                                    '</div>'+
                                    '<div class="mb-0 white-space-pre-line">'+
                                        '<textarea class="form-control" id="textual-'+count+'" name="value_textual-'+count+'[]" placeholder="Masukkan Keterangan" value="" rows="3"></textarea>'+
                                    '</div>'+
                                '</td>'+
                                '<td class="product-removal">'+
                                    '<a href="#" onclick="remove();" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i></a>'+
                                '</td>'+
                            '</tr>');
                            
                            //inisialisasi select2
                            //level
                            $('#level-'+count+'').select2({dropdownParent: $('#modal-setting-benefit .modal-content')});
                            $('#level-'+count+'').find('option[value="' + e.level_id + '"]').prop('selected', true);
                            $('#level-'+count+'').trigger('change.select2');
                            //employee
                            $('#employee-'+count+'').select2({dropdownParent: $('#modal-setting-benefit .modal-content')});
                            $.each(e.employee_id, function(a,b){
                                $('#employee-'+count+'').find('option[value="' + b + '"]').prop(
                                'selected', true);
                            });
                            $('#employee-'+count+'').trigger('change.select2');
                            //area
                            $('#area-'+count+'').select2({dropdownParent: $('#modal-setting-benefit .modal-content')});
                            $.each(e.area_id, function(a,b){
                                $('#area-'+count+'').find('option[value="' + b + '"]').prop(
                                'selected', true);
                            });
                            $('#area-'+count+'').trigger('change.select2');
                            //inisialisasi input
                            const nominal = e.nominal; 
                            $('#nominal-'+count+'').val(new Intl.NumberFormat('en-DE').format(nominal));                
                            $('#textual-'+count+'').val(e.textual);
                            //convert currency
                            var rupiah = document.getElementById('nominal-'+count+'');
                            rupiah.addEventListener("keyup", function(e) {
                            // tambahkan 'Rp.' pada saat form di ketik
                            // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
                            rupiah.value = formatRupiah(this.value);
                            });
                            /* Fungsi formatRupiah */
                            function formatRupiah(angka, prefix) {
                            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                                split = number_string.split(","),
                                sisa = split[0].length % 3,
                                rupiah = split[0].substr(0, sisa),
                                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                            // tambahkan titik jika yang di input sudah menjadi angka ribuan
                            if (ribuan) {
                                separator = sisa ? "." : "";
                                rupiah += separator + ribuan.join(".");
                            }

                            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
                            return prefix == undefined ? rupiah : rupiah ? rupiah : "";
                            // return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
                            }                                          
                        }
                    }); 
                }             
                //send to setting modal
                // $.each(result.permission, function(i,e){
                //     console.log(e[2])
                //     $("input[name='benefit']").val(e[2]);                
                //     $('#id_area').find('option[value="' + e[] + '"]').prop(
                //     'selected', true);    
                //     $('#id_level').find('option[value="' + e.id_level + '"]').prop(
                //     'selected', true);        
                // });
                $('#id_rule2').trigger('change.select2');
                // $('#id_area2').trigger('change.select2');
                // $('#id_level').trigger('change.select2');
                // $("#modal-setting").modal("show");
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });

        });

        $("#modal-delete-benefit").on("hidden.bs.modal", function(){
            $("input[name='id_delete_benefit']").val();            
        });

        $('#table_benefit').on("click", ".delete-benefit-btn", function() {
            var delete_benefit = $(this).data("id");
            $("input[name='id_delete_benefit']").val(delete_benefit);            
        });        
    });
</script>
<script>
    $(document).ready(function () {
        $('#table_pkb').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        scrollX: false,
        ajax: "{{ route('pkb.index') }}",
            "columnDefs": [
                { "width": "20%", "targets": 4 }
            ],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'nama', name: 'nama' , "className": "text-left"},
                {data: 'periode', name: 'periode' , "className": "text-center"},
                {data: 'isi', name: 'isi' , "className": "text-left"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });

        $('#table_pkb').on("click", ".edit-btn", function() {
            document.getElementById("file_pkb").required = false;  
            var pkbId = $(this).data("id");
            $.ajax({
                url: "{{ route('pkb.edit') }}",
                method: "GET",
                data: {
                    id: pkbId
                },
                success: function(result) {                
                    //send to add/edit modal
                    var tgl_periode = result.tgl_berlaku+' to '+result.tgl_berakhir;
                    // console.log(tgl_periode);
                    $("input[name='id_pkb']").val(result.id);
                    $("input[name='nama_pkb']").val(result.nama);
                    $("input[name='periode_pkb']").val(tgl_periode);
                    document.getElementsByName('isi_pkb')[0].value = result.isi;
                    $("#modalPKB").modal("show");
                },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });
        });

        $('#table_pkb').on("click", ".view-btn", function() {
            var preview = $(this).data("id");
            console.log(preview)
            $("#show-preview-pkb").html('<embed src="'+preview+'" frameborder="0" width="100%" height="450px">');
        });
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