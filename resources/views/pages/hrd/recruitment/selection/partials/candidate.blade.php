{{-- Detail Candidate --}}
<style type="text/css">
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }
</style>
<div class="modal fade" id="detailCandidateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f7fbf8;">
                <h5 class="modal-title">Detail Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #f7fbf8;">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0 text-primary text-center">Candidate Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2 text-center" id="det_photo_container" style="display:none;">
                                <img src="" id="det_photo" class="img-thumbnail" style="width: 200px; height: 300px; object-fit: cover;">
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label fw-semibold">Nomor KTP / KTP Number</label>
                                <input type="text" class="form-control" id="det_no_ktp" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label fw-semibold">Nama Lengkap / Full Name</label>
                                <input type="text" class="form-control" id="det_fullname" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label fw-semibold">Nama Panggilan / Nickname</label>
                                <input type="text" class="form-control" id="det_nickname" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Alamat Sesuai KTP / KTP Address</label>
                                <input type="text" class="form-control" id="det_ktp_address" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Alamat Domisili Saat Ini / Domicile Address</label>
                                <input type="text" class="form-control" id="det_domicile_address" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label fw-semibold">Tempat Lahir / Place of Birth</label>
                                <input type="text" class="form-control" id="det_birthplace" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label fw-semibold">Tanggal Lahir / Date of Birth</label>
                                <input type="text" class="form-control" id="det_birthdate" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label for="det_gender" class="form-label col-form-label fw-semibold">Jenis Kelamin / <i>Gender</i></label>
                                <select class="form-select select2" id="det_gender" disabled>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Male">Pria / Male</option>
                                    <option value="Female">Wanita / Female</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="det_religion" class="form-label col-form-label fw-semibold">Agama / <i>Religion</i></label>
                                <select class="form-select select2" id="det_religion" disabled>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Moslem">Islam / Moslem</option>
                                    <option value="Catholic">Katolik / Catholic</option>
                                    <option value="Christian">Kristen / Christian</option>
                                    <option value="Budhist">Buddha / Budhist</option>
                                    <option value="Hindu">Hindu / Hindu</option>
                                    <option value="None">Lainnya / None</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="det_marital" class="form-label col-form-label fw-semibold">Status Perkawinan / <i>Marital</i></label>
                                <select class="form-select select2" id="det_marital" disabled>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Single">Belum Menikah / Single</option>
                                    <option value="Married">Menikah / Married</option>
                                    <option value="Divorced">Cerai / Divorced</option>
                                    <option value="Widow">Janda / Widow</option>
                                    <option value="Widower">Duda / Widower</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Tinggi Badan / Height</label>
                                <input type="text" class="form-control" id="det_height" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Berat Badan / Weight</label>
                                <input type="text" class="form-control" id="det_weight" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Telepon / Phone</label>
                                <input type="text" class="form-control" id="det_phone" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Email / Email</label>
                                <input type="text" class="form-control" id="det_email" disabled>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label col-form-label fw-semibold">Keterampilan & Kemampuan / Skill & Ability</label>
                                <textarea class="form-control" id="det_skill" rows="3" disabled></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="expected_salary" class="form-label col-form-label fw-semibold">Gaji yang Diharapkan / Expected Salary</label>
                                <input type="text" class="form-control" id="expected_salary" name="expected_salary" value="{{ $c->expected_salary ?? '-' }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header"><h5 class="card-title mb-0">Riwayat Pendidikan / Education Background</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped bordered display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">Education Level</th>
                                        <th scope="col" class="text-center">Institution Name</th>
                                        <th scope="col" class="text-center">Major / Field of Study</th>
                                        <th scope="col" class="text-center">Year Graduated</th>
                                        <th scope="col" class="text-center">GPA</th>
                                        <th scope="col" class="text-center">File</th>
                                    </tr>
                                </thead>
                                <tbody id="det_edu_tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header"><h5 class="card-title mb-0">Pengalaman Bekerja / Working Experience</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped bordered display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">Company Name</th>
                                        <th scope="col" class="text-center">Position</th>
                                        <th scope="col" class="text-center">Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="det_exp_tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header"><h5 class="card-title mb-0 text-primary text-center">Submit Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Job Posting Title</label>
                                <input type="text" class="form-control" id="det_posting_title" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Submitted Date</label>
                                <input type="text" class="form-control" id="det_submit_date" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Position</label>
                                <input type="text" class="form-control" id="det_pos_name" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Section</label>
                                <input type="text" class="form-control" id="det_sect_name" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Department</label>
                                <input type="text" class="form-control" id="det_dept_name" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label fw-semibold">Area</label>
                                <input type="text" class="form-control" id="det_area_name" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f7fbf8;">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#table_candidate tbody, #table_final_candidate tbody').on('click', '.view-detail', function() {
            let id = $(this).data('id');
            let data = allCandidates.find(c => c.candidate_id == id);
            if(!data) return;
            $('#det_no_ktp').val(data.no_ktp);
            $('#det_fullname').val(data.fullname);
            if(data.raw_data) {
                let r = data.raw_data;
                $('#det_nickname').val(r.nickname || '-');
                $('#det_ktp_address').val(r.ktp_address || '-');
                $('#det_domicile_address').val(r.domicile_address || '-');
                $('#det_birthplace').val(r.birthplace || '-');
                let displayBirthdate = r.birthdate ? `${r.birthdate} (${r.age})` : '-';
                $('#det_birthdate').val(displayBirthdate);
                $('#det_gender').val(r.gender).trigger('change');
                $('#det_religion').val(r.religion).trigger('change');
                $('#det_marital').val(r.marital).trigger('change');
                $('#det_height').val(r.height ? r.height + ' cm' : '-');
                $('#det_weight').val(r.weight ? r.weight + ' kg' : '-');
                $('#det_phone').val(r.phone || '-');
                $('#det_email').val(r.email || '-');
                $('#det_skill').val(data.skill || '-');
                $('#det_posting_title').val(r.posting_title);
                $('#det_submit_date').val(r.submit_date);
                $('#det_pos_name').val(r.pos_name);
                $('#det_sect_name').val(r.sect_name);
                $('#det_dept_name').val(r.dept_name);
                $('#det_area_name').val(r.area_name);
                if(r.photo) {
                    $('#det_photo').attr('src', "{{ asset('storage/candidates/photos/') }}/" + r.photo);
                    $('#det_photo_container').show();
                } else {
                    $('#det_photo_container').hide();
                }
            }
            let eduHtml = '';
            if(data.raw_educations && data.raw_educations.length > 0) {
                data.raw_educations.forEach((e, index) => {
                    let fileBtn = e.ijazah ? `<a href="{{ asset('storage/candidates/ijazah/') }}/${e.ijazah}" target="_blank" class="btn btn-sm btn-primary"><i class="ri-file-text-line"></i></a>` : '-';
                    eduHtml += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${e.level || '-'}</td>
                            <td class="text-center">${e.institution_name || '-'}</td>
                            <td class="text-center">${e.major || '-'}</td>
                            <td class="text-center">${e.year_graduated || '-'}</td>
                            <td class="text-center">${e.score_gpa || '-'}</td>
                            <td class="text-center">${fileBtn}</td>
                        </tr>
                    `;
                });
            } else {
                eduHtml = '<tr><td colspan="7" class="text-center text-danger fw-semibold">Kandidat belum menyertakan data riwayat pendidikan. / Candidate has not provided education history data.</td></tr>';
            }
            $('#det_edu_tbody').html(eduHtml);
            let expHtml = '';
            if(data.raw_experiences && data.raw_experiences.length > 0) {
                data.raw_experiences.forEach((ex, index) => {
                    let duration = ex.years ? ex.years + ' Years' : '-';
                    expHtml += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${ex.company || '-'}</td>
                            <td class="text-center">${ex.position || '-'}</td>
                            <td class="text-center">${duration}</td>
                        </tr>
                    `;
                });
            } else {
                expHtml = '<tr><td colspan="4" class="text-center text-danger fw-semibold">Kandidat belum menyertakan data pengalaman kerja. / Candidate has not provided working experience data.</td></tr>';
            }
            $('#det_exp_tbody').html(expHtml);
            $('#detailCandidateModal').modal('show');
        });
    });
</script>
{{-- View Comment --}}
<div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Result Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>      
            <div class="modal-body p-4">
                <h6 class="mb-1 fs-15">Candidate : <span id="modalCandidateName" class="fw-bold"></span></h6>
                <h6 class="mb-1 fs-15">Result : <span id="modalResultBadge" class="badge"></span></h6>
                <h6 class="mb-1 fs-15">Comment From : <span id="modalAssessorName" class="fw-bold"></span></h6>
                <div class="p-3 bg-light rounded border border-light">
                    <p class="mb-0 text-dark fs-15" id="modalAssessorComment" 
                        style="white-space: pre-wrap; word-break: break-word;">
                        -
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#table_candidate tbody, #table_final_candidate tbody').on('click', '.btn-view-comment', function() {
            let name = $(this).data('name');
            let comment = $(this).data('comment');
            let candidate = $(this).data('candidate');
            let status = $(this).data('status');
            $('#modalCandidateName').text(candidate);
            $('#modalAssessorName').text(name);
            let badge = $('#modalResultBadge');
            badge.removeClass('text-bg-success text-bg-danger');
            if (status == 1) {
                badge.addClass('text-bg-success');
                badge.text('PASSED');
            } else {
                badge.addClass('text-bg-danger');
                badge.text('REJECT');
            }
            if(!comment || comment === '-') {
                $('#modalAssessorComment').text('-');
            } else {
                $('#modalAssessorComment').text(comment);
            }
            var commentModal = new bootstrap.Modal(document.getElementById('commentModal'));
            commentModal.show();
        });
    });
</script>
