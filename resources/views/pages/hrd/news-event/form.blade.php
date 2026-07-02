@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Select2-->
    <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Form News and Event</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">News and Event</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="card col-sm-10 mx-auto">
            <div class="card-header">
            <div class="row">
                <div class="col-lg-6">
                    <h5 class="card-title">News and Event</h5>
                </div>
                <div class="col-lg-6">
                    <a href="{{ route('news-and-event.index') }}" class="btn btn-primary btn-label btn-sm waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                </div>
            </div>
            </div>
            <form id="myForm" action="{{ route('news-and-event.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row g-3" >
                        <input type="hidden" name="id" id="id" value="{{ $news->id ?? '' }}">
                        <div class="col-4">
                            <label class="form-label">JUDUL<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-8">
                            <div class="form-group" >
                                <input type="text" class="form-control text-sm" id="judul" name="judul" placeholder="Masukkan judul" required value="{{ old('judul', $news->judul ?? '') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Tanggal News<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-8">
                            <div class="input-group">
                                <input type="text" name="tanggal_news" id="tanggal_news"
                                    class="form-control @error('tanggal_news') is-invalid @enderror"
                                    placeholder="Pilih Tanggal" data-provider="flatpickr" value="{{ old('tanggal_news', $news->tanggal_news ?? '') }}" required>
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label">DETAIL AND TUMBNAIL</label>
                            <br>
                            <br>
                            @if(!empty($news->tumbnail))
                                Tumbnail Sebelumnya :
                                <br>
                                <br>
                                <img src="/storage/tumbnail/{{ $news->tumbnail }}" class="img-thumbnail" alt="230x230" width="230px" onclick="window.open(this.src)">
                                <br>
                                <br>
                                Tumbnail Sekarang :
                                <br>
                                <br>
                                <a href="#" onclick="image_tumbnail (this)">                            
                                    <div id="image_tumbnail"></div>
                                </a>
                            @else
                                <a href="#" onclick="image_tumbnail (this)">                            
                                    <div id="image_tumbnail"></div>
                                </a>
                            @endif
                        </div>
                        <div class="col-8">
                            <div class="form-group" >
                                <textarea name="detail" id="doc_detail">{{ old('detail', $news->detail ?? '') }}</textarea>
                            </div>
                            <br>
                            <div class="input-group">
                                <input onchange="validateSize(this); tumbnailValidation(this);" type="file" class="form-control text-sm col-sm-6" aria-label="file example" name="tumbnail" id="tumbnail" accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                                <button class="btn btn-outline-danger" type="button" onclick="clearTumbnail()">Remove</button>
                            </div>
                            <span class="form-text">hanya menerima file bertipe .jpg | .jpeg | .png dan pastikan ukuran file tidak lebih dari 5MB.</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label">GAMBAR</label>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <div class="needsclick dropzone" id="document-dropzone">
                            </div>
                            @if(!empty($arr_gambar))
                                <br>
                                <center><label class="form-label">GAMBAR SEBELUMNYA</label></center>
                                <div class="row">
                                    @foreach($arr_gambar as $key => $val)
                                    <div class="col-lg-6">
                                        <img src="/storage/konten/{{ $val }}" class="img-thumbnail" alt="230x230" width="230px">
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label">LINK VIDEO</label>
                        </div>
                        <div class="col-8">
                            <div class="form-group" >
                                <input type="text" class="form-control text-sm" id="link_video" name="link_video" placeholder="Masukkan link video" value="{{ old('link_video', $news->link_video ?? '') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label">LAMPIRAN TAMBAHAN</label>
                        </div>
                        <div class="col-8">
                            <div class="input-group">
                                <input onchange="validateSize(this); lampiranValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="lampiran" id="lampiran" accept="application/pdf,application/PDF">
                                <button class="btn btn-outline-danger" type="button" onclick="clearLampiran()">Remove</button>
                            </div>
                            <span class="form-text">hanya menerima file bertipe .pdf dan pastikan ukuran file tidak lebih dari 5MB.</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <center>
                        <button type="submit" name="action" value="release" class="btn btn-secondary btn-label waves-effect waves-light rounded-pill"><i class="ri-global-line label-icon align-middle rounded-pill fs-16 me-2"></i> Release</button>
                        <!-- &nbsp;
                        <button type="submit" name="action" value="preview" formtarget="_blank" class="btn btn-info btn-label waves-effect waves-light rounded-pill"><i class="ri-eye-2-line label-icon align-middle rounded-pill fs-16 me-2"></i> Preview</button> -->
                        &nbsp;
                        <button type="submit" name="action" value="draft" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"><i class="ri-save-line label-icon align-middle rounded-pill fs-16 me-2"></i> Draft</button>
                    </center>
                </div>
            </form>
            <!-- staticBackdrop Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <lord-icon
                                src="https://cdn.lordicon.com/ulhdumaq.json"
                                trigger="loop"
                                style="width:120px;height:120px">
                            </lord-icon>
                            
                            <div class="mt-4">
                                <h4 class="mb-3">Please wait...</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Validation Size Upload -->
            <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <lord-icon
                                src="https://cdn.lordicon.com/tdrtiskw.json"
                                trigger="loop"
                                colors="primary:#f7b84b,secondary:#405189"
                                style="width:130px;height:130px">
                            </lord-icon>
                            <div class="mt-4 pt-4">
                                <h4>Whoops, ada yang salah!</h4>
                                <p class="text-muted">Ukuran file tidak boleh lebih dari 5MB.</p>
                                <!-- Toogle to second dialog -->
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Validation Extension File Upload Gambar -->
            <div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <lord-icon
                                src="https://cdn.lordicon.com/tdrtiskw.json"
                                trigger="loop"
                                colors="primary:#f7b84b,secondary:#405189"
                                style="width:130px;height:130px">
                            </lord-icon>
                            <div class="mt-4 pt-4">
                                <h4>Whoops, ada yang salah!</h4>
                                <p class="text-muted">Maaf hanya menerima file gambar yang bertipe .jpg | .jpeg | .png</p>
                                <!-- Toogle to second dialog -->
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Validation Extension File Upload Lampiran -->
            <div class="modal fade" id="thirdmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <lord-icon
                                src="https://cdn.lordicon.com/tdrtiskw.json"
                                trigger="loop"
                                colors="primary:#f7b84b,secondary:#405189"
                                style="width:130px;height:130px">
                            </lord-icon>
                            <div class="mt-4 pt-4">
                                <h4>Whoops, ada yang salah!</h4>
                                <p class="text-muted">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>
                                <!-- Toogle to second dialog -->
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
@endsection
@section('script')
    <!-- CKEditor 4-->  
    <script src="/assets/ckeditor/jquery.min.js"></script>
    <script src="/assets/ckeditor/ckeditor.js"></script>
    <!-- Select2 -->
    <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
  var uploadedDocumentMap = {}
  Dropzone.options.documentDropzone = {
    url: "{{ route('news-and-event.uploads') }}",
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    acceptedFiles: 'image/*',
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="images[]" value="' + response.name + '">')
      uploadedDocumentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDocumentMap[file.name]
      }
      $('form').find('input[name="images[]"][value="' + name + '"]').remove()
    },
    init: function () {
      @if(isset($project) && $project->document)
        var files =
          {!! json_encode($project->document) !!}
        for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="images[]" value="' + file.file_name + '">')
        }
      @endif
    }
  }

