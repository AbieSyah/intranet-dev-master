@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<!-- Calendar Scheduler -->
<link href="/assets/libs/fullcalendar/scheduler/main.min.css" rel="stylesheet"/>
<style>
    #calendar {
        max-width: 1100px;
        margin: 40px auto;
    }
    .fc .fc-scrollgrid-section-header.fc-scrollgrid-section-sticky>* {
        /* top: 0px; */
    }

    .fc-timegrid-slot {
        height: 30px !important
    }

    .fc-event-time,.fc-event-title{
        white-space: normal !important;
    }
    .vr{ 
        border:         none;
        border-left:    1px solid hsla(200, 10%, 50%,100);
        height:         27vh;
        width:          1px;       
    }

    table, td {
        border: 0px solid black;
    }
    td.first {
        width: 10%;
    }
    td.second {
        width: 1%;
    }
    td.three {
        width: 50%;
    }
</style>
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
                            <div class="col-lg-3 mb-2">
                                <div class="input-group">
                                    <input type="text" name="tanggal" id="tanggal"
                                        class="form-control bulan"
                                        placeholder="Pilih Tanggal" value="" required>
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                            <a href="{{ route('booking-room.emp.index') }}" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-refresh-line label-icon align-middle me-2"></i> Refresh</a>
                            </div>
                            <div class="col-lg-3 ms-auto">
                                <div class="float-end">
                                    <span class="badge fs-14 text-bg-info">External</span>
                                    <span class="badge fs-14 text-bg-success">Internal</span>
                                </div>
                            </div>
                        </div>
                        <div id='calendar'></div>                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->
