@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
  <!-- Select2-->
  <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
  <style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
  </style>
  <!-- Toastr Notifications-->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Data Kunjungan Klinik</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Data</a></li>
              <li class="breadcrumb-item active">Kunjungan Klinik</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="bulan" id="bulan" required>
                        @if($month == '01')
                        <option value="01" selected> Januari</option>
                        @else
                        <option value="01"> Januari</option>
                        @endif
                        @if($month == '02')
                        <option value="02" selected> Februari</option>
                        @else
                        <option value="02"> Februari</option>
                        @endif
                        @if($month == '03')
                        <option value="03" selected> Maret</option>
                        @else
                        <option value="03"> Maret</option>
                        @endif
                        @if($month == '04')
                        <option value="04" selected> April</option>
                        @else
                        <option value="04"> April</option>
                        @endif
                        @if($month == '05')
                        <option value="05" selected> Mei</option>
                        @else
                        <option value="05"> Mei</option>
                        @endif
                        @if($month == '06')
                        <option value="06" selected> Juni</option>
                        @else
                        <option value="06"> Juni</option>
                        @endif
                        @if($month == '07')
                        <option value="07" selected> Juli</option>
                        @else
                        <option value="07"> Juli</option>
                        @endif
                        @if($month == '08')
                        <option value="08" selected> Agustus</option>
                        @else
                        <option value="08"> Agustus</option>
                        @endif
                        @if($month == '09')
                        <option value="09" selected> September</option>
                        @else
                        <option value="09"> September</option>
                        @endif
                        @if($month == '10')
                        <option value="10" selected> Oktober</option>
                        @else
                        <option value="10"> Oktober</option>
                        @endif
                        @if($month == '11')
                        <option value="11" selected> November</option>
                        @else
                        <option value="11"> November</option>
                        @endif
                        @if($month == '12')
                        <option value="12" selected> Desember</option>
                        @else
                        <option value="12"> Desember</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="tahun" id="tahun" required>
                        @for( $i=$max; $i>=$min; $i--)
                            @if($i == $max)
                            <option value="{{ $i }}" selected>{{ $i }}</option>
                            @else
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" name="filter" id="filter" class="btn btn-soft-secondary waves-effect waves-light btn-sm"><i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                <button type="button" name="refresh" id="refresh" class="btn btn-soft-danger waves-effect waves-light btn-sm"><i class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
            </div>
            <div class="col-md-4 ">
                @can('hrd.clinic.patient.create')
                <a href="{{ route('clinic.patient.create') }}" class="btn btn-primary btn-label waves-effect waves-light float-end" data-text="Tambah Pasien">
                  <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Tambah Pasien
                </a>  
                @endcan
            </div>
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        @can('hrd.clinic.patient.resume.excel')
        <div id="btn-export"></div>
        @endcan
        <table class="table table-striped bordered" id="table_patient">
          <thead>
            <tr>
              <th scope="col" style="text-align:center">No</th>
              <th scope="col" style="text-align:center">Date</th>
              <th scope="col" style="text-align:center">Patient Name</th>
              <th scope="col" style="text-align:center">Diagnose</th>
              <th scope="col" style="text-align:center">Symptoms</th>
              <th scope="col" style="text-align:center">Tension</th>
              <th scope="col" style="text-align:center">Keterangan</th>
              <th scope="col" style="text-align:center">Doctor Name</th>
              <th scope="col" style="text-align:center">Action</th>
              <th scope="col" style="text-align:center"></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--end col-->
</div>
<!--end row-->