</script>
@endsection
@section('javascript')
    <script>
        $(function () {
            $('.select2').select2()
        });
        $('#tanggal_news').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });
    </script>
    <script>
        function validateSize(input) {
            const fileSize = input.files[0].size / 1024 / 1024; // in MiB
                if (fileSize > 5) {
                    $('#firstmodal').modal('show');
                    // alert('Maaf ukuran file lebih dari 5MB');
                    $(input).val(''); //direset
                } else {
                    // dijalankan
                }
        }

        function tumbnailValidation() {
            
            //foto tumbnail
            var tumbnail = document.getElementById('tumbnail');             
            var pathtumbnail = tumbnail.value;

            // tipe file yang diizinkan
            var allowedExtensions =
                    /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;
             
            //tumbnail modal
            if (!allowedExtensions.exec(pathtumbnail)) {
                $('#secondmodal').modal('show');
                // alert('Invalid file type');
                tumbnail.value = '';
                return false;
            }
            else
            {             
                // image preview
                if (tumbnail.files && tumbnail.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        //image
                        document.getElementById(
                            'image_tumbnail').innerHTML =
                            '<img src="' + e.target.result
                            + '" class="img-thumbnail" alt="230x230" width="230px"/>';
                        //reset
                        // document.getElementById(
                        //     'reset').innerHTML =
                        //     '<input type="button" value="Reset" onclick="clearResult()"/>';
                    };
                     
                    reader.readAsDataURL(tumbnail.files[0]);
                }                
            }            
        }
        //open in new tab image tumbnail
        function image_tumbnail(element){
            var newtab = window.open();
            setTimeout(function(){
                newtab.document.body.innerHTML = element.innerHTML;
            });

            return false;
        }
        //remove image tumbnail
        function clearTumbnail(){
            //reset image tumbnail
            document.getElementById("image_tumbnail").innerHTML = '';
            var tumbnail = document.getElementById('tumbnail');
            tumbnail.value = '';

        }
        function lampiranValidation() {
            //lampiran
            var lampiran = document.getElementById('lampiran');
            var pathLampiran = lampiran.value;
         
            // tipe file yang diizinkan
            var allowedExtensions =
                    /(\.pdf|\.PDF)$/i;
             
            //hasil modal
            if (!allowedExtensions.exec(pathLampiran)) {
                $('#thirdmodal').modal('show');
                // alert('Invalid file type');
                lampiran.value = '';
                return false;
            }
            else
            {             
                // dijalankan
            }
        }
        //remove lampiran
        function clearLampiran(){
            //reset lampiran            
            var lampiran = document.getElementById('lampiran');
            lampiran.value = '';
        }
    </script>    
    <script>
        //Form editor detail
        $(window).load(function() {
            CKEDITOR.replace( 'doc_detail',
            { 
                // toolbar :[['Undo','Redo','RemoveFormat'],['Bold', 'Italic', '-', 'NumberedList', 'BulletedList']]
                toolbarGroups: [{
                    "name": "basicstyles",
                    "groups": ["basicstyles"]
                    },
                    {
                    "name": "links",
                    "groups": ["links"]
                    },
                    {
                    "name": "paragraph",
                    "groups": ["list", "blocks"]
                    },
                    // {
                    //   "name": "document",
                    //   "groups": ["mode"]
                    // },
                    {
                    "name": "insert",
                    "groups": ["insert"]
                    },
                    {
                    "name": "styles",
                    "groups": ["styles"]
                    }
                    // ,
                    // {
                    //   "name": "about",
                    //   "groups": ["about"]
                    // }
                ],
                //   removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
            });
        });
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
