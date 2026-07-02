@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ===================== */
/* LAYOUT */
/* ===================== */
#attendance-calendar {
    max-width: 100%;
    margin-top: 20px;
}

#calendar-title {
    font-weight: bold;
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: #fff;
}

.legend-box {
    width: 18px;
    height: 18px;
    border-radius: 4px;
}

/* container event dalam 1 tanggal */
.fc-daygrid-day-events {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

/* batas tinggi + scroll */
.fc-daygrid-day-frame {
    max-height: 100px;
    overflow-y: auto;
}

/* styling event */
.custom-event-box {
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 600;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ===================== */
/* EVENT STYLE */
/* ===================== */

/* remove default FullCalendar style */
.fc-daygrid-event {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    cursor: pointer !important;
    z-index: 5;
}

/* event stack */
.fc-daygrid-day-events {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

/* scroll kalau banyak event */
.fc-daygrid-day-frame {
    max-height: 100px;
    overflow-y: auto;
}

/* ===================== */
/* DAY CELL */
/* ===================== */

.fc-daygrid-day {
    position: relative;
    transition: all 0.2s ease;
    cursor: pointer;
}

/* hover effect */
.fc-daygrid-day:hover {
    transform: scale(1.02);
}

/* hover overlay */
.fc-daygrid-day:hover::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.08);
    z-index: 2;
}

/* ===================== */
/* WEEKEND */
/* ===================== */

/* Sabtu */
.fc-daygrid-day.fc-day-sat::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 123, 255, 0.05);
    z-index: 1;
}

/* Minggu */
.fc-daygrid-day.fc-day-sun::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 0, 0, 0.05);
    z-index: 1;
}

/* ===================== */
/* OUTSIDE MONTH */
/* ===================== */

.fc-daygrid-day.fc-day-other {
    position: relative;
}

/* overlay abu transparan */
.fc-daygrid-day.fc-day-other::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(200, 200, 200, 0.4);
    z-index: 3;
}

/* angka tanggal redup */
.fc-daygrid-day.fc-day-other .fc-daygrid-day-number {
    color: #999;
}

/* versi soft untuk bulan lain */
.fc-daygrid-day.fc-day-other.fc-day-holiday {
    background-color: #ffe69c !important;
}

.fc-daygrid-day.fc-day-other.fc-day-company {
    background-color: #f5a5a5 !important;
}

.fc-daygrid-day.fc-day-other.fc-day-cultural {
    background-color: #a8e6b0 !important;
}

/* ===================== */
/* FIX LAYER INTERACTION */
/* ===================== */

/* semua overlay tidak ganggu klik */
.fc-daygrid-day::before,
.fc-daygrid-day::after {
    pointer-events: none;
}

/* ===================== */
/* TODAY */
/* ===================== */

.fc-day-today {
    outline: 2px solid #0d6efd;
    outline-offset: -2px;
}
</style>
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Attendance Calendar</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Attendance Calendar</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-3">Calendar Management</h5>
        <!-- FILTER + BUTTON -->
        <div class="d-flex justify-content-between align-items-center">
            <!-- LEFT: HQ HO -->
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary active" id="btn-hq" data-value="1">HQ</button>
                <button class="btn btn-outline-primary" id="btn-ho" data-value="0">HO</button>
            </div>
            <!-- RIGHT: ADD -->
                <button class="btn btn-primary" id="btn-sync-nasional">
                    Sync National Holiday
                </button>
        </div>
        <!-- CUSTOM HEADER CALENDAR -->
        <div class="d-flex flex-column align-items-center">
            <!-- TITLE -->
            <h4 id="calendar-title" class="mb-2"></h4>
            <!-- NAVIGATION -->
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light fw-bold" id="btn-prev"><<</button>
                <button class="btn btn-sm btn-primary fw-bold px-3" id="btn-today">Today</button>
                <button class="btn btn-light fw-bold" id="btn-next">>></button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div id="attendance-calendar"></div>

        <div class="mt-3 d-flex gap-4">
        <div class="d-flex align-items-center gap-2">
            <div class="legend-box bg-warning"></div>
            <small>Libur Nasional</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="legend-box bg-danger"></div>
            <small>Libur Company</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="legend-box bg-success"></div>
            <small>Libur Keagamaan</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="legend-box bg-secondary"></div>
            <small>Other Event</small>
        </div>
    </div>
    </div>
</div>

{{-- -Modal Add Event --}}
<div class="modal fade" id="modalEvent" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Calendar Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="event-list" class="mb-3"></div>
                <form id="form-event">
                    <div class="mb-3">
                        <label>Activity Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" id="event-date" required readonly>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select class="form-select" name="type">
                            <option value="national">National Holiday</option>
                            <option value="company">Company Event</option>
                            <option value="cultural">Cultural Event</option>
                            <option value="other">Other Event</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Applies To</label>
                        <select class="form-select" name="is_hq">
                            <option value="1">HQ</option>
                            <option value="0">HO</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger d-none" id="delete-event">Delete</button>
                <button class="btn btn-warning d-none" id="update-event">Update</button>
                <button class="btn btn-primary" id="save-event">Save</button>
            </div>
        </div>
    </div>
</div>

