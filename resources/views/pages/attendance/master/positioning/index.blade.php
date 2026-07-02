@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Positioning</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Positioning</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <button type="button" id="add-positioning" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Positioning">
                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Positioning
                </button>
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped bordered" id="table_positioning">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Area</th>
                    <th scope="col" style="text-align:center">Latitude</th>
                    <th scope="col" style="text-align:center">Longitude</th>
                    <th scope="col" style="text-align:center">Max Distance</th>
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
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Positioning</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="form" action="{{ route('positioning.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <input type="hidden" name="id">
                <!-- AREA -->
                {{-- <div class="col-lg-12">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area" placeholder="Contoh: Office / Warehouse" required>
                </div> --}}
                <div class="col-lg-12">
                    <label>Area Name</label>
                    <select name="area" class="form-select select2" required>
                        <option value="">Select Area Name</option>
                        @foreach($areas as $area)
                            <option value="{{$area->id}}">{{$area->name}}</option>
                        @endforeach
                    </select>
                </div>
                <!-- LATITUDE -->
                <div class="col-lg-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" class="form-control" name="latitude" id="latitude" readonly required>
                </div>
                <!-- LONGITUDE -->
                <div class="col-lg-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" class="form-control" name="longitude" id="longitude" readonly required>
                </div>
                <!-- BUTTON GPS -->
                <div class="col-lg-12">
                    <button type="button" id="get-location" class="btn btn-info w-100">
                        Ambil Lokasi Saat Ini
                    </button>
                </div>
                <!-- MAP -->
                <!-- <div class="col-lg-12">
                    <div id="map" style="height:300px;width:100%;border-radius:10px;"></div>
                </div> -->
                <!-- MAX DISTANCE -->
                <div class="col-lg-12">
                    <label class="form-label">Max Distance (Meter)</label>
                    <input type="number" class="form-control" name="max_distance" placeholder="Contoh: 50" required>
                </div>
                <div class="col-lg-12">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
            </form>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('javascript')