<!--Modal delete-->
<div id="modal-delete" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form class="form-delete" action="{{ route('clinic.patient.destroy') }}" method="post">
              @csrf
              @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" id="id" name="id" value="">
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Ya</button>
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- Disclaimer Modal -->
<div class="modal fade" id="modal-doctor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalScrollableTitle">
                  SELECT DOCTOR
              </h5>
          </div>
          <form action="{{route('select.doctor')}}" method="POST">
            <div class="modal-body">
              @csrf
              <div class="row">
                <div class="col-lg-12">                            
                  <label for="id_doctor" class="form-label">Nama Doctor</label>                            
                  <select class="form-control js-example-basic-single" id="id_doctor" name="id_doctor" data-placeholder="Pilih Doctor" required>
                      <option selected="true" disabled="true"></option>
                      @foreach ($doctors as $doctor)
                        <option value="{{$doctor->id_dokter}}">{{$doctor->nama}}</option>
                      @endforeach
                  </select>
                </div><!--end col-->
              </div>             
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Ya</button>
            </div>
          </div><!-- /.modal-content -->
        </form>
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Disclaimer Modal -->
<div class="modal fade" id="modal-disclaimer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalScrollableTitle">
                  KETENTUAN PENGGUNAAN
              </h5>
          </div>
          <div class="modal-body">
              <form action="{{ route('privacy.policy') }}" method="POST">
                  @csrf
                  <h6 class="fs-15">Ketentuan Penggunaan Situs INTRANET</h6>
                  <div class="d-flex">
                      <div class="flex-shrink-0">
                          1.
                      </div>
                      <div class="flex-grow-1 ms-2">
                          <p class="text-muted mb-0">
                              Ketentuan penggunaan Situs INTRANET ini merupakan kebijakan dari PT Hisamitsu Pharma
                              Indonesia terkait pengelolaan teknis Situs serta penggunaan data pribadi yang di
                              kumpulkan dan di simpan melalui Situs ini.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          2.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Ketentuan penggunaan Situs INTRANET ini merupakan bagian dari syarat penggunaan Situs
                              ini. Dengan menggunakan Situs ini Anda telah membaca, mengetahui dan menyetujui
                              Ketentuan Penggunaan Situs INTRANET.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          3.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Ketentuan penggunaan Situs INTRANET ini dapat berubah sewaktu – waktu sesuai dengan
                              perkembangan kebijakan yang ada dan di tetapkan, Anda disarankan untuk dapat selalu
                              memahami perubahan kebijakan yang berlaku.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          4.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Ketentuan pengelolaan data pribadi pada Situs INTRANET merupakan bagian yang tidak
                              terpisahkan dari ketentuan pelindungan data pribadi yang di tetapkan oleh Perusahaan
                              yang di tuangkan dalam kebijakan pelindungan data pribadi dalam upaya pemenuhan
                              ketentuan hukum pada peraturan perundangan terkait.
                          </p>
                      </div>
                  </div>
                  <h6 class="fs-15 my-3">Pengelolaan Teknis Situs INTRANET</h6>
                  <div class="d-flex">
                      <div class="flex-shrink-0">
                          1.
                      </div>
                      <div class="flex-grow-1 ms-2">
                          <p class="text-muted mb-0">
                              Situs INTRANET adalah Sistem Informasi yang berkaitan dengan kepersonaliaan dari
                              Internal Karyawan.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          2.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Perubahan pada Situs INTRANET ini dapat Kami lakukan dari waktu ke waktu tanpa
                              pemberitahuan sebelumnya.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          3.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Beberapa konten dari Situs INTRANET di persiapkan oleh Departemen HRD & GA dimana konten
                              tersebut berdiri sendiri dan dapat di akses melalui Hypertext Link.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          4.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Hak Cipta dari setiap konten, gambar atau materi audio-visual pada Situs INTRANET
                              sepenuhnya di lindungi dan merupakan milik PT Hisamitsu Pharma Indonesia, penggunaan
                              merek dagang, ikon dan logo tanpa ijin terlebih dahulu dari pejabat yang berwenang
                              adalah dilarang.
                          </p>
                      </div>
                  </div>
                  <div class="flex-shrink-0 my-2">5. <span class="text-muted">Situs INTRANET dilindungi beberapa
                          metode kemanan :</span></div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              a. Situs INTRANET di lindungi menggunakan Kemanan SSL Versi 3 SHA-256 With RSA
                              Encryption yang selalu di perbaharui setiap tahunnya.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              b. Setiap akses pengguna di pastikan menggunakan kata sandi dengan minimum 8 karakter
                              dengan tanda baca dan huruf yang berbeda.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              c. Setiap akses pengguna akan di hubungkan secara otomatis melalui masing-masing email
                              Perusahaan atau Pribadi.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              d. Situs INTRANET hanya dapat di akses oleh Karyawan yang terdaftar pada Departemen HRD
                              & GA dan telah memiliki hak akses.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              e. Seluruh data akan tersimpan pada server secara terpusat, Di kontrol dan di kelola
                              oleh Bagian IT
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              f. Situs INTRANET di lakukan pencadangan setiap hari yang dapat di pulihkan hanya oleh
                              Bagian IT
                          </p>
                      </div>
                  </div>
                  <h6 class="fs-15 my-3">Pengelolaan Data Pribadi</h6>
                  <div class="d-flex">
                      <div class="flex-shrink-0">
                          1.
                      </div>
                      <div class="flex-grow-1 ms-2">
                          <p class="text-muted mb-0">
                              Pengumpulan Data Pribadi Anda Kami lakukan dengan tidak bertentangan pada peraturan
                              perundang-undangan yang berlaku.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          2.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Tujuan pengelolaan data pribadi di laksanakan dalam rangka penyediaan layanan Informasi
                              bagi Karyawan dan untuk mendukung pemenuhan kewajiban hukum Perusahaan pada pihak –
                              pihak yang berwenang.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          3.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Pengungkapan data pribadi Anda kepada pihak ketiga selain dari pada pihak – pihak
                              berwenang yang oleh peraturan perundangan di izinkan menerima pengungkapan berdasarkan
                              kepentingan hukum yang sah, wajib mendapatkan persetujuan Anda.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          4.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Kualitas pelindungan data pribadi menjadi perhatian kami dalam melaksanakan
                              pengelolaannya, akses dan pengungkapan data pribadi hanya dapat di laksanakan pada
                              saluran – saluran terbatas yang di izinkan oleh peraturan perundangan.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          5.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Kami berkomitmen untuk mengelola data pribadi Anda sesuai dengan ketentuan pelindungan
                              data pribadi di bawah pengawasan pejabat yang berkompeten.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          6.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Anda dapat melakukan koreksi pada data pribadi Anda berupa pengkinian keakuratan dan
                              kelengkapan dari informasi pribadi dengan terlebih dahulu mengajukan permintaan baik
                              secara lisan maupun tertulis kepada Kepala Bagian HRD melalui:
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              a. Email : phontas@hisamitsu.co.id
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-grow-1 ms-3 ">
                          <p class="text-muted mb-0">
                              b. Handphone : 0811-313-7079
                          </p>
                      </div>
                  </div>
                  <h6 class="fs-15 my-3">Kerahasiaan Data</h6>
                  <div class="d-flex">
                      <div class="flex-shrink-0">
                          1.
                      </div>
                      <div class="flex-grow-1 ms-2">
                          <p class="text-muted mb-0">
                              Situs ini hanya dapat di akses oleh orang yang terdaftar sebagai karyawan PT Hisamitsu
                              Pharma Indonesia.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          2.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Setiap karyawan dilarang keras mengambil data dan informasi yang terkandung di dalamnya
                              untuk kepentingan di luar perusahaan.
                          </p>
                      </div>
                  </div>
                  <div class="d-flex mt-2">
                      <div class="flex-shrink-0">
                          3.
                      </div>
                      <div class="flex-grow-1 ms-2 ">
                          <p class="text-muted mb-0">
                              Dilarang melakukan pemotretan atau screenshoot dari informasi yang terkandung di
                              dalamnya untuk kepentingan di luar perusahaan.
                          </p>
                      </div>
                  </div>
                  <h5 class="fs-15 my-3">Apabila anda membutuhkan bantuan dari layanan penggunaan Situs INTRANET ini,
                      silahkan di sampaikan melalui email <a
                          href="mailto:helpdesk@hisamitsu.co.id">helpdesk@hisamitsu.co.id</a> atau kunjungi layanan
                      Service IT di <a href="https://helpdesk.hisamitsu.co.id">https://helpdesk.hisamitsu.co.id</a>
                  </h5>
                  <!-- Base Example -->
                  <div class="form-check mb-2">
                      <div id="input-disclaimer">

                      </div>
                      <input class="form-check-input" type="checkbox" id="syarat" onclick="disclaimer()">
                      <label class="form-check-label" for="syarat">
                          <span class="text-primary"> Saya setuju dan mengerti dengan segala resiko dari pernyataan
                              ini.</span>
                      </label>
                  </div>
                  <div id="tutup" class="float-end">
                  </div>
                  <br>
                  <br>
          </div>
          </form>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
  @if($user->hasRole('Doctor'))
    <script>
      $('.js-example-basic-single').select2();
      $('#id_doctor').select2({dropdownParent: $('#modal-doctor .modal-content')});
      $(function () {
        var test = {{ Js::from($user) }};
        if (!test['disclaimer']) {
          $('#modal-doctor').modal('show');  
        }else{
          $('#modal-doctor').modal('hide');  
        }
      });
    </script>
  @endif
  @if(Session::has('disclaimer'))
  <script>
    $(function () {
      $('#modal-doctor').modal('hide');  
    });
    var test = {{ Js::from($user) }};
    if (!test['disclaimer']) {
        window.onload = () => {
            const myModal = new bootstrap.Modal('#modal-disclaimer');
            myModal.show();
        }
    }

    function disclaimer() {
        var checkBox = document.getElementById("syarat");
        if (checkBox.checked == true) {
            $("#input-disclaimer").html('<input type="hidden" name="id_dis" id="id_dis" value="1"/>');
            $("#tutup").html('<button type="submit" class="btn btn-primary">Setuju</button>');
        } else {
            $("#input-disclaimer").html('<input type="hidden" name="id_dis" id="id_dis" value="0"/>');
            $("#tutup").html('');
        }
    }
  </script>
  @endif
  <script>
    $('#bulan').select2();
    $('#tahun').select2();
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
        var bulan = $('#bulan').val();
        var tahun = $('#tahun').val();
        var url = '{{route("patient.export", ["bulan" => ":bulan_id","tahun" => ":tahun_id"]) }}';
        url = url.replace('%3Abulan_id', bulan);
        url = url.replace('%3Atahun_id', tahun);
        url = url.replace('amp;tahun', 'tahun');
        $('#btn-export').append('<a href="'+url+'" target="_blank" class="btn btn-success btn-label waves-effect waves-light mb-4" data-text="Resume Pasien"><i class="ri-file-excel-line label-icon align-middle fs-16 me-2"></i>Resume Pasien</a>');
      load_data();
      function load_data(bulan = '', tahun = ''){
        let swalert;
        let table = $('#table_patient').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        // "dom": 'Blfrtip',
        // "buttons": [
        //     {
        //         "extend": 'excel',
        //         "text": '<i class="ri-file-excel-line me-1 align-middle"></i>  Export Excel',
        //         "titleAttr": 'Export Excel',
        //         "action": newexportaction
        //     },
        // ],
        ajax: {
            url:"{{ route('clinic.patient.index') }}",
            data:{bulan:bulan, tahun:tahun}
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: "text-center"
            },            
            {
            data: 'visit_date',
            name: 'visit_date',
            className: "text-center"
            },
            {
            data: 'id_employee',
            name: 'id_employee',
            className: "text-center"
            },           
            {
            data: 'diagnosa',
            name: 'diagnosa',
            className: "text-center"
            },
            {
            data: 'keluhan',
            name: 'keluhan',
            className: "text-center"
            },
            {
            data: 'tensi',
            name: 'tensi',
            className: "text-center"
            },
            {
            data: 'keterangan',
            name: 'keterangan',
            className: "text-center"
            },
            {
            data: 'id_dokter',
            name: 'id_dokter',
            className: "text-center"
            },
            {
            data: 'action',
            name: 'action',
            className: "text-center",
            orderable: false,
            searchable: false
            },
            {
            data: 'medicine',
            name: 'medicine',
            className: "none text-center"
            },
        ]
        });
      }
      $('#filter').click(function(){
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();
            $('#btn-export').html('');
            if(bulan != '' &&  tahun != '')
            {
                $('#table_patient').DataTable().destroy();
                load_data(bulan, tahun);

                var url = '{{route("patient.export", ["bulan" => ":bulan_id","tahun" => ":tahun_id"]) }}';
                url = url.replace('%3Abulan_id', bulan);
                url = url.replace('%3Atahun_id', tahun);
                url = url.replace('amp;tahun', 'tahun');
                $('#btn-export').append('<a href="'+url+'" target="_blank" class="btn btn-success btn-label waves-effect waves-light mb-4" data-text="Resume Pasien"><i class="ri-file-excel-line label-icon align-middle fs-16 me-2"></i>Resume Pasien</a>');
            }else{
                alert('bulan dan tahun tidak boleh kosong');
            }
        });
        $('#refresh').click(function(){
            var bulan =  {{ Js::from($month) }};
            var tahun =  {{ Js::from($year) }};
            $('#bulan').val(bulan).trigger('change');
            $('#tahun').val(tahun).trigger('change');
            $('#table_patient').DataTable().destroy();
            load_data();
            $('#btn-export').html('');

            var url = '{{route("patient.export", ["bulan" => ":bulan_id","tahun" => ":tahun_id"]) }}';
            url = url.replace('%3Abulan_id', bulan);
            url = url.replace('%3Atahun_id', tahun);
            url = url.replace('amp;tahun', 'tahun');
            $('#btn-export').append('<a href="'+url+'" target="_blank" class="btn btn-success btn-label waves-effect waves-light mb-4" data-text="Resume Pasien"><i class="ri-file-excel-line label-icon align-middle fs-16 me-2"></i>Resume Pasien</a>');
        });

      $(document).on("click", ".delete-btn", function() {
        var patientId = $(this).data("id");
        $("input[name='id']").val(patientId);
        $("#modal-delete").modal("show");
      });
        //export serverside
        // function newexportaction(e, dt, button, config) {
        //     var self = this;
        //     var oldStart = dt.settings()[0]._iDisplayStart;
        //     dt.one('preXhr', function (e, s, data) {
        //         // Just this once, load all data from the server...
        //         data.start = 0;
        //         data.length = 1000;
        //         dt.one('preDraw', function (e, settings) {
        //             // Call the original action function
        //             if (button[0].className.indexOf('buttons-copy') >= 0) {
        //                 $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
        //             } else if (button[0].className.indexOf('buttons-excel') >= 0) {
        //                 $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
        //                     $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
        //                     $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
        //             } else if (button[0].className.indexOf('buttons-csv') >= 0) {
        //                 $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
        //                     $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
        //                     $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
        //             } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
        //                 $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
        //                     $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
        //                     $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
        //             } else if (button[0].className.indexOf('buttons-print') >= 0) {
        //                 $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
        //             }
        //             dt.one('preXhr', function (e, s, data) {
        //                 // DataTables thinks the first item displayed is index 0, but we're not drawing that.
        //                 // Set the property to what it was before exporting.
        //                 settings._iDisplayStart = oldStart;
        //                 data.start = oldStart;
        //             });
        //             // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
        //             setTimeout(dt.ajax.reload, 0);
        //             // Prevent rendering of the full data to the DOM
        //             return false;
        //         });
        //     });
        //     // Requery the server with the new one-time export settings
        //     dt.ajax.reload();
        // }
    });
  </script>
  <script>
    $(".form-delete").submit(function(e) {
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
            swalert.then(() => window.location.reload() = response.redirect)
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
