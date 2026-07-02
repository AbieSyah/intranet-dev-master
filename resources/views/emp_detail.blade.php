@extends('layouts.general')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- start page -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                <h4 class="card-title">{{$news->judul}}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <a href="{{ route('home') }}" class="btn btn-primary btn-label btn-sm waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                                </div>
                            </div>
                            @if(!empty($arr_konten))
                            <div class="row mt-4">
                                @if(count($arr_konten) > 3)                        
                                <!-- Swiper -->
                                <div class="swiper effect-coverflow-swiper rounded pb-5">
                                    <div class="swiper-wrapper">
                                        @foreach($arr_konten as $key => $val)
                                        <div class="swiper-slide">
                                            <img src="{{asset('storage/konten/'.$val)}}" alt="" class="img-fluid" />
                                        </div>
                                        @endforeach
                                        <!-- <div class="swiper-slide">
                                            <img src="/assets/images/small/img-4.jpg" alt="" class="img-fluid" />
                                        </div>
                                        <div class="swiper-slide">
                                            <img src="/assets/images/small/img-5.jpg" alt="" class="img-fluid" />
                                        </div>
                                        <div class="swiper-slide">
                                            <img src="/assets/images/small/img-6.jpg" alt="" class="img-fluid" />
                                        </div>
                                        <div class="swiper-slide">
                                            <img src="/assets/images/small/img-7.jpg" alt="" class="img-fluid" />
                                        </div>
                                        <div class="swiper-slide">
                                            <img src="/assets/images/small/img-8.jpg" alt="" class="img-fluid" />
                                        </div>
                                        <div class="swiper-slide">
                                            <img src="/assets/images/small/img-9.jpg" alt="" class="img-fluid" />
                                        </div> -->
                                    </div>
                                    <div class="swiper-pagination swiper-pagination-dark"></div>
                                </div>
                                @else
                                <!-- Swiper -->
                                <div style="max-width: 30%; and height: auto;">
                                    <div class="swiper pagination-dynamic-swiper rounded">
                                        <div class="swiper-wrapper">
                                            @foreach($arr_konten as $key => $val)
                                            <div class="swiper-slide">
                                                <img src="{{asset('storage/konten/'.$val)}}" alt="" class="img-fluid" />
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination dynamic-pagination"></div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
                
                            <div class="row mt-4">
                                <div class="row">
                                    <div class="col-lg-12">                                    
                                        <div class="card-text text-muted">{!! $news->detail !!}</div>
                                    </div>
                                </div>
                                @if(!empty($news->link_video))
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <!-- Custom Ratio Video -->
                                        <div style="max-width: 50%; and height: auto;">
                                            <div class="ratio" style="--vz-aspect-ratio: 60%;">
                                            <video controls width="360" height="240">
                                                <source src="{{$news->link_video}}" type='video/mp4'/>
                                            </video>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(!empty($news->lampiran))
                                <div class="row mt-4">
                                    <div class="col-lg-4">
                                        <div class="border rounded border-dashed p-2">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-sm">
                                                        <div class="avatar-title bg-light text-secondary rounded fs-24">
                                                            <i class="ri-file-pdf-line"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="fs-13 mb-1"><span href="#" class="text-body text-truncate d-block">DOCUMENT</span></h5>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <div class="d-flex gap-1">
                                                        <a href="{{$lampiran}}" target="_blank" class="btn btn-icon text-muted btn-sm fs-18"><i class="ri-eye-2-line"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                     
                                    </div>
                                </div> 
                                @endif             
                            </div>
                        </div>
                        <!--end card-body-->
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
<!--Swiper slider js-->
<script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

<!-- swiper.init js -->
<script src="/assets/js/pages/swiper.init.js"></script>
@endsection
@section('javascript')
<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
    $(document).ready(() => {
        $('video').attr('controlsList', 'nodownload');
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
