@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">IT Service Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">IT Service Management</a></li>
                        <li class="breadcrumb-item active">IT Initiative</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Create IT Initiative</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light">
                        <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <x-service-desk.form it_initiative="true" />
                </div>
            </div>
        </div>
    </div>
@endsection
