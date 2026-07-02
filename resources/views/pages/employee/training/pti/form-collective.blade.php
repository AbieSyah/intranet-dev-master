@extends('layouts.general')
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
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">                                        
                <form id="Form-collective" action="{{ route('training.emp.fpkt.pti.store') }}" method="post">
                    @csrf
                    @method('PUT')                                      
                    <div class="row mb-3">
                        <div class="col-lg-6">
                        <h4 class="text-primary">Formulir Penilaian Kebutuhan Training (FPKT)</h4>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-10">
                            <div class="row">
                                <input type="hidden" name="id_fkt" value="{{implode(',',$qry_fkt->pluck('id')->toArray())}}">
                                <div class="col-lg-5">
                                    <label for="topik" class="form-label col-form-label col-form-label-sm">No Form</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->kode ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Usulan Topik Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->judul ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Rekomendasi Jenis Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->jenis_pelatihan ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Rekomendasi Vendor Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->provider->nama ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Peserta Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{implode(', ',$arr_peserta->pluck('fullname')->toArray()) ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Nomor Induk Karyawan (NIK)</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->peserta->nik ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Departemen / Bagian</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->peserta->department->name ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Jabatan</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->peserta->position->nama ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Atasan Langsung</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->penilai->fullname ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-10">
                            <!-- Tables Border Colors -->
                            <div class="table-responsive">
                                <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                Latar Belakang Usulan Training : <br>
                                                <p class="text-muted"><i>(Penjelasan mengenai keterkaitan antara usulan topik training dengan pekerjaan saat ini).</i></p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <div>
                                                    <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="3" required></textarea>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div data-simplebar data-simplebar-auto-hide="false" style="max-width: 100%;">
                                <table class="table table-borderless fs-12" style="table-layout: fixed; width: 100%;">
                                    <thead class="align-middle">
                                        <tr class="table-active">
                                            <!-- <th scope="col" style="width: 2%;">#</th> -->
                                            <th scope="col" style="width: 13%; text-align: center;">
                                                Tujuan Training <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tuliskan Tujuan yang ingin dicapai setelah mengikuti training"></i>
                                            </th>
                                            <th scope="col" style="width: 13%; text-align: center;">
                                                Kompetensi yang Diharapkan <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tuliskan Kompetensi apa saja yang dapat menunjang dalam mencapai tujuan training ini"></i>
                                            </th>
                                            <th scope="col" style="width: 13%; text-align: center;">
                                                Skill / Knowledge <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Sebutkan minimal 3 komponen Skill / Knowledge yang saat ini dimiliki oleh karyawan dan diperlukan untuk merepresentasikan kompetensi yang diharapkan"></i>
                                            </th>
                                            <th scope="col" style="width: 8%; text-align: center;">Level Skill diisi oleh peserta (Skala 1-5) <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tingkat Skill / Knowledge menurut penilaian diri sendiri"></i></th>
                                            <th scope="col" style="width: 8%; text-align: center;">Level Skill diisi oleh atasan langsung (Skala 1-5) <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tingkat Skill / Knowledge menurut penilaian atasan langsung"></i></th>
                                            <th scope="col" id="h-provider" style="width: 8%; text-align: center;">Rata - rata Level Skill <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Rata - rata tingkat Skill / Knowledge menurut penilaian diri sendiri dan atasan langsung"></i></th>
                                            <th scope="col" id="h-biaya" style="width: 10%; text-align: center;">Kebutuhan Training</th>
                                            <th scope="col" style="width: 5%; text-align: center;"></th>
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
                                                <a href="javascript:new_link()"
                                                    class="btn btn-soft-success"><i
                                                        class="ri-add-fill me-1 align-bottom"></i> Add New</a>
                                            </td>
                                        </tr>
                                        <tr class="border-top border-top-dashed mt-2">
                                            <td colspan="3"></td>
                                            <td colspan="2" class="p-0"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <h5>Analisa Kebutuhan Pelatihan Karyawan</h5>
                        <div class="row mt-2">
                            <label for="no_1" class="form-label">1. Apakah ada keterkaitan antara usulan training yang anda ajukan dengan pekerjaan anda saat ini?</label>
                            <div class="col-lg-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_satu" id="satu_ya" value="satu_ya" required>
                                    <label class="form-check-label col-form-label-sm" for="satu_ya">
                                        Ya, sebutkan
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_satu" id="satu_tidak" value="satu_tidak" required>
                                    <label class="form-check-label col-form-label-sm" for="satu_tidak">
                                        Tidak
                                    </label>
                                </div>                                        
                            </div>
                        </div>
                        <div id="cek_catatan_satu" class="mt-2">
                            <div class="col-lg-10">
                                <textarea class="form-control" id="catatan_satu" name="catatan_satu" rows="3"></textarea>
                            </div>
                        </div>
                        <div id="cek_analisa_dua" class="mt-4">
                            <div class="row">
                                <label for="no_2" class="form-label">2. Apakah ada permasalahan saat ini sehingga perlu dilakukan training?</label>
                                <div class="col-lg-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="analisa_dua" id="dua_ya" value="dua_ya" required>
                                        <label class="form-check-label col-form-label-sm" for="dua_ya">
                                            Ya, sebutkan
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="analisa_dua" id="dua_tidak" value="dua_tidak" required>
                                        <label class="form-check-label col-form-label-sm" for="dua_tidak">
                                            Tidak
                                        </label>
                                    </div>                                        
                                </div>
                            </div>
                            <div id="cek_catatan_dua" class="mt-2">
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="catatan_dua" name="catatan_dua" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div id="cek_analisa_tiga" class="mt-2">
                            <div id="cek_catatan_tiga" class="mt-2">
                                <label for="no_3" class="form-label">3. Apa harapan anda terhadap pelatihan yang anda usulkan?</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="catatan_tiga" name="catatan_tiga" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <div class="col-lg-10">
                            <!-- Tables Border Colors -->
                            <div class="table-responsive">
                                <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                Catatan Dari Atasan : <br>
                                                <p class="text-muted"><i>(diisi jika skor kebutuhan training 5 atau 4).</i></p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <div>
                                                    <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary float-end" id="btn-submit" name="action" value="collective" type="submit">Submit</button>
                        </div>
                    </div>
                </form>        
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->
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
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $("#btn-submit").click(function() {
            $("#Form-collective").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
    });
</script>
<script>
    $(document).ready(function () {        
        $('#cek_catatan_satu').hide();
        $('#cek_analisa_dua').hide();
        $('#cek_catatan_dua').hide();
        $('#cek_analisa_tiga').hide();
        $('#cek_catatan_tiga').hide();
        $("input[name='analisa_satu']").click(function() {
                var analisa_satu = this.value;
                if(analisa_satu == 'satu_ya'){
                    $('#cek_catatan_satu').show();
                    $('#catatan_satu').prop('required',true);
                    $('#cek_analisa_dua').show();
                    $('#dua_ya').prop('required',true);
                    $('#dua_tidak').prop('required',true);
                    $('#cek_analisa_tiga').hide();
                    $('#cek_catatan_tiga').hide();
                    $('#catatan_tiga').prop('required',false);
                    $('#catatan_tiga').val('');
                }else{
                    $('#cek_catatan_satu').hide();
                    $('#catatan_satu').prop('required',false);
                    $('#catatan_satu').val('');
                    $('#cek_analisa_dua').hide();
                    $('#cek_catatan_dua').hide();
                    $('#catatan_dua').prop('required',false);
                    $('#catatan_dua').val('');
                    $('#dua_ya').prop('checked', false);
                    $('#dua_tidak').prop('checked', false);
                    $('#dua_ya').prop('required',false);
                    $('#dua_tidak').prop('required',false);
                    $('#cek_analisa_tiga').show();
                    $('#cek_catatan_tiga').show();
                    $('#catatan_tiga').prop('required',true);
                }
            });
            $("input[name='analisa_dua']").click(function() {
                var analisa_dua = this.value;
                if(analisa_dua == 'dua_ya'){
                    $('#cek_catatan_dua').show();
                    $('#catatan_dua').prop('required',true);
                    $('#cek_analisa_tiga').show();
                    $('#cek_catatan_tiga').show();
                    $('#catatan_tiga').prop('required',true);
                }else{
                    $('#cek_catatan_dua').hide();
                    $('#catatan_dua').prop('required',false);
                    $('#catatan_dua').val('');
                    $('#cek_analisa_tiga').show();
                    $('#cek_catatan_tiga').show();
                    $('#catatan_tiga').prop('required',true);
                }
            });
    });
</script>
<script>
    $(function () {    
        $('.select2').select2();
    });
</script>
<script>
    var count = 1;
    function new_link() {
        count++;        
        var e = document.createElement("tr"),
            t = (e.id = count, e.className = "produk", 
            '<tr>'+
                '<th scope="row" class="produk-id" hidden>' + count + '</th>'+
                '<td class="text-start">'+
                    '<input type="hidden" id="nomor" name="no_urut[]" value="'+count+'">'+
                    '<div class="input-group mb-2">'+
                        '<textarea rows="2" class="form-control" id="tujuan-' +count +'" name="tujuan-'+count+'[]" required></textarea>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<textarea rows="2" class="form-control" id="kompetensi-' +count +'" name="kompetensi-'+count+'[]" required></textarea>'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<textarea rows="2" class="form-control" id="skill-' +count +'" name="skill-'+count+'[]" required></textarea>'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="number" class="form-control peserta" id="level_peserta-' +count +'" name="level_peserta-'+count+'[]" value="">'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="number" class="form-control atasan" id="level_atasan-' +count +'" name="level_atasan-'+count+'[]" value="">'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="number" class="form-control" id="level_rata-' +count +'" name="level_rata-'+count+'[]" value="" style="Background-color: #eff2f7;" readonly>'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="text" class="form-control" id="level_kebutuhan-' +count +'" name="level_kebutuhan-'+count+'[]" value="" style="Background-color: #eff2f7;" readonly>'+
                    '</div>'+
                '</td>'+                
                '<td class="produk-removal">'+
                    '<a href="javascript:void(0)" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i></a>'+
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
        $('.select2').select2();

        //limit skala peserta
        $(".peserta").on('keyup', function(){
            if(this.value > 5){
                alert('Nilai yang anda masukkan melebihi skala');
                this.value = null;
            }
            let get_id = this.id;
            var urut = get_id.replace("level_peserta-", "");
            //kalkulasi rata rata 
            let nilai_peserta = this.value;
            let nilai_atasan = $("#level_atasan-" +urut +"").val();
            if(nilai_atasan >= 0){
                let rata_rata = (parseInt(nilai_peserta) + parseInt(nilai_atasan))/2;
                $('#level_rata-' +urut +'').val(Math.floor((rata_rata)));
                if(Math.floor((rata_rata)) == '1'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Tinggi');
                }else if(Math.floor((rata_rata)) == '2'){
                    $('#level_kebutuhan-'+urut+'').val('Tinggi');
                }else if(Math.floor((rata_rata)) == '3'){
                    $('#level_kebutuhan-'+urut+'').val('Sedang');
                }else if(Math.floor((rata_rata)) == '4'){
                    $('#level_kebutuhan-'+urut+'').val('Rendah');
                }else if(Math.floor((rata_rata)) == '5'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Rendah');
                }else{
                    $('#level_kebutuhan-'+urut+'').val('');
                }
            }
        });     
        $(".atasan").on('keyup', function(){
            if(this.value > 5){
                alert('Nilai yang anda masukkan melebihi skala');
                this.value = null;
            }
            let get_id = this.id;
            var urut = get_id.replace("level_atasan-", "");
            //kalkulasi rata rata 
            let nilai_peserta = $("#level_peserta-" +urut +"").val();
            let nilai_atasan = this.value;
            if(nilai_peserta >= 0){
                let rata_rata = (parseInt(nilai_peserta) + parseInt(nilai_atasan))/2;
                $('#level_rata-' +urut +'').val(Math.floor((rata_rata)));
                if(Math.floor((rata_rata)) == '1'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Tinggi');
                }else if(Math.floor((rata_rata)) == '2'){
                    $('#level_kebutuhan-'+urut+'').val('Tinggi');
                }else if(Math.floor((rata_rata)) == '3'){
                    $('#level_kebutuhan-'+urut+'').val('Sedang');
                }else if(Math.floor((rata_rata)) == '4'){
                    $('#level_kebutuhan-'+urut+'').val('Rendah');
                }else if(Math.floor((rata_rata)) == '5'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Rendah');
                }else{
                    $('#level_kebutuhan-'+urut+'').val('');
                }
            }
        });      
    }
    remove();

    function remove() {
        Array.from(document.querySelectorAll(".produk-removal a")).forEach(function(e) {
            e.addEventListener("click", function(e) {
                removeItem(e), resetRow()
            })
        })
    }

    function resetRow() {
        Array.from(document.getElementById("newlink").querySelectorAll("tr")).forEach(function(e, t) {
            t += 1;
            e.querySelector(".produk-id").innerHTML = t
        })
    }

    function removeItem(e) {
        e.target.closest("tr").remove()
    }
</script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection
