@extends('layouts.master')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Risk Register</h4>

                <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Risk Register</a></li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <button onclick="addRisk()" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i> Add Risk</button>
                <a href="{{ route('service-management.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
            </div>
            <div class="card-body">
                <table class="table w-100" id="riskTable">
                    <thead>
                        <tr>
                            <th>Risk ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Impact</th>
                            <th>Prob.</th>
                            <th>Score</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewRiskModal" tabindex="-1" aria-labelledby="viewModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold" id="viewModalTitle"><i class="bi bi-eye me-2 text-primary"></i>Risk Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">Risk ID</label>
                            <div class="p-2 bg-light rounded fw-bold text-secondary" id="view_risk_id">-</div>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label small text-muted fw-bold mb-1">Risk Name</label>
                            <div class="p-2 bg-light rounded text-dark fw-semibold" id="view_name">-</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-muted fw-bold mb-1">Description</label>
                            <div class="p-3 bg-light rounded text-secondary" id="view_description" style="min-height: 80px; white-space: pre-line;">-</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">Impact (1-3)</label>
                            <div class="p-2 bg-light rounded text-dark" id="view_impact">-</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">Probability (1-3)</label>
                            <div class="p-2 bg-light rounded text-dark" id="view_probability">-</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">Risk Score</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white border-0"><i class="bi bi-shield-shaded"></i></span>
                                <div class="form-control fw-bold bg-light" id="view_display_score">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold mb-1">Mitigation</label>
                            <div class="p-3 border border-light-subtle bg-light rounded text-secondary" id="view_mitigation" style="min-height: 90px; white-space: pre-line;">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold mb-1">Contingency Plan</label>
                            <div class="p-3 border border-light-subtle bg-light rounded text-secondary" id="view_contingency_plan" style="min-height: 90px; white-space: pre-line;">-</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="accordion" id="guideAccordion">
                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-light py-2" type="button" data-bs-toggle="collapse" data-bs-target="#impactGuide" style="font-size: 0.8rem;">
                                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Panduan Impact (Dampak)
                                    </button>
                                </h2>
                                <div id="impactGuide" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="alert alert-info mb-0 border-0 rounded-0" style="font-size: 0.75rem;">
                                            <ul class="mb-0 ps-3">
                                                <li><strong>1 - Low:</strong> Permintaan bersifat administratif, bantuan penggunaan (how-to), atau perbaikan perangkat personal yang masih bisa menyala. Dampak : Operasional</li>
                                                <li><strong>2 - Medium:</strong> Medium : Gangguan pada sebagian fitur atau kinerja lambat. Pekerjaan terhambat tapi masih bisa berjalan (ada solusi sementara). Dampak : Operasional, Reputasi</li>
                                                <li><strong>3 - High:</strong> High : Layanan kritis mati total, menghambat operasional inti perusahaan, atau risiko kehilangan data Perusahaan. Dampak : Kerugian Financial, Reputasi, Operasional</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-light py-2" type="button" data-bs-toggle="collapse" data-bs-target="#probGuide" style="font-size: 0.8rem;">
                                        <i class="bi bi-graph-up-arrow text-primary me-2"></i> Panduan Probability (Kemungkinan)
                                    </button>
                                </h2>
                                <div id="probGuide" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="alert alert-primary mb-0 border-0 rounded-0" style="font-size: 0.75rem;">
                                            <ul class="mb-0 ps-3">
                                                <li><strong>1 - Rare:</strong> Hampir tidak pernah, atau terjadi 1 tahun sekali.</li>
                                                <li><strong>2 - Moderate:</strong> Terjadi minimal 1 bulan sekali.</li>
                                                <li><strong>3 - Frequent:</strong> Terjadi setiap minggu (High Risk).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="riskModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add New Risk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="riskForm">
                    @csrf
                    <input type="hidden" name="id" id="risk_db_id">
                    
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Risk ID</label>
                                <input type="text" name="risk_id" id="risk_id" class="form-control" placeholder="e.g. R01" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Risk Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Describe the risk name" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Explain the potential impact and details..."></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">
                                    Impact (1-3)
                                </label>
                                <select name="impact" id="impact" class="form-select select-calc">
                                    <option value="1">1 - Low</option>
                                    <option value="2">2 - Medium</option>
                                    <option value="3">3 - High</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Probability (1-3)</label>
                                <select name="probability" id="probability" class="form-select select-calc">
                                    <option value="1">1 - Rare</option>
                                    <option value="2">2 - Moderate</option>
                                    <option value="3">3 - Frequent</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Risk Score</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white border-0"><i class="bi bi-shield-shaded"></i></span>
                                    <input type="text" id="display_score" class="form-control fw-bold bg-light" readonly value="1">
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Score = Impact x Probability</small>
                            </div>
                        </div>

                        <div class="mt-2 row">
                            <div class="col">
                                <label class="form-label small fw-bold">Mitigation</label>
                                <textarea name="mitigation" id="mitigation" class="form-control" rows="3" placeholder="Describe the mitigation strategies..."></textarea>
                            </div>
                            <div class="col">
                                <label class="form-label small fw-bold">Contingency Plan</label>
                                <textarea name="contingency_plan" id="contingency_plan" class="form-control" rows="3" placeholder="Describe the contingency plans..."></textarea>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="accordion" id="guideAccordion">
                                <div class="accordion-item border-0 shadow-sm mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light py-2" type="button" data-bs-toggle="collapse" data-bs-target="#impactGuide" style="font-size: 0.8rem;">
                                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Panduan Impact (Dampak)
                                        </button>
                                    </h2>
                                    <div id="impactGuide" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="alert alert-info mb-0 border-0 rounded-0" style="font-size: 0.75rem;">
                                                <ul class="mb-0 ps-3">
                                                    <li><strong>1 - Low:</strong> Permintaan bersifat administratif, bantuan penggunaan (how-to), atau perbaikan perangkat personal yang masih bisa menyala. Dampak : Operasional</li>
                                                    <li><strong>2 - Medium:</strong> Medium : Gangguan pada sebagian fitur atau kinerja lambat. Pekerjaan terhambat tapi masih bisa berjalan (ada solusi sementara). Dampak : Operasional, Reputasi</li>
                                                    <li><strong>3 - High:</strong> High : Layanan kritis mati total, menghambat operasional inti perusahaan, atau risiko kehilangan data Perusahaan. Dampak : Kerugian Financial, Reputasi, Operasional</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item border-0 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light py-2" type="button" data-bs-toggle="collapse" data-bs-target="#probGuide" style="font-size: 0.8rem;">
                                            <i class="bi bi-graph-up-arrow text-primary me-2"></i> Panduan Probability (Kemungkinan)
                                        </button>
                                    </h2>
                                    <div id="probGuide" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="alert alert-primary mb-0 border-0 rounded-0" style="font-size: 0.75rem;">
                                                <ul class="mb-0 ps-3">
                                                    <li><strong>1 - Rare:</strong> Hampir tidak pernah, atau terjadi 1 tahun sekali.</li>
                                                    <li><strong>2 - Moderate:</strong> Terjadi minimal 1 bulan sekali.</li>
                                                    <li><strong>3 - Frequent:</strong> Terjadi setiap minggu (High Risk).</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnSave">
                            <i class="bi bi-save me-1"></i> Save Risk
                        </button>
                    </div>
                </form>
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
    <!-- Select2 -->
    <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
    <script>
        let table;

        $(document).ready(function() {
            table = $('#riskTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('risk-register.data') }}",
                columns: [
                    { data: 'risk_id', name: 'risk_id' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description', render: function(data, type, row) {
                        return data.length > 500 ? data.substr(0, 500) + '...' : data;
                    }},
                    { data: 'impact', name: 'impact', class: 'text-center' },
                    { data: 'probability', name: 'probability', class: 'text-center' },
                    { data: 'score', name: 'score', class: 'text-center' },
                    { 
                        data: null, 
                        sortable: false,
                        searchable: false,
                        orderable: false,
                        class: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <button onclick="viewRisk('${row.edit_url}')" class="btn btn-sm btn-primary text-white"><i class="ri-eye-line"></i></button>
                                <button onclick="editRisk('${row.edit_url}')" class="btn btn-sm btn-info text-white"><i class="ri-pencil-line"></i></button>
                                <button onclick="deleteRisk('${row.delete_url}')" class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                            `;
                        }
                    }
                ]
            });
        });

        function addRisk() {
            $('#riskForm')[0].reset();
            $('#risk_db_id').val('');
            $('#modalTitle').text('Add New Risk');
            $('#riskModal').modal('show');
        }

        function viewRisk(url) {
            $.get(url, function(data) {
                $('#view_risk_id').text(data.risk_id);
                $('#view_name').text(data.name);
                $('#view_description').text(data.description);
                $('#view_impact').text(data.impact);
                $('#view_probability').text(data.probability);
                $('#view_display_score').text(data.score);
                $('#view_mitigation').text(data.mitigation);
                $('#view_contingency_plan').text(data.contingency_plan);
                $('#viewRiskModal').modal('show');
            });
        }

        function editRisk(url) {
            $.get(url, function(data) {
                $('#risk_db_id').val(data.encrypted_id);
                $('#risk_id').val(data.risk_id);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#impact').val(data.impact);
                $('#probability').val(data.probability);
                $('#mitigation').val(data.mitigation);
                $('#contingency_plan').val(data.contingency_plan);
                $('#modalTitle').text('Edit Risk');
                $('#riskModal').modal('show');
            });
        }

        $('#riskForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('risk-register.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#riskModal').modal('hide');
                    toastr.success(res.message);
                    table.ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    }
                }
            });
        });

        function deleteRisk(url) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete!',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            table.ajax.reload();
                            toastr.success(res.message);
                        }
                    });
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            function calculateRiskScore() {
                const impact = parseInt($('#impact').val()) || 0;
                const probability = parseInt($('#probability').val()) || 0;
                const score = impact * probability;
                $('#display_score').val(score);
                if (score >= 7) {
                    $('#display_score').removeClass('text-dark text-warning').addClass('text-danger');
                } else if (score >= 4) {
                    $('#display_score').removeClass('text-dark text-danger').addClass('text-warning');
                } else {
                    $('#display_score').removeClass('text-danger text-warning').addClass('text-dark');
                }
            }
            $('.select-calc').on('change', calculateRiskScore);
            $('#riskModal').on('shown.bs.modal', function() {
                calculateRiskScore();
            });
        });
    </script>
@endsection