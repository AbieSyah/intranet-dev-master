@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<!-- costume css -->
<link rel="stylesheet" type="text/css" href="{{asset('assets/flip/css/flipbook.style.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/flip/css/font-awesome.css')}}">
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                    <div class="hstack gap-2 mb-2">
                        <button id="read" type="button" class="btn btn-info btn-label btn-sm waves-effect waves-light"><i class="ri-book-open-line label-icon align-middle fs-16 me-2"></i> E-book</button>
                        <!-- <a class="btn btn-danger btn-label btn-sm waves-effect waves-light" href="{{ route('profile.pkb.download') }}"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Download</a> -->
                    </div>
                    <iframe src="{{$url_pkb}}#toolbar=0" frameborder="0" style="height:500px; width:100%;"></iframe>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<!-- Include JS -->
<script src="{{asset('assets/flip/js/flipbook.min.js')}}"></script>
@endsection
@section('javascript')
<script>
    var preview = {{ Js::from($url_pkb) }};
    $('#read').flipBook({
        //Layout Setting
        pdfUrl:preview,
        lightBox:true,
        layout:3,
        currentPage:{vAlign:"bottom", hAlign:"left"},
        // BTN SETTING
        btnShare : {enabled:false},
        btnPrint : {
            enabled: false,
            hideOnMobile:true
        },
        btnDownloadPages : {
            enabled: false,
            title: "Download pages",
            icon: "fa-download",
            icon2: "file_download",
            url: preview,
            name: "allPages.zip",
            hideOnMobile:false
        },
        btnDownloadPdf : {
            enabled: false
        },
        btnAutoplay: {
            enabled: false
        },
        btnBookmark: {
            enabled: false
        },
        btnSound: {
            enabled: false
        },
        btnThumbs: {
            enabled: false
        },
        btnColor:'red',
        sideBtnColor:'rgb(255,120,60)',
        sideBtnSize:40,
        sideBtnBackground:"rgba(0,0,0,.7)",
        sideBtnRadius:40,
        btnSound:{vAlign:"top", hAlign:"left"},
        btnAutoplay:{vAlign:"top", hAlign:"left"}                
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