<script type="text/javascript">
let map;
let marker;
let circle;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // SUBMIT FORM ADD / UPDATE
    $("#form").on("submit", function(e){
        e.preventDefault();
        let form = $(this);
        let url = form.attr("action");
        let formData = new FormData(this);
        Swal.fire({
            title: 'Saving...',
            text: 'Please wait',
            allowOutsideClick:false,
            didOpen:()=>{
                Swal.showLoading()
            }
        });
        $.ajax({
            url:url,
            method:"POST",
            data:formData,
            processData:false,
            contentType:false,
            success:function(res){
                Swal.close();
                if(res.status === 'success'){
                    Swal.fire({
                        icon:'success',
                        title:'Success',
                        text:res.message
                    });
                    $("#modal").modal("hide");
                    $('#table_positioning').DataTable().ajax.reload();
                }else{
                    Swal.fire({
                        icon:'error',
                        title:'Error',
                        text:res.message
                    });
                }
            },
            error:function(xhr){
                Swal.close();
                let message = "Terjadi kesalahan";
                // if(xhr.responseJSON && xhr.responseJSON.errors){

                //     message = Object.values(xhr.responseJSON.errors)
                //         .map(e => e.join("<br>"))
                //         .join("<br>");
                // }
                Swal.fire({
                    icon:'error',
                    title:'Validation Error',
                    html:message
                });
            }
        });
    });

    // INIT MAP
    // map = L.map('map').setView([-2.5489,118.0149],5);
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     attribution: 'Map data © OpenStreetMap contributors'
    // }).addTo(map);
    // marker = L.marker([-2.5489,118.0149]).addTo(map);

    // DATATABLE
    $('#table_positioning').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        ajax:"{{ route('positioning.index') }}",
        columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'name', name: 'areas.name' , "className": "text-center"},
                {data: 'latitude', name: 'master_positioning.latitude' , "className": "text-center"},
                {data: 'longitude', name: 'master_positioning.longitude' , "className": "text-center"},
                {data: 'max_distance', name: 'master_positioning.max_distance' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        // columns:[
        //     {data:'DT_RowIndex',className:'text-center'},
        //     {data:'area',className:'text-center'},
        //     {data:'latitude',className:'text-center'},
        //     {data:'longitude',className:'text-center'},
        //     {data:'max_distance',className:'text-center'},
        //     {data:'action',className:'text-center',orderable:false,searchable:false}
        // ]
    });

    // ADD POSITIONING
    $('#add-positioning').click(function(){
        $('#form').attr('action',"{{ route('positioning.store') }}");
        $("input[name='id']").val('');
        $("input[name='area']").val('');
        $("#latitude").val('');
        $("#longitude").val('');
        $("input[name='max_distance']").val('');
        // map.setView([-2.5489,118.0149],5);
        // if(marker){
        //     map.removeLayer(marker);
        // }
        // if(circle){
        //     map.removeLayer(circle);
        // }
        // marker = L.marker([-2.5489,118.0149]).addTo(map);
        $("#modal").modal("show");
    });

    // EDIT POSITIONING
    $('#table_positioning').on("click",".edit-btn",function(){
        let posId=$(this).data("id");
        $.ajax({
            url:"{{ route('positioning.edit') }}",
            method:"GET",
            data:{id:posId},
            success:function(result){
                $('#form').attr('action',"{{ route('positioning.update') }}");
                $("input[name='id']").val(result.id);
                $("input[name='area']").val(result.area);
                $("#latitude").val(result.latitude);
                $("#longitude").val(result.longitude);
                $("input[name='max_distance']").val(result.max_distance);
                // map.setView([result.latitude,result.longitude],18);
                // marker.setLatLng([result.latitude,result.longitude]);
                // if(circle){
                //     map.removeLayer(circle);
                // }
                // circle=L.circle([result.latitude,result.longitude],{
                //     radius:result.max_distance,
                //     color:'blue'
                // }).addTo(map);
                $("#modal").modal("show");
            }
        });
    });

    //DELETE POSISITIONING
    $('#table_positioning').on("click",".delete-btn",function(){

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
                    url: "{{ route('positioning.destroy') }}",
                    method: "DELETE",
                    data: {id:id},
                    success:function(){
                        Swal.fire({
                            icon:'success',
                            title:'Deleted',
                            text:'Data berhasil dihapus'
                        });
                        $('#table_positioning').DataTable().ajax.reload();
                    },

                    error:function(){
                        Swal.fire({
                            icon:'error',
                            title:'Error',
                            text:'Gagal menghapus data'
                        });
                    }
                })
            }
        });
    });

    // GET GPS LOCATION
    $('#get-location').click(function(){
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(function(position){
                let lat=position.coords.latitude;
                let lng=position.coords.longitude;
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                // map.setView([lat,lng],18);
                // if(marker){
                //     map.removeLayer(marker);
                // }
                // marker=L.marker([lat,lng]).addTo(map);
                updateRadius();
            });
        }
    });

    // UPDATE RADIUS
    $('input[name="max_distance"]').on('input',function(){
        updateRadius();
    });

    function updateRadius(){
        let lat=$('#latitude').val();
        let lng=$('#longitude').val();
        let radius=$('input[name="max_distance"]').val();
        // if(circle){
        //     map.removeLayer(circle);
        // }
        // if(lat && lng && radius){
        //     circle=L.circle([lat,lng],{
        //         color:'blue',
        //         fillOpacity:0.2,
        //         radius:radius
        //     }).addTo(map);
        // }
    }

    // FIX MAP DI MODAL
    // $('#modal').on('shown.bs.modal',function(){
    //     setTimeout(function(){
    //         map.invalidateSize();
    //     },200);
    // });
});
</script>
<script>
$(document).ready(function () {
    $('.select2').select2({
    dropdownParent: $('#modal')
    });
});
</script>
@endsection
