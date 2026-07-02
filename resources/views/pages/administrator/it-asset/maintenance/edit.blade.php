@extends('layouts.master')
@section('content')
   <div class="col">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0">Asset Maintenances</h4>

         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
               <li class="breadcrumb-item">Maintenances</li>
               <li class="breadcrumb-item active">Edit</li>
            </ol>
         </div>
      </div>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Maintenance Details</h5>
            <a href="{{ route('asset-maintenance.index') }}" class="btn btn-sm btn-primary">
               <i class="ri-arrow-go-back-line"></i> Back to List
            </a>
         </div>
         <div class="card-body">
            <x-asset-maintenance.form :maintenance="$maintenance" mode="edit"/>
         </div>
      </div>
   </div>
@endsection