<!-- modal add room calendar -->
<div id="modal-edit-calendar" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title">Add Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>                
            <form id="Form-add" action="{{ route('booking-room.emp.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input class="form-control" type="text" name="brief_description" id="brief_description" required value="" />
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Detail</label>
                            <textarea class="form-control" name="full_description" id="full_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Start</label>
                                    <div class="input-group">
                                        <input type="text" name="start_date" id="start_date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-select" name="start_time" id="start_time">
                                        <option value="07:00">07:00</option>
                                        <option value="07:30">07:30</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:30">17:30</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:30">18:30</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">End</label>
                                    <div class="input-group">
                                        <input type="text" name="end_date" id="end_date"
                                            class="form-control @error('end_date') is-invalid @enderror"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                            <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-select" name="end_time" id="end_time">
                                        <option value="07:00">07:00</option>
                                        <option value="07:30">07:30</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:30">17:30</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:30">18:30</option>
                                        <option value="19:00">19:00</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Rooms</label>
                            <select class="form-select" name="room" id="room">
                                @foreach($data_room as $key => $val_room)
                                    <option value="{{$val_room['id']}}">{{$val_room['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <!-- <select class="form-select" name="tipe" id="tipe">
                                <option value="internal">INTERNAL</option>
                                <option value="external">EXTERNAL</option>
                            </select> -->
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="cek_type" id="internal" value="internal" required>
                                        <label class="form-check-label" for="internal">
                                            Internal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="cek_type" id="external" value="external" required>
                                        <label class="form-check-label" for="external">
                                            External
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mb-3">
                        <div class="row">
                            <label class="form-label">Confirmation Status</label>
                            <div class="col-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cek_status" id="tentative" value="1" required>
                                    <label class="form-check-label" for="tentative">
                                        Tentative
                                    </label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cek_status" id="confirmed" value="0" required>
                                    <label class="form-check-label" for="confirmed">
                                        Confirmed
                                    </label>
                                </div>
                            </div>
                        </div>   
                    </div> -->
                    <hr>
                    <div class="mb-3">
                        <div class="row">
                            <label class="form-label">Repeat Type</label>
                            <div class="col-3">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cek_repeat" id="none" value="none" required>
                                            <label class="form-check-label" for="none">
                                                None
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cek_repeat" id="daily" value="daily" required>
                                            <label class="form-check-label" for="daily">
                                                Daily
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cek_repeat" id="weekly" value="weekly" required>
                                            <label class="form-check-label" for="weekly">
                                                Weekly
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cek_repeat" id="monthly" value="monthly" required>
                                            <label class="form-check-label" for="monthly">
                                                Monthly
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 5px;">
                                <div class="vr"></div>
                            </div>
                            <div class="col-8">
                                <div id="form-weekly">
                                    <label class="form-label">Repeat day :</label>
                                    <div class="row">        
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Sunday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Sun
                                                </label>
                                            </div>                                        
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Monday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Mon
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Tuesday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Tue
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Wednesday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Wed
                                                </label>
                                            </div>
                                        </div>                                    
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Thursday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Thu
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Friday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Fri
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="cek_repeat_day[]" id="repeat_day" value="Saturday">
                                                <label class="form-check-label" for="repeat_day">
                                                    Sat
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="form-label">Repeat every weeks:</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="input-step">
                                                <button type="button" class="minus">–</button>
                                                <input type="number" class="product-quantity" name="cek_repeat_week" id="cek_repeat_week" value="1" min="0"
                                                    max="100" readonly>
                                                <button type="button" class="plus">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="form-monthly">
                                    <label class="form-label">On day :</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <select class="form-select" name="on_day" id="on_day">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25</option>
                                                <option value="26">26</option>
                                                <option value="27">27</option>
                                                <option value="28">28</option>
                                                <option value="29">29</option>
                                                <option value="30">30</option>
                                                <option value="31">31</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                    <div id="tgl_repeat">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Repeat end date</label>
                                <div class="input-group">
                                    <input type="text" name="repeat_date" id="repeat_date"
                                        class="form-control @error('repeat_date') is-invalid @enderror"
                                        placeholder="Pilih Tanggal" value="" required>
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ">Submit</button>
                </div> 
            </form>                                 
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- modal add room calendar -->
<div id="flipModalViewPlan" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title"><span id="judul-modal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>                
                <div class="modal-body">
                    <div class="text-end">
                        <button type="button" id="update_id" data-bs-target="#flipModalUpdate" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-warning">Edit Entry</button>
                        <button type="button" id="delete_id" data-bs-target="#flipModalDelete" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-danger">Delete Entry</button>
                        <button type="button" id="update_seris_id" data-bs-target="#flipModalUpdateSeris" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-secondary">Edit Series</button>
                        <button type="button" id="delete_seris_id" data-bs-target="#flipModalDeleteSeris" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-dark">Delete Series</button>
                    </div>
                    <table style="width:100%">
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Start time
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="ri-time-line text-muted fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="d-block mb-0"
                                                id="view_start_time"></h6>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>                                
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Duration
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-timer-2-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="d-block fw-semibold mb-0">
                                            <span id="view_duration"></span>
                                        </h6>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        End time
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-time-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="d-block fw-semibold mb-0"> <span
                                                id="view_end_time"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Room
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-home-8-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="d-block fw-semibold mb-0"> <span
                                                id="view_room"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Last updated
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-history-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="d-block fw-semibold mb-0"> <span
                                        id="view_last_updated"></span></h6>
                                    </div>
                                </div>
                            </td>                                    
                        </tr>
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Created by
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-user-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        <h6 class="d-block fw-semibold mb-0"><span
                                                id="view_created_by"></span></h6>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{--<tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Modified by
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-history-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        <h6 class="d-block fw-semibold mb-0">
                                            <span id="view_modified_by"></span>
                                        </h6>
                                    </div>
                                </div>
                            </td>
                        </tr>--}}
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Type
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-todo-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        <h6 class="d-block fw-semibold mb-0">
                                            <span id="view_type"></span>
                                        </h6>
                                    </div>
                                </div>
                            </td>
                        </tr>                                
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Detail
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-todo-line text-muted fs-16"></i>
                                    </div>                                            
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        <h6 class="d-block fw-semibold mb-0"><span
                                                id="view_description"></span></h6>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="first">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        Repeat type
                                    </div>
                                </div>
                            </td>
                            <td class="second">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                        :
                                    </div>
                                </div>
                            </td>
                            <td class="three">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="ri-todo-line text-muted fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="d-block mb-0"
                                                id="view_repeat_type"></h6>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>                             
                    </table>                                                
                </div>
                <div class="modal-footer">
                </div>                                  
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal add room calendar -->

<!-- Modal update room calendar -->
<div id="flipModalUpdate" class="modal fade" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="Form-update" action="{{ route('booking-room.emp.update') }}" method="post">
                @csrf
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title">Edit <span id="judul-modal-edit"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-end">
                        <button type="button" data-bs-target="#flipModalViewPlan" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-primary">Cancel</button>
                    </div>
                    <input type="hidden" name="id_edit_booking" id="id_edit_booking" value="">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input class="form-control" type="text" name="edit_brief_description" id="edit_brief_description" required value="" />
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Detail</label>
                            <textarea class="form-control" name="edit_full_description" id="edit_full_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Start</label>
                                    <div class="input-group">
                                        <input type="text" name="edit_start_date" id="edit_start_date"
                                            class="form-control"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-select" name="edit_start_time" id="edit_start_time">
                                        <option value="07:00">07:00</option>
                                        <option value="07:30">07:30</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:30">17:30</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:30">18:30</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">End</label>
                                    <div class="input-group">
                                        <input type="text" name="edit_end_date" id="edit_end_date"
                                            class="form-control"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                            <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-select" name="edit_end_time" id="edit_end_time">
                                        <option value="07:00">07:00</option>
                                        <option value="07:30">07:30</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:30">17:30</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:30">18:30</option>
                                        <option value="19:00">19:00</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Rooms</label>
                            <select class="form-select" name="edit_room" id="edit_room">
                                @foreach($data_room as $key => $val_room)
                                    <option value="{{$val_room['id']}}">{{$val_room['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <!-- <select class="form-select" name="edit_tipe" id="edit_tipe">
                                <option value="internal">INTERNAL</option>
                                <option value="external">EXTERNAL</option>
                            </select> -->
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="cek_edit_type" id="edit_internal" value="internal" required>
                                        <label class="form-check-label" for="edit_internal">
                                            Internal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="cek_edit_type" id="edit_external" value="external" required>
                                        <label class="form-check-label" for="edit_external">
                                            External
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div id="flipModalUpdateSeris" class="modal fade" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="Form-update-series" action="{{ route('booking-room.emp.update.series') }}" method="post">
                @csrf
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title">Edit Series <span id="judul-modal-edit-series"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-end">
                        <button type="button" data-bs-target="#flipModalViewPlan" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-primary">Cancel</button>
                    </div>
                    <input type="hidden" name="id_edit_series" id="id_edit_series" value="">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input class="form-control" type="text" name="edit_series_brief_description" id="edit_series_brief_description" required value="" />
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Detail</label>
                            <textarea class="form-control" name="edit_series_full_description" id="edit_series_full_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Start</label>
                                    <div class="input-group">
                                        <input type="text" name="edit_series_start_date" id="edit_series_start_date"
                                            class="form-control"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-select" name="edit_series_start_time" id="edit_series_start_time">
                                        <option value="07:00">07:00</option>
                                        <option value="07:30">07:30</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:30">17:30</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:30">18:30</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">End</label>
                                    <div class="input-group">
                                        <input type="text" name="edit_series_end_date" id="edit_series_end_date"
                                            class="form-control"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                            <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-select" name="edit_series_end_time" id="edit_series_end_time">
                                        <option value="07:00">07:00</option>
                                        <option value="07:30">07:30</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:30">17:30</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:30">18:30</option>
                                        <option value="19:00">19:00</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Rooms</label>
                            <select class="form-select" name="edit_series_room" id="edit_series_room">
                                @foreach($data_room as $key => $val_room)
                                    <option value="{{$val_room['id']}}">{{$val_room['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>   
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <!-- <select class="form-select" name="edit_series_tipe" id="edit_series_tipe">
                                <option value="internal">INTERNAL</option>
                                <option value="external">EXTERNAL</option>
                            </select> -->
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="cek_edit_series_type" id="edit_series_internal" value="internal" required>
                                        <label class="form-check-label" for="edit_series_internal">
                                            Internal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="cek_edit_series_type" id="edit_series_external" value="external" required>
                                        <label class="form-check-label" for="edit_series_external">
                                            External
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="row">
                                <label class="form-label">Repeat Type</label>
                                <div class="col-3">
                                    <div class="row" id="tipe-daily">
                                        <div class="col-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cek_series_repeat" id="series_daily" value="daily" required>
                                                <label class="form-check-label" for="series_daily">
                                                    Daily
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="tipe-weekly">
                                        <div class="col-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cek_series_repeat" id="series_weekly" value="weekly" required>
                                                <label class="form-check-label" for="series_weekly">
                                                    Weekly
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="tipe-monthly">
                                        <div class="col-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cek_series_repeat" id="series_monthly" value="monthly" required>
                                                <label class="form-check-label" for="series_monthly">
                                                    Monthly
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="width: 5px;">
                                    <div class="vr"></div>
                                </div>
                                <div class="col-8">
                                    <div id="series-weekly">
                                        <label class="form-label">Repeat day :</label>
                                        <div class="row">        
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Sunday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Sun
                                                    </label>
                                                </div>                                        
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Monday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Mon
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Tuesday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Tue
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Wednesday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Wed
                                                    </label>
                                                </div>
                                            </div>                                    
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Thursday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Thu
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Friday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Fri
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input repeat-day" type="checkbox" name="cek_series_repeat_day[]" id="series_repeat_day" value="Saturday">
                                                    <label class="form-check-label" for="series_repeat_day">
                                                        Sat
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="form-label">Repeat every weeks:</label>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="input-step">
                                                    <button type="button" class="minus">–</button>
                                                    <input type="number" class="product-quantity" name="cek_series_repeat_week" id="cek_series_repeat_week" value="1" min="0"
                                                        max="100" readonly>
                                                    <button type="button" class="plus">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="series-monthly">
                                        <label class="form-label">On day :</label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-select" name="series_on_day" id="series_on_day">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                    <option value="24">24</option>
                                                    <option value="25">25</option>
                                                    <option value="26">26</option>
                                                    <option value="27">27</option>
                                                    <option value="28">28</option>
                                                    <option value="29">29</option>
                                                    <option value="30">30</option>
                                                    <option value="31">31</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Repeat end date</label>
                            <div class="input-group">
                                <input type="text" name="repeat_series_date" id="repeat_series_date"
                                    class="form-control @error('repeat_series_date') is-invalid @enderror"
                                    placeholder="Pilih Tanggal" value="" required>
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal update room calendar -->

<!-- Modal konfirmasi delete -->
<div id="flipModalDelete" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form id="form-delete" action="{{ route('booking-room.emp.delete') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="delete-modal"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" name="id_delete_booking" id="id_delete_booking">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" id="submitDelete" class="btn btn-danger">Ya</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div id="flipModalDeleteSeris" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form id="form-delete-series" action="{{ route('booking-room.emp.delete.series') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="delete-modal-series"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" name="id_delete_series" id="id_delete_series">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-danger">Ya</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal konfirmasi delete -->
@endsection
@section('script')
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Calendar Scheduler -->
<script src="/assets/libs/fullcalendar/scheduler/main.min.js"></script>
<!-- sweetalert2 -->
<script src="/assets/libs/sweetalert2/11/sweetalert2.min.js"></script>
@endsection
@section('javascript')
<script>
    $('#form-weekly').hide();
    $('#form-monthly').hide();
    $('#tgl_repeat').hide();
    $("#none").prop('checked', true);
    $('.repeat-day').prop('checked', false);
    
    $("input[name='cek_repeat']").click(function() {
        var cek_repeat = this.value;
        if(cek_repeat == 'weekly'){
            //show or hide
            $('#form-weekly').show();
            $('#form-monthly').hide();
            $('#tgl_repeat').show();
        }else if(cek_repeat == 'monthly'){
            $('#form-weekly').hide();
            $('#form-monthly').show();
            $('#tgl_repeat').show();
        }else if(cek_repeat == 'daily'){
            $('#form-weekly').hide();
            $('#form-monthly').hide();
            $('#tgl_repeat').show();
        }else{
            $('#form-weekly').hide();
            $('#form-monthly').hide();
            $('#tgl_repeat').hide();

        }
    });

    $("#modal-edit-calendar").on("hidden.bs.modal", function(){
        $("input[name='brief_description']").val('');
        document.getElementsByName('full_description')[0].value = '';            
        $('#tipe').val('internal').trigger('change');
        $("input[name='cek_status']").prop('checked', false);
        $("#none").prop('checked', true);
        $('#form-weekly').hide();
        $('#form-monthly').hide();
        $("input[name='cek_repeat_day']").prop('checked', false);
        $("input[name='cek_repeat_week']").val('1');
        $('#on_day').val('1').trigger('change');
        $("input[name='repeat_date']").val('');
        $('#tgl_repeat').hide();        
    });
</script>
<script>
    $(function () {    
        $('.select2').select2();
    });
    $('#start_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 
    $('#edit_start_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 
    $('#edit_end_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 
    $('#end_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 
    $('#tanggal').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });       
</script>
<script>
    //submit update event
    $("#Form-add").submit(function(e) {
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
        errorMessage += `<p class="text-danger">${responseJson.message}</p>`;
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
    //submit update event
    $("#Form-update-series").submit(function(e) {
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
    //submit update event
    $("#Form-update").submit(function(e) {
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
    //submit delete event
    $("#form-delete-series").submit(function(e) {
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
    //submit delete event
    $("#form-delete").submit(function(e) {
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
    var rooms = {{ Js::from($data_room) }}; 
    var bookings = {{ Js::from($data_booking) }};
    
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            titleFormat: { 
                month: 'long',
                year: 'numeric',
                day: 'numeric',
                weekday: 'long'
            },
            selectable: true,
            allDaySlot: false,
            slotMinTime: "07:00:00",
            slotMaxTime: "19:00:00",
            timeZone: 'local',
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            initialView: 'resourceTimeGridDay',
            headerToolbar : {
                left:   'title',
                center: '',
                right:  'today prev next'
            },
            customButtons: {
                prev: {
                    text: '<',
                    click: function() {
                        calendar.prev();
                        var view = calendar.view;
                        let datenow = new Date().toISOString(view.title).slice(0, 10)
                        // alert("The view's title is " + view.title);
                        document.getElementById("tanggal").value = '';
                    }
                },
                next: {
                    text: '>',
                    click: function() {
                        calendar.next();
                        var view = calendar.view;
                        let datenow = new Date().toISOString(view.title).slice(0, 10)
                        // alert("The view's title is " + view.title);
                        document.getElementById("tanggal").value = '';
                    }
                },
                today: {
                    text: 'Today',
                    click: function() {
                        calendar.today();
                        var view = calendar.view;
                        let datenow = new Date().toISOString(view.title).slice(0, 10)
                        // alert("The view's title is " + datenow);
                        document.getElementById("tanggal").value = datenow;

                    }
                }
            },
            slotLabelFormat: [
            {
                hour: '2-digit',
                minute: '2-digit',
                hour12:false
            }
            ],
            resources: rooms,
            events: bookings,
            height: 'auto', // will activate stickyHeaderDates automatically!
            // slotDuration: '00:05:00', // very small slots will make the calendar really tall
            dayMinWidth: 100, // will cause horizontal scrollbars
            select: function(info) {
                $('#modal-edit-calendar').modal('show', true);
                // alert('selected ' + info.startStr + ' to ' + info.endStr);
                function addZero(i) {
                    if (i < 10) {i = "0" + i}
                    return i;
                }
                //date start
                var date_start = new Date(info.startStr);

                var year_start = date_start.getFullYear();
                var month_start = addZero(date_start.getMonth() + 1);
                var month_next = addZero(date_start.getMonth() + 3);
                var day_start = addZero(date_start.getDate());
                var hours_start = addZero(date_start.getHours());
                var minutes_start = addZero(date_start.getMinutes());
                var seconds_start = addZero(date_start.getSeconds());

                var startDate = year_start + "-" + month_start + "-" + day_start + " " + hours_start + ":" + minutes_start + ":" + seconds_start;
                var nextMonthDate = year_start + "-" + month_next + "-" + day_start + " " + hours_start + ":" + minutes_start + ":" + seconds_start;
                //date end
                var date_end = new Date(info.endStr);

                date_end.setMinutes(date_end.getMinutes()+30);

                var year_end = date_end.getFullYear();
                var month_end = addZero(date_end.getMonth() + 1);
                var day_end = addZero(date_end.getDate());
                var hours_end = addZero(date_end.getHours());
                var minutes_end = addZero(date_end.getMinutes());
                var seconds_end = addZero(date_end.getSeconds());

                var endDate = year_end + "-" + month_end + "-" + day_end + " " + hours_end + ":" + minutes_end + ":" + seconds_end;
                // console.log('selected ' + info.startStr + ' to ' + info.endStr + 'room id = ' + info.resource.title);
                // console.log('selected ' + startDate + ' to ' + endDate + ' room id = ' + info.resource.title);
                
                $('#start_date').val(year_start + "-" + month_start + "-" + day_start);
                $('#end_date').val(year_end + "-" + month_end + "-" + day_end);
                
                $('#start_time').find('option[value="'+hours_start + ":" + minutes_start +'"]').prop('selected', true);
                $('#end_time').find('option[value="'+hours_end + ":" + minutes_end +'"]').prop('selected', true);
                
                $('#room').find('option[value="'+ info.resource.id + '"]').prop('selected', true);
                $('#repeat_date').val(year_end + "-" + month_end + "-" + day_end);
                // for(var i = hours_start; i<= 19; i++){
                //     if(minutes_start < 30){
                //         console.log(i)   
                //     }else{
                //         console.log(i)   
                //     }
                // }

                //form repeat date
                $('#repeat_date').flatpickr({
                    allowInput: true,
                    altInput: false,
                    altFormat: "d F, Y",
                    dateFormat: "Y-m-d",
                    minDate: startDate,
                    maxDate: nextMonthDate
                });                
                
            },
            eventClick: function(e) {
                $('#flipModalViewPlan').modal('show', true);
                console.log('selected =' + e.event.id);
                document.getElementById("judul-modal").innerHTML = e.event.title;
                document.getElementById("judul-modal-edit").innerHTML = e.event.title;
                document.getElementById("delete-modal").innerHTML = e.event.title;
                document.getElementById("judul-modal-edit-series").innerHTML = e.event.title;                             
                document.getElementById("delete-modal-series").innerHTML = e.event.title;
                
                var id_event = e.event.id;
                //get ajax table booking_record
                $.ajax({
                    url: "{{ route('booking-room.emp.view') }}",
                    type: "POST",
                    data: {
                        id_event: id_event,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        // console.log(result)
                        var user = {{ Js::from($user->employee_id) }};
                        //get produk
                        // var id_produk = result.produk_id;
                        // var nama_produk = result.produk_nama;
                        // var kode_produk = result.produk_kode;
                        //view entry
                        document.getElementById("view_start_time").innerHTML = result.time_start+" - "+result.date_start;
                        document.getElementById("view_end_time").innerHTML = result.time_end+" - "+result.date_end;
                        document.getElementById("view_duration").innerHTML = result.duration+" hours";
                        document.getElementById("view_room").innerHTML = result.room;
                        document.getElementById("view_last_updated").innerHTML = result.time_last_update+" - "+result.date_last_update;
                        document.getElementById("view_type").innerHTML = result.tipe;
                        document.getElementById("view_created_by").innerHTML = result.create_by;
                        document.getElementById("view_description").innerHTML = result.description;
                        document.getElementById("view_repeat_type").innerHTML = result.repeat_status;
                        if(result.employee_id == user){
                            $("#update_id").prop('hidden', false);
                            $("#delete_id").prop('hidden', false);
                            if(result.repeat_status == 'None'){
                                $("#update_seris_id").prop('hidden', true);
                                $("#delete_seris_id").prop('hidden', true);
                            }else{
                                $("#update_seris_id").prop('hidden', false);
                                $("#delete_seris_id").prop('hidden', false);
                            }
                        }else{
                            $("#update_id").prop('hidden', true);
                            $("#update_seris_id").prop('hidden', true);
                            $("#delete_id").prop('hidden', true);
                            $("#delete_seris_id").prop('hidden', true);
                        }
                        //edit entry
                        document.getElementById("id_edit_booking").value = result.id_edit_booking;
                        document.getElementById("edit_brief_description").value = result.edit_brief_description;
                        document.getElementById("edit_full_description").value = result.edit_full_description;
                        document.getElementById("edit_start_date").value = result.edit_start_date;
                        document.getElementById("edit_end_date").value = result.edit_end_date;
                        $('#edit_start_time').val(result.edit_start_time).trigger('change');
                        $('#edit_end_time').val(result.edit_end_time).trigger('change');
                        $('#edit_room').val(result.edit_room).trigger('change');
                        if(result.edit_tipe == 'internal'){
                            $('input[value="internal"]').prop('checked', true);
                            $('input[value="external"]').prop('checked', false);
                            // $('#edit_tipe').val(result.edit_tipe).trigger('change');
                        }else{
                            $('input[value="internal"]').prop('checked', false);
                            $('input[value="external"]').prop('checked', true);
                        }
                        //delete entry                        
                        document.getElementById("id_delete_booking").value = result.id_delete_booking;
                        //delete series                        
                        document.getElementById("id_delete_series").value = result.kode;
                        //edit series
                        document.getElementById("id_edit_series").value = result.kode;
                        document.getElementById("edit_series_brief_description").value = result.edit_brief_description;
                        document.getElementById("edit_series_full_description").value = result.edit_full_description;
                        document.getElementById("edit_series_start_date").value = result.series_start_date;
                        document.getElementById("edit_series_end_date").value = result.series_start_date;
                        $('#edit_series_start_time').val(result.series_start_time).trigger('change');
                        $('#edit_series_end_time').val(result.series_end_time).trigger('change');
                        $('#edit_series_room').val(result.edit_room).trigger('change');
                        $('#edit_series_tipe').val(result.edit_tipe).trigger('change');
                        $('#repeat_series_date').val(result.last_date_series).trigger('change');                        
                        $.each(JSON.parse(result.repeat_day), function(i, item) {
                            $('input[value="' + item + '"]:checkbox').prop('checked', true);
                        });
                        document.getElementById("cek_series_repeat_week").value = result.repeat_week;
                        $('#repeat_series_date').flatpickr({
                            allowInput: true,
                            altInput: false,
                            altFormat: "d F, Y",
                            dateFormat: "Y-m-d",
                            minDate: result.start_date,
                            maxDate: result.end_date
                        });
                        if(result.repeat_status == 'Daily'){
                            $('#tipe-daily').show(); 
                            $('#tipe-weekly').hide(); 
                            $('#tipe-monthly').hide(); 
                            $('#series-weekly').hide(); 
                            $('#series-monthly').hide();
                            $("#series_daily").prop('checked', true); 
                            $("#series_weekly").prop('checked', false); 
                        }
                        else if(result.repeat_status == 'Weekly'){
                            $('#tipe-weekly').show(); 
                            $('#series-weekly').show(); 
                            $('#tipe-daily').hide(); 
                            $('#tipe-monthly').hide();  
                            $('#series-monthly').hide();
                            $("#series_daily").prop('checked', false); 
                            $("#series_weekly").prop('checked', true); 
                        }
                        else{
                            $('#tipe-daily').hide(); 
                            $('#tipe-weekly').hide();
                            $('#tipe-monthly').show();

                            $('#series-weekly').hide();
                            $('#series-monthly').show();

                            $("#series_daily").prop('checked', false); 
                            $("#series_weekly").prop('checked', false);
                            $("#series_monthly").prop('checked', true);
                            $('#series_on_day').val(result.repeat_month).trigger('change');
                        }
                    }
                });
            }
        });

        calendar.render();    
        $('input[name=tanggal]').on('change', function() {
            calendar.gotoDate(this.value);
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
