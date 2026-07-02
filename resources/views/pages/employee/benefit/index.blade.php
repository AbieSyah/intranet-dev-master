@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .white-space-pre-line {
        white-space: pre-line;
    }
</style>
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-evenly">
                    <div class="col-lg-12">
                        <div class="mt-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0 me-1">
                                    <i class="ri-hand-coin-line fs-24 align-middle text-success me-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-16 mb-0 fw-semibold">My Benefits</h5>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 500px;">
                                <div class="row">
                                    @if($benefits->isNotEmpty())
                                        @foreach($benefits as $benefit)
                                            <div class="col-lg-6 col-md-12">
                                                <div class="d-flex mt-3">                                                    
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        @if(!empty($benefit->id_internal_rule))
                                                        
                                                            <!-- <i class="ri-link align-bottom me-1"></i> link available -->
                                                            <h6 class="mb-0">{{$benefit->benefit}}</h6>
                                                            {{--<a href="#" class="cek_rule" data-bs-toggle="modal" data-id="{{route('benefit.emp.rule', encrypt($benefit->id_internal_rule))}}" data-bs-target="#modal-benefit"><span class="badge badge-soft-success"><i class="ri-link align-bottom me-1"></i> Rule</span></a>--}}                                                                    
                                                            
                                                        @else
                                                        <h6 class="mb-0">{{$benefit->benefit}}</h6>
                                                        <!-- <a href="#" class="btn btn-danger btn-sm">
                                                            <i class="ri-link align-bottom me-1"></i> link not available
                                                        </a> -->
                                                        @endif
                                                        @if(!empty($benefit->value_nominal))
                                                        <div class="mt-1">{{'Rp '.number_format($benefit->value_nominal,2)}}</div>
                                                        @endif
                                                        @if(!empty($benefit->value_textual))
                                                        <div class="mt-1 white-space-pre-line">{{$benefit->value_textual}}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        @endforeach
                                    @endif        
                                </div>
                                <br>
                                <br>
                            </div>
                            <!--end row-->
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div>                   
    <!--end col-->
</div>
<!--end row-->
<!--modal preview rules-->
<div class="modal flip" id="modal-benefit" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="show-rule">
                </div>    
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- simplebar -->
<script src="/assets/libs/simplebar/simplebar.min.js"></script>
@endsection
@section('javascript')
<script>
    $('.cek_rule').on("click", function() {
        var data_rule = $(this).data("id");
        $("#show-rule").html('<iframe src="'+data_rule+'#toolbar=0" frameborder="0" style="height:500px; width:100%;"></iframe>');
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
