@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Work Hour</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Work Hour</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <button type="button" id="add-workhour" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Work Hour">
                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Work Hour
                </button>
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped bordered" id="table_workhour">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Work Name</th>
                    <th scope="col" style="text-align:center">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Modal add/edit-->
<div class="modal fade" id="modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Work Hour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form" action="{{ route('workhour.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <!-- WORK NAME -->
                    <div class="mb-3">
                        <label class="form-label">Work Name</label>
                        <input type="text" class="form-control" name="work_name" required>
                    </div>
                    <hr>
                        <h6>Work Hour Detail</h6>
                    <table class="table table-bordered" id="detail-table">
                        <thead>
                            <tr>
                            <th width="19%">Day</th>
                            <th width="15%">Work In</th>
                            <th width="18%">Break</th>
                            <th width="15%">Work Out</th>
                            <th width="23%">Note</th>
                            <th width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody id="detail-body">
                            <tr>
                                <td>
                                    <select name="day[]" class="form-control">
                                        <option value="monday">Monday</option>
                                        <option value="tuesday">Tuesday</option>
                                        <option value="wednesday">Wednesday</option>
                                        <option value="thursday">Thursday</option>
                                        <option value="friday">Friday</option>
                                        <option value="saturday">Saturday</option>
                                        <option value="sunday">Sunday</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="time" name="work_in[]" class="form-control work-in">
                                </td>
                                <td>
                                    <select name="break_duration[]" class="form-control break-duration">
                                        <option value="15">15 Minutes</option>
                                        <option value="30">30 Minutes</option>
                                        <option value="45">45 Minutes</option>
                                        <option value="60">1 Hour</option>
                                    </select>
                                    </td>
                                <td>
                                    <input type="time" name="work_out[]" class="form-control work-out">
                                </td>
                                <td>
                                    <input type="text" name="notes[]" class="form-control" placeholder="Optional note">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger remove-row">X</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- BUTTON -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-success" id="add-row">
                                + Add Row
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    {{-- DETAIL MODAL --}}
    <div class="modal fade" id="modal-detail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Work Hour Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Work In</th>
                                <th>Break</th>
                                <th>Work Out</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="detail-view-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    // CSRF SETUP
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // DATATABLE
    const table = $('#table_workhour').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        ajax: "{{ route('workhour.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', className: "text-center"},
            {data: 'work_name', name: 'work_name', className: "text-center"},
            {data: 'action', name: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });
    // FORM SUBMIT (LOADING MODAL)
    $("#form").on("submit", function (e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);

        Swal.fire({
            title: 'Saving...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: $(form).attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res){
                Swal.fire({
                    icon:'success',
                    title:'Success',
                    text:'Work Hour berhasil disimpan'
                });

                $("#modal").modal("hide");
                $('#table_workhour').DataTable().ajax.reload();

            },
            error: function(xhr){

                let msg = "Terjadi kesalahan";
                if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon:'error',
                    title:'Error',
                    text: msg
                });
            }
        });
    });
    // ADD WORKHOUR
    $('#add-workhour').click(function(){
        $('#form').attr('action',"{{ route('workhour.store') }}");
        // reset form
        $('#form')[0].reset();
        $("input[name='id']").val('');
        // reset detail table
        $("#detail-body").html('');
        $("#modal").modal("show");
    });
    // EDIT WORKHOUR
    $('#table_workhour').on("click",".edit-btn",function(){
        let posId = $(this).data("id");
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        $.ajax({
            url: "{{ route('workhour.edit') }}",
            method: "GET",
            data: {id: posId},
            success:function(result){
                Swal.close();
                $('#form').attr('action',"{{ route('workhour.update') }}");
                $("input[name='id']").val(result.id);
                $("input[name='work_name']").val(result.work_name);
                // reset detail
                $("#detail-body").html('');
                // isi detail shift
                if(result.details){
                    result.details.forEach(function(detail){
                        let row = `
                        <tr>
                            <td>
                                <select name="day[]" class="form-control">
                                    <option value="monday" ${detail.day== 'Monday' ? 'selected' : ''}>Monday</option>
                                    <option value="tuesday" ${detail.day== 'Tuesday' ? 'selected' : ''}>Tuesday</option>
                                    <option value="wednesday" ${detail.day== 'Wednesday' ? 'selected' : ''}>Wednesday</option>
                                    <option value="thursday" ${detail.day== 'Thursday' ? 'selected' : ''}>Thursday</option>
                                    <option value="friday" ${detail.day== 'Friday' ? 'selected' : ''}>Friday</option>
                                    <option value="saturday" ${detail.day== 'Saturday' ? 'selected' : ''}>Saturday</option>
                                    <option value="sunday" ${detail.day== 'Sunday' ? 'selected' : ''}>Sunday</option>
                                </select>
                            </td>

                            <td>
                            <input type="time" name="work_in[]" class="form-control work-in" value="${detail.work_in}">
                            </td>

                            <td>
                            <select name="break_duration[]" class="form-control break-duration">
                            <option value="15" ${detail.break_duration == '15' ? 'selected' : ''}>15 Minutes</option>
                            <option value="30" ${detail.break_duration == '30' ? 'selected' : ''}>30 Minutes</option>
                            <option value="45" ${detail.break_duration == '45' ? 'selected' : ''}>45 Minutes</option>
                            <option value="60" ${detail.break_duration == '60' ? 'selected' : ''}>1 Hour</option>
                            </select>
                            </td>

                            <td>
                            <input type="time" name="work_out[]" class="form-control work-out" value="${detail.work_out}">
                            </td>

                            <td>
                            <input type="text" name="notes[]" class="form-control" value="${detail.notes ?? ''}">
                            </td>

                            <td class="text-center">
                            <button type="button" class="btn btn-danger remove-row">X</button>
                            </td>

                        </tr>
                        `;
                        $("#detail-body").append(row);
                    });
                }
                $("#modal").modal("show");
            }
        });
    });

    //DETAIL WORKHOUR
    $('#table_workhour').on("click",".detail-btn",function(){
        let id = $(this).data("id");
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        $.ajax({
        url: "{{ route('workhour.show') }}",
        method: "GET",
        data:{id:id},
        success:function(result){
            Swal.close();
            $("#detail-view-body").html('');
            result.details.forEach(function(detail){
            let row = `
                <tr>
                <td>${detail.day}</td>
                <td>${detail.work_in}</td>
                <td>${detail.break_duration} Minutes</td>
                <td>${detail.work_out}</td>
                <td>${detail.notes ?? '-'}</td>
                </tr>
            `;
                $("#detail-view-body").append(row);
            });
                $("#modal-detail").modal("show");
        }
        });
    });

    // DELETE WORKHOUR
    $('#table_workhour').on("click",".delete-btn",function(){

        let id = $(this).data("id");

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data tidak bisa dikembalikan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result)=>{

            if(result.isConfirmed){

                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick:false,
                    didOpen:()=>{
                        Swal.showLoading()
                    }
                });
                $.ajax({
                    url: "{{ route('workhour.destroy') }}",
                    method: "DELETE",
                    data: {id:id},
                    success:function(){
                        Swal.fire({
                            icon:'success',
                            title:'Deleted',
                            text:'Data berhasil dihapus'
                        });
                        $('#table_workhour').DataTable().ajax.reload();
                    },

                    error:function(){
                        Swal.fire({
                            icon:'error',
                            title:'Error',
                            text:'Gagal menghapus data, Jam Kerja Sudah ada yang menggunakan di attendance Record'
                        });
                    }
                })
            }
        });
    });
    // ADD DETAIL ROW
    $("#add-row").click(function(){
        let row = `
        <tr>
            <td>
                <select name="day[]" class="form-control">
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                </select>
            </td>

            <td>
            <input type="time" name="work_in[]" class="form-control work-in">
            </td>

            <td>
            <select name="break_duration[]" class="form-control break-duration">
            <option value="15">15 Minutes</option>
            <option value="30">30 Minutes</option>
            <option value="45">45 Minutes</option>
            <option value="60">1 Hour</option>
            </select>
            </td>

            <td>
            <input type="time" name="work_out[]" class="form-control work-out">
            </td>

            <td>
            <input type="text" name="notes[]" class="form-control" placeholder="Optional note">
            </td>

            <td class="text-center">
            <button type="button" class="btn btn-danger remove-row">X</button>
            </td>
        </tr>
        `;
        $("#detail-body").append(row);

    });
    // AUTO CALCULATE WORK OUT
    $(document).on("change",".work-in, .break-duration",function(){

        let row = $(this).closest("tr");

        let workIn = row.find(".work-in").val();
        let breakDuration = parseInt(row.find(".break-duration").val());

        if(workIn){

            let parts = workIn.split(":");

            let hour = parseInt(parts[0]);
            let minute = parseInt(parts[1]);

            let date = new Date();
            date.setHours(hour);
            date.setMinutes(minute);

            // tambah 8 jam kerja
            date.setHours(date.getHours() + 8);

            // tambah waktu istirahat
            date.setMinutes(date.getMinutes() + breakDuration);

            let h = String(date.getHours()).padStart(2,'0');
            let m = String(date.getMinutes()).padStart(2,'0');

            row.find(".work-out").val(h + ":" + m);
        }
    });
    // REMOVE DETAIL ROW
    $(document).on("click",".remove-row",function(){
        $(this).closest("tr").remove();
    });
});
</script>
@endsection