<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
<script type="text/javascript">
$(function () {
    let calendarEl = document.getElementById('attendance-calendar');
    let selectedHQ = 1;
    let eventMap = {};
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: false,
        timeZone: 'local',
        dayMaxEventRows: false,
        events: function(fetchInfo, successCallback) {
            $.ajax({
                url: "{{ route('attendance-calendar.data') }}",
                data: {
                    is_hq: selectedHQ
                },
                success: function(res) {
                    successCallback(res);
                    setTimeout(() => {
                    }, 50);
                }
            });
        },
        datesSet: function() {
            let date = calendar.getDate();
            let options = { month: 'long', year: 'numeric' };
            $('#calendar-title').text(date.toLocaleDateString('en-US', options));

            // 🔥 ini penting untuk bulan abu-abu
            setTimeout(() => {
            }, 100);
        },

        loading: function(isLoading) {
            if (isLoading) {
                // 🔥 hapus semua warna sebelum render baru
                document.querySelectorAll('.fc-daygrid-day').forEach(el => {
                    el.classList.remove('fc-day-holiday', 'fc-day-company', 'fc-day-cultural');
                });
            }
        },
        eventWillUnmount: function(info) {
            let date = info.event.startStr.substring(0,10);
            let cell = document.querySelector('[data-date="'+date+'"]');

            if (cell) {
                cell.classList.remove('fc-day-holiday', 'fc-day-company', 'fc-day-cultural','fc-day-other');
            }
        },
        dateClick: function(info) {
            openModalByDate(info.dateStr);
        },
        eventClick: function(info) {
            openModalByDate(info.event.startStr.substring(0,10));
        },

        eventContent: function(arg) {
            let type = arg.event.extendedProps.type;
            let title = arg.event.title;

            let bgColor = '';
            let textColor = '#000';

            if (type === 'national') {
                bgColor = '#ffc107';
            }
            if (type === 'company') {
                bgColor = '#dc3545';
                textColor = '#fff';
            }
            if (type === 'cultural') {
                bgColor = '#3abe59';
                textColor = '#fff';
            }
            if (type === 'other') {
                bgColor = '#6c757d';
                textColor = '#fff';
            }
            return {
                html: `
                    <div class="custom-event-box"
                        style="
                            background:${bgColor};
                            color:${textColor};
                        ">
                        ${title}
                    </div>
                `
            };
        }
    });
    // 🔥 Buka modal berdasarkan tanggal, cek apakah sudah ada event atau belum
    function openModalByDate(date) {
        $('#form-event')[0].reset();
        $('input[name="id"]').remove();
        $('#event-date').val(date);
        $('select[name="is_hq"]').val(selectedHQ);

        let events = calendar.getEvents().filter(e =>
            e.startStr.substring(0,10) === date
        );

        let html = '';

        if (events.length > 0) {
            events.forEach(ev => {
                html += `
                    <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <b>${ev.title}</b><br>
                            <medium>${ev.extendedProps.type}</medium>
                        </div>
                        <button class="btn btn-sm btn-danger btn-delete-event" data-id="${ev.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
            });

            $('#event-list').html(html);
        } else {
            $('#event-list').html('<small class="text-muted">No events</small>');
        }

        $('#save-event').removeClass('d-none');
        $('#update-event').addClass('d-none');
        $('#delete-event').addClass('d-none');

        $('#modalEvent').modal('show');
    }

    calendar.render();

    // 🔹 FILTER HQ / HO
    $('#btn-hq').click(function () {
        selectedHQ = 1;
        $('#btn-hq, #btn-ho').removeClass('active');
        $(this).addClass('active');
        calendar.refetchEvents();
    });

    $('#btn-ho').click(function () {
        selectedHQ = 0;
        $('#btn-hq, #btn-ho').removeClass('active');
        $(this).addClass('active');
        calendar.refetchEvents();
    });

    // 🔹 NAVIGATION
    $('#btn-prev').click(function () {calendar.prev();});
    $('#btn-next').click(function () {calendar.next();});
    $('#btn-today').click(function () {calendar.today();});

    // 🔹 ADD EVENT
    $('#save-event').click(function () {
        let formData = $('#form-event').serialize();
        $('#modalEvent').modal('hide');
        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $.ajax({
            url: "{{ route('attendance-calendar.store') }}",
            type: 'POST',
            data: formData,
            success: function(res) {
                Swal.close();
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message
                    });
                    calendar.refetchEvents();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let errors = xhr.responseJSON.errors;
                let message = '';
                $.each(errors, function(key, val) {
                    message += val[0] + '\n';
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: message
                });
            }
        });
    });

    //Update Event
    $('#update-event').click(function () {
        let id = $('input[name="id"]').val();
        let formData = $('#form-event').serialize();

        Swal.fire({
            title: 'Updating...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('attendance-calendar.update', ':id') }}".replace(':id', id),
            type: 'PUT',
            data: formData,
            success: function(res) {
                Swal.close();
                Swal.fire('Success', res.message, 'success');
                $('#modalEvent').modal('hide');
                calendar.refetchEvents();
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'Failed to update', 'error');
            }
        });
    });

    //Delete Event
    $(document).on('click', '.btn-delete-event', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Delete this event?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('attendance-calendar.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Deleted!', '', 'success');
                        calendar.refetchEvents();
                        $('#modalEvent').modal('hide');
                    }
                });
            }
        });
    });

    $('#modalEvent').on('hidden.bs.modal', function () {
    $('#form-event')[0].reset();
    $('input[name="id"]').remove();

    $('#save-event').removeClass('d-none');
    $('#update-event').addClass('d-none');
    $('#delete-event').addClass('d-none');
});

    $('#btn-sync-nasional').click(function () {
        Swal.fire({
            title: 'Sync Libur Nasional?',
            text: "Data akan diambil dari internet",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Sync!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('attendance-calendar.syncNational') }}";
            }
        });
    });
});
</script>
@endsection
