@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Select2-->
    <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <!-- fullcalendar css -->
    <link href="/assets/libs/fullcalendar/main.min.css" rel="stylesheet" type="text/css" />
    <style>
        #calendar .fc-day-sun {
            color: red;
        }
        #calendar .fc-day-sat {
            color: blue;
        }
        table, td {
            border: 0px solid black;
        }
        p.judul-plan {
            text-transform: lowercase;
        } 

        p.judul-plan::first-letter {
            text-transform: uppercase;
        }
        td.first {
            width: 10%;
            vertical-align: top;
        }
        td.second {
            width: 1%;
            vertical-align: top;
        }
        td.three {
            width: 50%;
            vertical-align: top;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
    </style>
@endsection
@section('content')
    <!-- start page -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Scheduled Calendar</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Calendar</a></li>
                    <li class="breadcrumb-item active">Scheduled</li>
                </ol>
            </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-3" hidden>
                            <div class="card card-h-100" hidden>
                                <div class="card-body">
                                    <div id="external-events">
                                        <p class="text-muted">Note :</p>
                                        <div class="external-event fc-event bg-soft-warning text-warning" data-class="bg-soft-warning">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                                            On Progress
                                        </div>
                                        <div class="external-event fc-event bg-soft-success text-success" data-class="bg-soft-success">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                                            Finished
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div hidden>
                                <h5 class="mb-1">Upcoming Trainings</h5>
                                <p class="text-muted">Don't miss scheduled trainings</p>
                                <div class="pe-2 me-n1 mb-3" data-simplebar style="height: 333px">
                                    <div id="upcoming-event-list" hidden></div>
                                </div>
                            </div>
                        </div> <!-- end col-->

                        <div class="col-lg-12">
                            <div class="card card-h-100">
                                <div class="card-body">
                                    <div class="float-end mb-4">
                                        <span class="badge fs-14 text-bg-warning">On Progress</span>
                                        <span class="badge fs-14 text-bg-success">Finished</span>
                                    </div>
                                    <div id="calendar" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!--end row-->

                    <div style='clear:both'></div>

                    <!-- Add New Event MODAL -->
                    <div class="modal fade" id="event-modal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0">
                                <div class="modal-header p-3 bg-soft-info">
                                    <h5 class="modal-title" id="modal-title">Event</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <form action="#" class="form needs-validation" name="event-form" id="form-event" method="POST">
                                        <div class="text-end">
                                            <a href="#" class="btn btn-sm btn-soft-primary" id="edit-event-btn" data-id="edit-event" onclick="editEvent(this)" role="button">Edit</a>
                                        </div>
                                        <div class="event-details">
                                            <div class="d-flex mb-2">
                                                <div class="flex-grow-1 d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <i class="ri-calendar-event-line text-muted fs-16"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="d-block fw-semibold mb-0" id="event-start-date-tag"></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="ri-time-line text-muted fs-16"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="d-block fw-semibold mb-0"><span id="event-timepicker1-tag"></span> - <span id="event-timepicker2-tag"></span></h6>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="ri-map-pin-line text-muted fs-16"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="d-block fw-semibold mb-0"> <span id="event-location-tag"></span></h6>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="ri-discuss-line text-muted fs-16"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="d-block text-muted mb-0" id="event-description-tag"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row event-form">
                                            <div class="col-12" hidden>
                                                <div class="mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select class="form-select d-none" name="category" id="event-category">
                                                        <option value="bg-soft-danger">Danger</option>
                                                        <option value="bg-soft-success">Success</option>
                                                        <option value="bg-soft-primary">Primary</option>
                                                        <option value="bg-soft-info">Info</option>
                                                        <option value="bg-soft-dark">Dark</option>
                                                        <option value="bg-soft-warning">Warning</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a valid event category</div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <input type="hidden" id="id_template" name="id_template" value="" />
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select class="form-control" name="type[]" id="type" multiple="multiple" data-placeholder="Select type" required>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Event Name</label>
                                                    <input class="form-control" placeholder="Enter event name" type="text" name="event" id="event" value="" />
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="tanggal" class="form-label">Date</label>
                                                    <div class="input-group">
                                                        <input type="text" name="tanggal" id="tanggal"
                                                            class="form-control"
                                                            placeholder="Select date" value="" required>
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Collective Leave</label>
                                                    <select class="form-select" name="leave" id="leave" data-placeholder="Select leave" required>
                                                        <option selected="true" disabled="true"></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end row-->
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-soft-danger" id="btn-delete-event"><i class="ri-close-line align-bottom"></i> Delete</button>
                                            <button type="submit" class="btn btn-success" id="btn-save-event">Add Event</button>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- end modal-content-->
                        </div> <!-- end modal dialog-->
                    </div> <!-- end modal-->
                    <!-- end modal-->
                </div>
            </div> <!-- end row-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    
    <div id="modal-view-training" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title"><span id="judul-modal"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                    <div class="modal-body">
                        <table style="width:100%">
                            <tr>
                                <td class="first">
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                            Date
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
                                                <i class="ri-calendar-event-line text-muted fs-16"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="d-block mb-0"
                                                    id="view_tanggal"></h6>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="first">
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                            Training
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
                                            <i class="ri-todo-line text-muted fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="d-block fw-semibold mb-0"> <span
                                                    id="view_judul"></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>                                
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
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="ri-todo-line text-muted fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="d-block fw-semibold mb-0"> <span
                                                    id="view_tipe"></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>                                
                            <tr>
                                <td class="first">
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                            Peserta
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
                                        <div class="flex-grow-1">
                                            <div id="peserta"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>                                
                        </table>                                                
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="edit_id" data-bs-target="#ModalEdit" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-soft-primary"><i class="ri-edit-2-line align-bottom"></i> Edit this training</button>
                        @can('hrd.training.scheduled.update')
                        @endcan
                    </div>                                  
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal konfirmasi view calendar -->
    <!-- Modal Edit Calendar -->
    <form id="form-edit" action="{{ route('training.scheduled.update') }}" method="POST">
        @csrf
        @method('put')
        <div id="ModalEdit" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="flipModalLabel">Edit Training</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control text-sm" id="id_edit" name="id_edit" style="Background-color: #eff2f7;" readonly>
                        <div class="row">
                            <div class="col-lg-12">
                                <div>
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <div class="input-group">
                                        <input type="text" name="start_date" id="start_date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <div>
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <div class="input-group">
                                        <input type="text" name="end_date" id="end_date"
                                            class="form-control @error('end_date') is-invalid @enderror"
                                            placeholder="Pilih Tanggal" value="" required>
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="id_vendor" class="form-label">Vendor</label>                            
                                <select class="form-control" id="id_vendor" name="id_vendor" data-placeholder="Pilih Vendor" required>
                                    <option selected="true" disabled="true"></option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{$vendor->id}}">{{$vendor->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <div>
                                    <label for="lokasi" class="form-label">Lokasi</label>
                                    <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan Nama Lokasi" value="" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <div>
                                    <label for="biaya" class="form-label">Biaya</label>
                                    <input type="number" class="form-control" name="biaya" id="biaya" placeholder="Masukkan Nama Biaya" value="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btn-update" class="btn btn-primary">Save</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal detail product edit-->
    </form>
