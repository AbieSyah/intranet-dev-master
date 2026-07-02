@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/assets/libs/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/libs/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/libs/Datatables/datatables.css">

    <style>
        th {
            text-align: center !important;
        }
    </style>
@endsection

@section('content')
    <x-page-title title="Guest Form" :breadcrumbs="['Employee']" />

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                @can('security.guest.update')
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <a href="{{ route('guest.security-form') }}"
                            class="float-end btn btn-primary btn-label waves-effect waves-light" data-text="Add New Employee"><i
                                class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Create</a>
                    </div>
                @endcan
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="guest-table" class="table" style="overflow: visible">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>Tujuan Kunjungan</th>
                                    <th>Bertemu Dengan</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Bertemu</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="/assets/libs/Datatables/datatables.js"></script>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            $('#guest-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('guest.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'perusahaan',
                        name: 'perusahaan'
                    },
                    {
                        data: 'tujuan_kunjungan',
                        name: 'tujuan_kunjungan'
                    },
                    {
                        data: 'employee.fullname',
                        name: 'employee.fullname',
                        render: function(data, type, row, meta) {
                            return data ? data : row.nama_pic;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'security_guest.created_at', // Specify the table alias here
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return new Date(full.created_at).toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'waktu_bertemu',
                        name: 'waktu_bertemu',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if (data) {
                                return new Date(data).toLocaleTimeString('en-GB', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            } else if (full.waktu_keluar) {
                                return "NA";
                            } else {
                                return "Pending"
                            }
                        }
                    },
                    {
                        data: 'waktu_keluar',
                        name: 'waktu_keluar',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            if (data) {
                                return new Date(data).toLocaleTimeString('en-GB', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            } else {
                                return `
                        <button class="btn btn-sm btn-info set-waktu-keluar-button text-nowrap" data-id="${row.id}">
                            <i class="mdi mdi-clock-out"></i> Set
                        </button>
                    `;
                            }
                        }
                    },
                    {
                        data: null,
                        name: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            const checklistImage =
                                '{{ asset('/assets/images/security/checklist.png') }}';
                            const isCheckedOut = full.waktu_keluar !== null;

                            return `
                    <div class="d-flex align-items-center justify-content-center w-100">
                        ${full.nomor_kartu_identitas ? `<img src="${checklistImage}" alt="checklist" class="m-1" style="width: 15px;">` : ''}
                        <span class="badge ${isCheckedOut ? 'bg-success' : 'bg-warning'} text-light m-1">
                            ${isCheckedOut ? 'Out' : 'In'}
                        </span>
                        ${full.nomor_visitor ? `<span class="badge border border-primary text-primary m-1">${full.nomor_visitor}</span>` : ''}
                    </div>
                `;
                        }
                    },
                    {
                        data: 'encrypted_id',
                        name: 'encrypted_id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full) {
                            let verificateButton = '';

                            if (full.nomor_kartu_identitas === null) {
                                verificateButton = `
                        <a class="dropdown-item verificate-button" href="{{ route('guest.security-form') }}/${data}" data-id="${full.id}">
                            <i class="mdi mdi-close-circle-outline"></i> Verificate
                        </a>
                    `;
                            } else {
                                verificateButton = `
                        <a class="dropdown-item verificate-button" href="{{ route('guest.security-form') }}/${data}" data-id="${full.id}">
                            <i class="mdi mdi-check-circle-outline"></i> Verificate
                        </a>
                    `;
                            }

                            return `
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        </button>
                        <ul class="dropdown-menu">
                            <a class="dropdown-item detail-button" href="{{ route('guest.detail') }}/${data}" data-id="${full.encrypted_id}">
                                <i class="mdi mdi-eye-outline"></i> Detail
                            </a>
                            <a class="dropdown-item" href="{{ route('guest.print') }}/${data}" target="_blank" data-id="${full.encrypted_id}">
                                <i class="mdi mdi-file-pdf-box"></i> Print
                            </a>
                            ${verificateButton}
                            ${full.nomor_kartu_identitas ? '' : `
                                            <a class="dropdown-item delete-button text-danger" href="#" data-id="${full.id}">
                                                <i class="mdi mdi-delete-outline"></i> Delete
                                            </a>
                                        `}
                        </ul>
                    </div>
                `;
                        }
                    }
                ]
            });


            // Generic SweetAlert Confirmation with AJAX
            function handleGuestAction(buttonSelector, requestUrl, method, confirmText, buttonColor) {
                $('#guest-table').on('click', buttonSelector, function() {
                    const guestId = $(this).data('id');

                    Swal.fire({
                        title: `Apakah anda yakin untuk ${confirmText.toLowerCase()}?`,
                        icon: 'info',
                        showCancelButton: true,
                        cancelButtonText: 'Batal',
                        confirmButtonColor: buttonColor,
                        confirmButtonText: confirmText,
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: requestUrl.replace(':id', guestId),
                                method: method,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                }
                            }).fail(response => {
                                let errorMessage = response.responseJSON?.message ||
                                    'Error occurred!';
                                Swal.fire('Error!', errorMessage, 'error');
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading(),
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Success!', 'Operation successful.', 'success');
                            $('#guest-table').DataTable().ajax.reload(); // Redraw the table
                        }
                    });
                });
            }

            handleGuestAction('.delete-button', "{{ route('guest.delete') }}/:id", "DELETE", 'Delete', 'darkred');
            handleGuestAction('.set-waktu-keluar-button', "{{ route('guest.set-waktu-keluar') }}/:id", "PATCH",
                'Set Waktu Keluar',
                '#17a2b8');

        });
    </script>
@endsection
