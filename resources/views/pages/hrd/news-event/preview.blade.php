@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .description {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">News and Event</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">News and Event</a></li>
              <li class="breadcrumb-item active">Preview</li>
          </ol>
      </div>

    </div>
  </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="row">                
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="card-title">DRAFT</h5>
                            </div>
                            <div class="col-lg-6">
                                <a href="{{ route('news-and-event.index') }}" class="btn btn-primary btn-label btn-sm waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                            </div>
                        </div>
                        @if(!empty($news))
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img class="rounded-start img-fluid h-100 object-cover" src="{{asset('storage/tumbnail/'.$news->tumbnail)}}" alt="Card image">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{$news->judul}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-text mb-2 description">
                                            {!! $news->detail !!}
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('news-and-event.preview.detail', encrypt($news->id)) }}" class="link-primary fw-medium">Read More <i class="ri-arrow-right-line align-middle"></i></a>
                                        </div>
                                        @if(!empty($news->tanggal_news))
                                        <p class="card-text"><small class="text-muted">{{date('d M Y', strtotime($news->tanggal_news))}}</small></p>
                                        @else
                                        <p class="card-text"><small class="text-muted">-</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else                      
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                    trigger="loop" colors="primary:#405189,secondary:#0ab39c"
                                    style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted">The information you are looking for was not found.</p>
                            </div>                           
                        @endif
                    </div>
                    <!--end card-body-->
                </div><!-- end card -->
            </div>
            <!--end col-->
            <div class="col-lg-3">
                <h5 class="mb-1">Upcoming Events</h5>
                <p class="text-muted">Don't miss scheduled events</p>
                @if(!empty($data_all))
                <div data-simplebar style="height: 90%;">
                    @foreach($data_all as $key => $value)
                        @if($value['start'] >= $date_now)
                            @if($value['className'] == 'bg-soft-success border-success')
                                <div class='card ribbon-box border ribbon-fill shadow-none right mb-lg-3'>
                                    <div class='card-body'>
                                        <div class="ribbon ribbon-primary">New</div>
                                        <div class='d-flex mb-3'>
                                            <div class='flex-grow-1'>
                                                <i class='mdi mdi-checkbox-blank-circle me-2 text-primary'></i>
                                                <span class='fw-medium'>{{$value['dateup']}}</span>
                                            </div>                                
                                            <div class='flex-shrink-0'></div>                            
                                        </div>                            
                                        <h6 class='card-title fs-14'>{{$value['title']}}</h6>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
                @endif
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
@endsection

@section('javascript')
@endsection