@endsection
@section('script')
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- calendar min js -->
<script src="/assets/libs/fullcalendar/main.min.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('javascript')
<script>
    $(function () {    
        $('.select2').select2();
        $('#id_vendor').select2({dropdownParent: $('#ModalEdit .modal-content')});
    });

    $('#start_date').flatpickr({
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
</script>
<script>
    //submit update event
    $("#form-edit").submit(function(e) {
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
    var data_all = {{Js::from($data_all)}};
    
    function eventClicked() {
        $('#modal-view-training').modal('show', true);
    }
    
    function upcomingEvent(e) {
        e.reverse(function(e, t) {
            return new Date(e.start) - new Date(t.start)
        }), document.getElementById("upcoming-event-list").innerHTML = null, Array.from(e).forEach(function(e) {
            var t = e.title,
                n = (l = e.end ? (endUpdatedDay = new Date(e.end)).setDate(endUpdatedDay.getDate() - 1) : l) || void 0;
            n = "Invalid Date" == n || null == n ? null : (a = new Date(n).toLocaleDateString("en", {
                year: "numeric",
                month: "numeric",
                day: "numeric"
            }), new Date(a).toLocaleDateString("en-GB", {
                day: "numeric",
                month: "short",
                year: "numeric"
            }).split(" ").join(" "));
            (e.start ? str_dt(e.start) : null) === (l ? str_dt(l) : null) && (n = null);
            var a = e.start,
                d = (a = "Invalid Date" === a || void 0 === a ? null : (d = new Date(a).toLocaleDateString("en", {
                    year: "numeric",
                    month: "numeric",
                    day: "numeric"
                }), new Date(d).toLocaleDateString("en-GB", {
                    day: "numeric",
                    month: "short",
                    year: "numeric"
                }).split(" ").join(" ")), n ? " to " + n : ""),
                n = e.className.split("-"),
                i = e.description || "",
                e = tConvert(getTime(e.start)),
                l = (e == (l = tConvert(getTime(l))) && (e = "Full day event", l = null), l ? " to " + l : "");
            u_event = "<div class='card mb-3'><div class='card-body'><div class='d-flex mb-3'><div class='flex-grow-1'><i class='mdi mdi-checkbox-blank-circle me-2 text-" + n[2] + "'></i><span class='fw-medium'>" + a + d + " </span></div>                                <div class='flex-shrink-0'></div>                            </div>                            <h6 class='card-title fs-14'> " + t + "</h6>                            <p class='text-muted text-truncate-two-lines mb-0'> " + i + "</p>                        </div>                    </div>", document.getElementById("upcoming-event-list").innerHTML += u_event
        })
    }
    
    function getTime(e) {
        if (null != (e = new Date(e)).getHours()) return e.getHours() + ":" + (e.getMinutes() ? e.getMinutes() : 0)
    }
    
    function tConvert(e) {
        var e = e.split(":"),
            t = e[0],
            e = e[1],
            n = 12 <= t ? "PM" : "AM";
        return (t = (t %= 12) || 12) + ":" + (e = e < 10 ? "0" + e : e) + " " + n
    }
    document.addEventListener("DOMContentLoaded", function() {
        var g = new bootstrap.Modal(document.getElementById("event-modal"), {
                keyboard: !1
            }),
            d = (document.getElementById("event-modal"), document.getElementById("modal-title")),
            i = document.getElementById("form-event"),
            v = null,
            p = document.getElementsByClassName("needs-validation"),
            e = new Date,
            t = e.getDate(),
            n = e.getMonth(),
            e = e.getFullYear(),
            a = FullCalendar.Draggable,
            l = document.getElementById("external-events"),
            y = data_all,
            e = (new a(l, {
                itemSelector: "#",
                eventData: function(e) {
                    return {
                        id: Math.floor(11e3 * Math.random()),
                        title: e.innerText,
                        allDay: !0,
                        start: new Date,
                        className: e.getAttribute("data-class")
                    }
                }
            }), document.getElementById("calendar"));
    
        function o(e) {
            document.getElementById("form-event").reset(), document.getElementById("btn-delete-event").setAttribute("hidden", !0), g.show(), i.classList.remove("was-validated"), i.reset(), v = null, d.innerText = "Add Event", newEventData = e, document.getElementById("edit-event-btn").setAttribute("data-id", "new-event"), document.getElementById("edit-event-btn").click(), document.getElementById("edit-event-btn").setAttribute("hidden", !0)
        }
    
        function r() {
            return 768 <= window.innerWidth && window.innerWidth < 1200 ? "timeGridWeek" : window.innerWidth <= 768 ? "listMonth" : "dayGridMonth"
        }
        var c = new Choices("#event-category", {
                searchEnabled: !1
            }),
            E = new FullCalendar.Calendar(e, {
                // timeZone: "local",
                height: 535,
                editable: !1,
                droppable: !1,
                selectable: !0,
                navLinks: !0,
                initialView: r(),
                themeSystem: "bootstrap",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,listMonth"
                },
                windowResize: function(e) {
                    var t = r();
                    E.changeView(t)
                },
                eventResize: function(t) {
                    var e = y.findIndex(function(e) {
                        return e.id == t.event.id
                    });
                    y[e] && (y[e].title = t.event.title, y[e].start = t.event.start, y[e].end = t.event.end || null, y[e].allDay = t.event.allDay, y[e].className = t.event.classNames[0], y[e].description = t.event._def.extendedProps.description || "", y[e].location = t.event._def.extendedProps.location || ""), upcomingEvent(y)
                },
                eventClick: function(e) {
                    document.getElementById("judul-modal").innerHTML = e.event.title;
                    var id_record = e.event.id;
                    $('#peserta').html('');
                    //ajax view calendar
                    $.ajax({
                        url: "{{ route('training.scheduled.view') }}",
                        type: "POST",
                        data: {
                            id_record: id_record,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result){
                            //view
                            $('input[name="id_edit"]').val(result['kode_fkt']);
                            document.getElementById("view_tanggal").innerHTML = result.date;
                            document.getElementById("view_judul").innerHTML = result['judul'];
                            if(result['tipe'] == 'ptt'){
                                document.getElementById("view_tipe").innerHTML = 'Program Training Tahunan';
                            }else if(result['tipe'] == 'pti'){
                                document.getElementById("view_tipe").innerHTML = 'Program Training Insidentil';
                            }else{
                                document.getElementById("view_tipe").innerHTML = 'NA';
                            }
                            $.each(result['detail'], function(key, value) {
                                $('#peserta').append('<h6 class="d-block align-items-center fw-semibold mb-2"><span>'+value.nama+' || '+value.dept+'</span></h6>');
                            });
                            //edit
                            $('#start_date').val(result['start_date']);
                            $('#end_date').val(result['end_date']);
                            $('#id_vendor').val(result['id_vendor']).trigger('change');
                            $('#lokasi').val(result['lokasi']);
                            $('#biaya').val(result['biaya']);
                        }
                    });
                    eventClicked(), i.reset();
                },
                events: y,
                eventReceive: function(e) {
                    e = {
                        id: parseInt(e.event.id),
                        title: e.event.title,
                        start: e.event.start,
                        allDay: e.event.allDay,
                        className: e.event.classNames[0]
                    };
                    y.push(e), upcomingEvent(y)
                },
                eventDrop: function(t) {
                    var e = y.findIndex(function(e) {
                        return e.id == t.event.id
                    });
                    y[e] && (y[e].title = t.event.title, y[e].start = t.event.start, y[e].end = t.event.end || null, y[e].allDay = t.event.allDay, y[e].className = t.event.classNames[0], y[e].description = t.event._def.extendedProps.description || "", y[e].location = t.event._def.extendedProps.location || ""), upcomingEvent(y)
                }
            });
        E.render(), upcomingEvent(y), i.addEventListener("submit", function() {
            upcomingEvent(y)
        }), document.getElementById("btn-delete-event").addEventListener("click", function(e) {
            if (v) {
                for (var t = 0; t < y.length; t++) y[t].id == v.id && (y.splice(t, 1), t--);
                upcomingEvent(y), v.remove(), v = null, g.hide()
            }
        })
        // , document.getElementById("btn-new-event").addEventListener("click", function(e) {
        //      o(), document.getElementById("edit-event-btn").setAttribute("data-id", "new-event"), document.getElementById("edit-event-btn").click(), document.getElementById("edit-event-btn").setAttribute("hidden", !0)
        // })
    });
    var str_dt = function(e) {
        var e = new Date(e),
            t = "" + ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][e.getMonth()],
            n = "" + e.getDate(),
            e = e.getFullYear();
        return t.length < 2 && (t = "0" + t), [(n = n.length < 2 ? "0" + n : n) + " " + t, e].join(",")
    };
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
