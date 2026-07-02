<?php

use App\Http\Controllers\HRD\EmployeeRewardController;
use App\Http\Controllers\HRD\AppraisalController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\HRD\AreaController;
use App\Http\Controllers\HRD\DepartmentController;
use App\Http\Controllers\HRD\EmployeeController;
use App\Http\Controllers\HRD\MedicalController;
use App\Http\Controllers\HRD\VendorController;
use App\Http\Controllers\HRD\SectionController;
use App\Http\Controllers\HRD\PositionController;
use App\Http\Controllers\HRD\LevelController;
use App\Http\Controllers\HRD\InternalruleController;
use App\Http\Controllers\HRD\TrainingController;
use App\Http\Controllers\HRD\BookingController;
use App\Http\Controllers\HRD\BuildingController;
use App\Http\Controllers\HRD\PKBController;
use App\Http\Controllers\HRD\CalendarController;
use App\Http\Controllers\HRD\LeaveController;
use App\Http\Controllers\HRD\RoomController;
use App\Http\Controllers\HRD\NewsandEventController;
use App\Http\Controllers\HRD\ClinicController;
use App\Http\Controllers\Recruitment\EmployeeRequisitionController;
use App\Http\Controllers\HRD\EvaluationController;
use App\Http\Controllers\Recruitment\HiringController;
use App\Http\Controllers\Recruitment\JobPostingController;
use App\Http\Controllers\HRD\LineApprovalController;
use App\Http\Controllers\HRD\MilestoneController;
use App\Http\Controllers\Recruitment\CandidateController;
use App\Http\Controllers\Recruitment\SelectionController;
use Illuminate\Support\Facades\Route;

//employees
Route::prefix('employees')->name('employee.')->middleware(["can:hrd.employee.read"])->controller(EmployeeController::class)->group(function () {
  Route::get('/', 'index')->name('index');
  Route::get('/form/{id?}', 'form')->name('form');
  Route::get('/detail/{id?}', 'detail')->name('detail');
  Route::get('/report', 'report')->name('report');
  Route::post('/', 'store')->name('store');
  Route::get('/export', 'exportExcel')->name('export');
  Route::post('/import', 'importExcel')->name('import');
  
  Route::prefix('milestone')->name('milestone.')->controller(MilestoneController::class)->group(function() {
    Route::get('/{id}', 'index')->name('index');
    Route::get('/{id}/load', 'loadMilestones')->name('load');
    Route::get('/{id}/edit', 'edit')->name('edit');
    Route::post('{id}/store', 'store')->name('store');
    Route::put('/{id}/update', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('delete');
  });
});

//internal rules
Route::prefix('internal-rules')->controller(InternalruleController::class)->group(function () {
  Route::get('/', 'index')->name('internal-rule.index')->middleware('can:hrd.internal-rules.read');
  Route::get('/edit', 'edit')->name('internal-rule.edit')->middleware('can:hrd.internal-rules.read');
  Route::get('/{id}/status', 'status')->name('internal-rule.status')->middleware('can:hrd.internal-rules.read');
  Route::post('/', 'store')->name('internal-rule.store')->middleware('can:hrd.internal-rules.read');
  Route::post('/revisi', 'revisi')->name('internal-rule.revisi')->middleware('can:hrd.internal-rules.read');
  Route::get('/{id}/pdf', 'lampiran_rule')->name('lampiran.rule')->middleware('can:hrd.internal-rules.read');
  Route::get('/{id}/download', 'download_rule')->name('download.rule')->middleware('can:hrd.internal-rules.read');
  Route::post('/setting', 'setting')->name('internal-rule.setting')->middleware('can:hrd.internal-rules.read');
  Route::get('/edit/setting', 'edit_setting')->name('internal-rule.edit.setting')->middleware('can:hrd.internal-rules.read');
  //Benefit
  Route::get('/benfit', 'index_benefit')->name('benefit.index')->middleware('can:hrd.benefit.read');
  Route::put('/create/benefit', 'store_benefit')->name('benefit.store')->middleware('can:hrd.benefit.read');
  Route::post('/setting/benefit', 'setting_benefit')->name('internal-rule.setting.benefit')->middleware('can:hrd.benefit.read');
  Route::get('/edit/setting/benefit', 'edit_setting_benefit')->name('internal-rule.edit.setting.benefit')->middleware('can:hrd.benefit.read');
  Route::put('/delete/benefit', 'destroy_benefit')->name('benefit.destroy')->middleware('can:hrd.benefit.read');
});

//news and event
Route::prefix('news-and-event')->controller(NewsandEventController::class)->group(function () {
  Route::get('/', 'index')->name('news-and-event.index')->middleware('can:hrd.news-and-event.read');
  Route::get('/{id}/preview', 'preview')->name('news-and-event.preview')->middleware('can:hrd.news-and-event.read');
  Route::get('/{id}/preview/detail', 'preview_detail')->name('news-and-event.preview.detail')->middleware('can:hrd.news-and-event.read');
  Route::get('/form/{id?}', 'form')->name('news-and-event.form')->middleware('can:hrd.news-and-event.read');
  Route::post('/', 'store')->name('news-and-event.store')->middleware('can:hrd.news-and-event.read');
  Route::post('uploads', 'uploads')->name('news-and-event.uploads')->middleware('can:hrd.news-and-event.read');
  Route::put('/delete', 'destroy')->name('news-and-event.destroy')->middleware('can:hrd.news-and-event.read');
  Route::get('/{id}/pdf', 'lampiran')->name('news-and-event.lampiran')->middleware('can:hrd.news-and-event.read');
});

//training
Route::prefix('training')->controller(TrainingController::class)->group(function () {
  Route::get('/', 'index')->name('training.index')->middleware('can:hrd.training.read');
  Route::get('/periode', 'periode')->name('training.periode')->middleware('can:hrd.training.read');
  Route::get('/edit/periode', 'periode_edit')->name('training.periode.edit')->middleware('can:hrd.training.read');
  Route::post('/periode', 'periode_store')->name('training.periode.store')->middleware('can:hrd.training.read');
  Route::get('/hrd/verified', 'hrd_verified')->name('training.hrd.verified')->middleware('can:hrd.training.read');
  Route::get('/hrd/approved', 'hrd_approved')->name('training.hrd.approved')->middleware('can:hrd.training.read');
  Route::get('/hrd/schedule', 'hrd_schedule')->name('training.hrd.schedule')->middleware('can:hrd.training.read');
  Route::get('/{id}/detail', 'training_detail')->name('training.detail')->middleware('can:hrd.training.read');
  Route::get('/edit', 'edit')->name('training.edit')->middleware('can:hrd.training.read');
  Route::post('/', 'update')->name('training.update')->middleware('can:hrd.training.read');
  Route::get('/{id}/pdf', 'lampiran_sertifikat')->name('lampiran.sertifikat')->middleware('can:hrd.training.read');
  //create jadwal
  Route::post('/store', 'store')->name('training.store')->middleware('can:hrd.training.read');

  //fpkt
  Route::get('/{id}/fpkt/form', 'fpkt_form')->name('training.fpkt.form')->middleware('can:hrd.training.read');
  Route::put('/fpkt/form', 'fpkt_form_store')->name('training.fpkt.form.store')->middleware('can:hrd.training.read');
  //laporan
  Route::get('/laporan', 'laporan_index')->name('training.laporan.index')->middleware('can:hrd.training.read');
  Route::get('/laporan/{id}/approval', 'laporan_approval')->name('training.laporan.approval')->middleware('can:hrd.training.read');
  Route::put('/laporan/approval/store', 'laporan_approval_store')->name('training.laporan.approval.store')->middleware('can:hrd.training.read');
});
//data training
Route::prefix('training/data')->controller(TrainingController::class)->group(function () {
  Route::get('/index', 'index_data')->name('training.data.index')->middleware('can:hrd.training.read');
  Route::get('/{id}/detail', 'training_data_detail')->name('training.data.detail')->middleware('can:hrd.training.read');
  Route::post('/', 'update_data')->name('training.data.update')->middleware('can:hrd.training.read');
  Route::get('/progress', 'index_proggress')->name('training.data.proggress')->middleware('can:hrd.training.read');
  Route::get('/progress/verification', 'index_verification_proggress')->name('training.data.verification.proggress')->middleware('can:hrd.training.read');
  Route::put('/verified/pti', 'store_verification_proggress')->name('training.data.verification.proggress.store')->middleware('can:hrd.training.read');
  Route::get('/progress/ptt', 'index_proggress_ptt')->name('training.data.proggress.ptt')->middleware('can:hrd.training.read');
  Route::get('/progress/verification/ptt', 'index_verification_proggress_ptt')->name('training.data.proggress.ptt.verified')->middleware('can:hrd.training.read');
  Route::put('/verified/ptt', 'store_verification_proggress_ptt')->name('training.data.verification.proggress.ptt.store')->middleware('can:hrd.training.read');
  Route::get('/progress/ptt/{id}/detail', 'index_proggress_ptt_detail')->name('training.data.proggress.ptt.detail')->middleware('can:hrd.training.read');
  Route::get('/progress/ptt/detail/back', 'index_proggress_ptt_detail_back')->name('training.data.proggress.ptt.detail.back')->middleware('can:hrd.training.read');
  Route::get('/progress/ptt/{id}/detail/verified', 'index_proggress_ptt_detail_verified')->name('training.data.proggress.ptt.detail.verified')->middleware('can:hrd.training.read');
  Route::get('/progress/ptt/detail/verified/back', 'index_proggress_ptt_detail_verified_back')->name('training.data.proggress.ptt.detail.verified.back')->middleware('can:hrd.training.read');
});
//scheduled training
Route::prefix('training/scheduled')->controller(TrainingController::class)->group(function () {
  Route::get('/', 'index_scheduled')->name('training.scheduled.index')->middleware('can:hrd.training.read');
  Route::post('/view', 'view_scheduled')->name('training.scheduled.view')->middleware('can:hrd.training.read');
  Route::put('/update', 'update_scheduled')->name('training.scheduled.update')->middleware('can:hrd.training.read');
});
//ptt training
Route::prefix('training/ptt')->controller(TrainingController::class)->group(function () {
  //active
  Route::get('/', 'index_ptt')->name('training.ptt.index')->middleware('can:hrd.training.read');//use
  Route::get('/{id}/form', 'form_ptt')->name('training.ptt.form')->middleware('can:hrd.training.read');
  Route::put('/store', 'store_ptt')->name('training.ptt.store')->middleware('can:hrd.training.read');//use
  Route::get('/{id}/fkt/pdf', 'fkt_pdf')->name('training.ptt.fkt.pdf')->middleware('can:hrd.training.read');//use
  //finished
  Route::get('/finished', 'ptt_finished')->name('training.ptt.finished')->middleware('can:hrd.training.read');
  //create jadwal
  Route::post('/schedule/store', 'ptt_schedule_store')->name('training.ptt.schedule.store')->middleware('can:hrd.training.read');
  Route::get('/schedule', 'ptt_schedule')->name('training.ptt.schedule')->middleware('can:hrd.training.read');
  //qrcode fkt
  Route::get('/{code}/{id}/pemohon', 'qrcode_pemohon')->name('training.ptt.qrcode.pemohon')->middleware('can:hrd.training.read');
  Route::get('/{code}/{id}/checker', 'qrcode_checker')->name('training.ptt.qrcode.checker')->middleware('can:hrd.training.read');
  Route::get('/{code}/{id}/verified', 'qrcode_verified')->name('training.ptt.qrcode.verified')->middleware('can:hrd.training.read');
  Route::get('/{code}/{id}/approval', 'qrcode_approval')->name('training.ptt.qrcode.approval')->middleware('can:hrd.training.read');
  
  Route::get('/{id}/fpkt/pdf', 'fpkt_pdf')->name('training.ptt.fpkt.pdf')->middleware('can:hrd.training.read');
  //qrcode fpkt
  Route::get('/{code}/{id}/fpkt', 'qrcode_fpkt')->name('training.ptt.qrcode.fpkt')->middleware('can:hrd.training.read');
  //notification mr.mizukami and mr.sakurai
  Route::put('/notification', 'notification_ptt')->name('training.ptt.notification')->middleware('can:hrd.training.read');
});
//pti training
Route::prefix('training/pti')->controller(TrainingController::class)->group(function () {
  //active
  Route::get('/', 'index_pti')->name('training.pti.index')->middleware('can:hrd.training.read');
  Route::get('/fpkt/{id}/form', 'form_fpkt_pti')->name('training.fpkt.pti.form')->middleware('can:hrd.training.read');
  Route::put('/store', 'pti_store')->name('training.pti.store')->middleware('can:hrd.training.read'); //use
  Route::get('/{id}/fkt/pdf', 'fkt_pti_pdf')->name('training.pti.fkt.pdf')->middleware('can:hrd.training.read');

  //qrcode fkt
  Route::get('/{code}/{id}/pemohon', 'qrcode_pemohon_pti')->name('training.pti.qrcode.pemohon')->middleware('can:hrd.training.read');
  Route::get('/{code}/{id}/checker', 'qrcode_checker_pti')->name('training.pti.qrcode.checker')->middleware('can:hrd.training.read');
  Route::get('/{code}/{id}/verified', 'qrcode_verified_pti')->name('training.pti.qrcode.verified')->middleware('can:hrd.training.read');
  Route::get('/{code}/{id}/approval', 'qrcode_approval_pti')->name('training.pti.qrcode.approval')->middleware('can:hrd.training.read');

  Route::get('/{id}/fpkt/pdf', 'fpkt_pti_pdf')->name('training.pti.fpkt.pdf')->middleware('can:hrd.training.read'); //use
  //qrcode fpkt
  Route::get('/{code}/{id}/fpkt', 'qrcode_fpkt_pti')->name('training.pti.qrcode.fpkt')->middleware('can:hrd.training.read');
  //status
  Route::post('/status', 'pti_status')->name('training.pti.status')->middleware('can:hrd.training.read'); //use

  //finished
  Route::get('/finished', 'pti_finished')->name('training.pti.finished')->middleware('can:hrd.training.read');
  //create jadwal
  Route::post('/schedule/store', 'pti_schedule_store')->name('training.pti.schedule.store')->middleware('can:hrd.training.read');
  Route::get('/schedule', 'pti_schedule')->name('training.pti.schedule')->middleware('can:hrd.training.read');
});


//PKB
Route::prefix('pkb')->controller(PKBController::class)->group(function () {
  Route::get('/', 'index')->name('pkb.index')->middleware('can:hrd.pkb.read');
  Route::get('/edit', 'edit')->name('pkb.edit')->middleware('can:hrd.pkb.read');
  Route::post('/', 'store')->name('pkb.store')->middleware('can:hrd.pkb.read');
  Route::get('/{id}/pdf', 'lampiran_pkb')->name('lampiran.pkb')->middleware('can:hrd.pkb.read');
});

//booking room
Route::prefix('booking-room')->controller(BookingController::class)->group(function () {
  Route::get('/', 'index')->name('booking-room.index')->middleware('can:hrd.booking-room.read');
  Route::post('/', 'store')->name('booking-room.store')->middleware('can:hrd.booking-room.read');
  Route::post('/api-view', 'view')->name('booking-room.view')->middleware('can:hrd.booking-room.read');
  Route::post('/update', 'update')->name('booking-room.update')->middleware('can:hrd.booking-room.read');
  Route::post('/delete', 'delete')->name('booking-room.delete')->middleware('can:hrd.booking-room.read');
  Route::post('/update/series', 'update_series')->name('booking-room.update.series')->middleware('can:hrd.booking-room.read');
  Route::post('/delete/series', 'delete_series')->name('booking-room.delete.series')->middleware('can:hrd.booking-room.read');
});

//calendar
Route::prefix('calendar')->controller(CalendarController::class)->group(function () {
  Route::get('/', 'index')->name('calendar.index')->middleware('can:hrd.calendar.read');
  Route::post('/', 'store')->name('calendar.store')->middleware('can:hrd.calendar.read');
  Route::get('/edit', 'edit')->name('calendar.edit')->middleware('can:hrd.calendar.read');
  Route::get('/{id}/detail', 'detail')->name('calendar.detail')->middleware('can:hrd.calendar.read');
  Route::get('/{id}/view-pdf', 'view_pdf')->name('calendar.pdf')->middleware('can:hrd.calendar.read');
  Route::post('/upload', 'upload')->name('calendar.upload')->middleware('can:hrd.calendar.read');
  Route::get('/{id}/download', 'download')->name('calendar.download')->middleware('can:hrd.calendar.read');
  //event calendar
  Route::post('/view', 'view')->name('calendar.view')->middleware('can:hrd.calendar.read');
  Route::put('/event', 'event_calendar_store')->name('event.calendar.store')->middleware('can:hrd.calendar.read');
  Route::put('/event/update', 'event_calendar_update')->name('event.calendar.update')->middleware('can:hrd.calendar.read');
  Route::put('/event/delete', 'event_calendar_delete')->name('event.calendar.delete')->middleware('can:hrd.calendar.read');
});

//medicals
Route::prefix('medical/reguler')->controller(MedicalController::class)->group(function () {
  //reguler
  Route::get('/', 'reguler_index')->name('reguler.index')->middleware('can:hrd.medical-record.reguler.read');  
  Route::get('/form', 'reguler_form')->name('reguler.form')->middleware('can:hrd.medical-record.reguler.read');  
  Route::get('/{id}/detail', 'reguler_detail')->name('reguler.detail')->middleware('can:hrd.medical-record.reguler.read');  
  Route::get('/{id}/medical', 'reguler_medical')->name('reguler.medical')->middleware('can:hrd.medical-record.reguler.read');  
  Route::get('/{id}/pdf', 'lampiran_mcu')->name('lampiran.mcu')->middleware('can:hrd.medical-record.reguler.read');  
  Route::post('/edit', 'reguler_edit')->name('reguler.edit')->middleware('can:hrd.medical-record.reguler.read');  
  Route::post('/store', 'reguler_store')->name('reguler.store')->middleware('can:hrd.medical-record.reguler.read');  
  Route::get('/{id}/upload', 'reguler_upload')->name('reguler.upload')->middleware('can:hrd.medical-record.reguler.read');  
  Route::post('/api-upload', 'api_reguler_upload')->name('api.reguler.upload')->middleware('can:hrd.medical-record.reguler.read');  
  Route::post('medicals-import', 'import')->name('medicals.import')->middleware('can:hrd.medical-record.reguler.read');
  Route::post('/update', 'reguler_update')->name('reguler.update')->middleware('can:hrd.medical-record.reguler.read');
  Route::post('/api-update', 'reguler_api_update')->name('reguler.api.update')->middleware('can:hrd.medical-record.reguler.read');
  Route::put('/destroy', 'reguler_destroy')->name('reguler.destroy')->middleware('can:hrd.medical-record.reguler.read');
  Route::get('/{id}/export', 'reguler_export')->name('reguler.export')->middleware('can:hrd.medical-record.reguler.read');  
});

Route::prefix('medical/ireguler')->controller(MedicalController::class)->group(function () {
  //ireguler
  Route::get('/', 'ireguler_index')->name('ireguler.index')->middleware('can:hrd.medical-record.ireguler.read');  
  Route::get('/form', 'ireguler_form')->name('ireguler.form')->middleware('can:hrd.medical-record.ireguler.read');
  Route::post('/store', 'ireguler_store')->name('ireguler.store')->middleware('can:hrd.medical-record.ireguler.read');    
  Route::post('/update', 'ireguler_update')->name('ireguler.update')->middleware('can:hrd.medical-record.ireguler.read');
  //generate pdf
  Route::post('/surat-mcu', 'generate_pdf')->name('ireguler.generate.pdf')->middleware('can:hrd.medical-record.ireguler.read');

});

//clinic
Route::prefix('clinic')->controller(ClinicController::class)->group(function () {
  //stock
  Route::get('/stock', 'index_stock')->name('clinic.stock.index')->middleware('can:hrd.clinic.stock.read');
  //masuk
  Route::get('/in', 'index_masuk')->name('clinic.masuk.index')->middleware('can:hrd.clinic.masuk.read');
  Route::get('/in/create', 'create_masuk')->name('clinic.masuk.create')->middleware('can:hrd.clinic.masuk.read');
  Route::put('/in', 'store_masuk')->name('clinic.masuk.store')->middleware('can:hrd.clinic.masuk.read');
  Route::put('/in/destroy', 'destroy_masuk')->name('clinic.masuk.destroy')->middleware('can:hrd.clinic.masuk.read');
  // Route::post('/api-view', 'view')->name('booking-room.view')->middleware('can:hrd.booking-room.read');
  // Route::post('/update', 'update')->name('booking-room.update')->middleware('can:hrd.booking-room.read');
  //keluar
  Route::get('/out', 'index_keluar')->name('clinic.keluar.index')->middleware('can:hrd.clinic.keluar.read');
  Route::get('/out/create', 'create_keluar')->name('clinic.keluar.create')->middleware('can:hrd.clinic.keluar.read');
  Route::put('/out', 'store_keluar')->name('clinic.keluar.store')->middleware('can:hrd.clinic.keluar.read');
  Route::put('/out/destroy', 'destroy_keluar')->name('clinic.keluar.destroy')->middleware('can:hrd.clinic.keluar.read');
  //patient
  Route::get('/patient', 'index_patient')->name('clinic.patient.index')->middleware('can:hrd.clinic.patient.read');
  Route::get('/patient/create', 'create_patient')->name('clinic.patient.create')->middleware('can:hrd.clinic.patient.read');
  Route::put('/patient/store', 'store_patient')->name('clinic.patient.store')->middleware('can:hrd.clinic.patient.read');
  Route::get('/patient/log', 'log_patient')->name('clinic.patient.log')->middleware('can:hrd.clinic.patient.read');
  Route::put('/patient/destroy', 'destroy_patient')->name('clinic.patient.destroy')->middleware('can:hrd.clinic.patient.read');
  Route::post('/api/patient/medical', 'medical_year_patient')->name('clinic.patient.medical.year')->middleware('can:hrd.clinic.patient.read');
  Route::post('/patient/medical', 'medical_patient')->name('clinic.patient.medical')->middleware('can:hrd.clinic.patient.read');
  Route::get('/patient/export', 'export_patient')->name('patient.export')->middleware('can:hrd.clinic.patient.read');
  Route::post('/api/preview-mcu', 'medical_patient_mcu_pdf')->name('clinic.patient.medical.mcu.pdf')->middleware('can:hrd.clinic.patient.read');
  Route::get('/{id}/mcu/pdf', 'lampiran_patient_mcu')->name('lampiran.patient.mcu')->middleware('can:hrd.clinic.patient.read');
  //opname
  Route::get('/opname', 'index_opname')->name('clinic.opname.index')->middleware('can:hrd.clinic.opname.read');
  Route::get('/opname/create', 'create_opname')->name('clinic.opname.create')->middleware('can:hrd.clinic.opname.read');
  Route::post('/opname-select-stock', 'select_stock_opname')->name('clinic.opname.select.stock')->middleware('can:hrd.clinic.opname.read');
  Route::put('/opname', 'store_opname')->name('clinic.opname.store')->middleware('can:hrd.clinic.opname.read');
});

//master
Route::prefix('master/area')->controller(AreaController::class)->group(function () {
  Route::get('/', 'index')->name('area.index')->middleware('can:hrd.master.area.read');
  Route::get('/edit', 'edit')->name('area.edit')->middleware('can:hrd.master.area.read');
  Route::post('/', 'store')->name('area.store')->middleware('can:hrd.master.area.read');
});

Route::prefix('master/department')->controller(DepartmentController::class)->group(function () {
  Route::get('/', 'index')->name('department.index')->middleware('can:hrd.master.department.read');
  Route::get('/edit', 'edit')->name('department.edit')->middleware('can:hrd.master.department.read');
  Route::post('/', 'store')->name('department.store')->middleware('can:hrd.master.department.read');
});

Route::prefix('master/vendor')->controller(VendorController::class)->group(function () {
  Route::get('/', 'index')->name('vendor.index')->middleware('can:hrd.master.vendor.read');
  Route::get('/edit', 'edit')->name('vendor.edit')->middleware('can:hrd.master.vendor.read');
  Route::post('/store', 'store')->name('vendor.store')->middleware('can:hrd.master.vendor.read');
});

Route::prefix('master/section')->controller(SectionController::class)->group(function () {
  Route::get('/', 'index')->name('section.index')->middleware('can:hrd.master.section.read');
  Route::get('/edit', 'edit')->name('section.edit')->middleware('can:hrd.master.section.read');
  Route::post('/store', 'store')->name('section.store')->middleware('can:hrd.master.section.read');
});

Route::prefix('master/position')->controller(PositionController::class)->group(function () {
  Route::get('/', 'index')->name('position.index')->middleware('can:hrd.master.position.read');
  Route::get('/edit', 'edit')->name('position.edit')->middleware('can:hrd.master.position.read');
  Route::post('/store', 'store')->name('position.store')->middleware('can:hrd.master.position.read');
});

Route::prefix('master/level')->controller(LevelController::class)->group(function () {
  Route::get('/', 'index')->name('level.index')->middleware('can:hrd.master.level.read');
  Route::get('/edit', 'edit')->name('level.edit')->middleware('can:hrd.master.level.read');
  Route::post('/store', 'store')->name('level.store')->middleware('can:hrd.master.level.read');
});
Route::prefix('master/leave')->controller(LeaveController::class)->group(function () {
  Route::get('/', 'index')->name('leave.index')->middleware('can:hrd.master.leave.read');
  Route::get('/edit', 'edit')->name('leave.edit')->middleware('can:hrd.master.leave.read');
  Route::post('/store', 'store')->name('leave.store')->middleware('can:hrd.master.leave.read');
});
Route::prefix('master/room')->controller(RoomController::class)->group(function () {
  Route::get('/', 'index')->name('room.index')->middleware('can:hrd.master.room.read');
  Route::get('/edit', 'edit')->name('room.edit')->middleware('can:hrd.master.room.read');
  Route::post('/store', 'store')->name('room.store')->middleware('can:hrd.master.room.read');
});
Route::prefix('master/drug')->controller(MasterController::class)->group(function () {
  Route::get('/', 'drug_index')->name('drug.index')->middleware('can:hrd.master.drug.read');
  Route::get('/edit', 'drug_edit')->name('drug.edit')->middleware('can:hrd.master.drug.read');
  Route::post('/store', 'drug_store')->name('drug.store')->middleware('can:hrd.master.drug.read');
});

Route::prefix('master/appraisal')->controller(AppraisalController::class)->group(function () {
  Route::get('/', 'index')->name('appraisal.index')->middleware('can:hrd.master.appraisal.read');
  Route::get('/form/{id?}', 'form')->name('appraisal.form')->middleware('can:hrd.master.appraisal.create');
  Route::post('/store', 'store')->name('appraisal.store')->middleware('can:hrd.master.appraisal.create');
  Route::put('/delete', 'destroy')->name('appraisal.destroy')->middleware('can:hrd.master.appraisal.update');
});
Route::prefix('master/building')->controller(BuildingController::class)->group(function () {
  Route::get('/', 'index')->name('building.index')->middleware('can:hrd.master.building.read');
  Route::get('/edit', 'edit')->name('building.edit')->middleware('can:hrd.master.building.update');
  Route::post('/store', 'store')->name('building.store')->middleware('can:hrd.master.building.create');
  Route::delete('/delete', 'destroy')->name('building.destroy')->middleware('can:hrd.master.building.delete');
});
Route::prefix('master/line-approval')->controller(LineApprovalController::class)->group(function () {
  Route::get('/', 'index')->name('line-approval.index')->middleware('can:hrd.master.line-approval.read');
  Route::get('/export_xlsx', 'export_xlsx')->name('line-approval.export_xlsx')->middleware('can:hrd.master.line-approval.export_xlsx');
  Route::get('/form/{id?}', 'form')->name('line-approval.form')->middleware('can:hrd.master.line-approval.create');
  Route::get('/get-employees','getEmployees')->name('line-approval.get-employees')->middleware('can:hrd.master.line-approval.create');
  Route::post('/store', 'store')->name('line-approval.store')->middleware('can:hrd.master.line-approval.create');
  Route::put('/delete', 'destroy')->name('line-approval.destroy')->middleware('can:hrd.master.line-approval.delete');
  Route::get('/get-eligible-employees','getEligibleEmployees')->name('line-approval.get-eligible-employees')->middleware('can:hrd.master.line-approval.create');
});
Route::prefix('master/hiring')->controller(HiringController::class)->group(function () {
  Route::get('/', 'index')->name('hiring.index')->middleware('can:hrd.master.hiring.read');
  Route::get('/edit', 'edit')->name('hiring.edit')->middleware('can:hrd.master.hiring.update');
  Route::post('/store', 'store')->name('hiring.store')->middleware('can:hrd.master.hiring.create');
  Route::delete('/delete', 'destroy')->name('hiring.destroy')->middleware('can:hrd.master.hiring.delete');
});
Route::prefix('master/contract')->controller(MasterController::class)->group(function () {
  Route::get('/', 'contract_index')->name('contract.index')->middleware('can:hrd.master.contract.read');
  Route::get('/form/{id?}', 'contract_form')->name('contract.form')->middleware('can:hrd.master.contract.create');
  Route::post('/store', 'contract_store')->name('contract.store')->middleware('can:hrd.master.contract.create');
  Route::delete('/delete', 'contract_destroy')->name('contract.destroy')->middleware('can:hrd.master.contract.delete');
});

// evaluations
Route::prefix('evaluation/process')->controller(EvaluationController::class)->middleware('can:hrd.evaluation.read')->group(function () {
  Route::get('/', 'index')->name('evaluation.index');
  Route::get('/form/{id?}', 'form')->name('evaluation.form');
  Route::get('/detail/{id?}', 'detail')->name('evaluation.detail');
  Route::post('/store', 'store')->name('evaluation.store');
  Route::delete('/delete', 'destroy')->name('evaluation.destroy')->middleware('can:hrd.evaluation.delete');
  Route::get('/get-appraisals/{employee_id}', 'getAppraisals')->name('evaluation.get-appraisals');
  Route::post('/release', 'releaseMultiple')->name('evaluation.release-multiple');
  Route::get('/get-evaluators/{employee_id}', 'getEvaluators')->name('evaluation.get-evaluators');
  Route::get('/create/multiple', 'createMultiple')->name('evaluation.create-multiple');
  Route::get('/create/multiple/getEmployee', 'createMultiple_getEmployees')->name('evaluation.create-multiple.getEmployee');
  Route::post('/create/multiple/store', 'createMultiple_store')->name('evaluation.create-multiple.store');
  Route::get('/steps/{id?}', 'getEvaluationSteps')->name('evaluation.process.steps');
  Route::post('/detail/attach/store/', 'detailAttachStore')->name('evaluation.detail.attach.store');
  Route::post('/detail/notes-hrd/store/', 'detailNotesHRDStore')->name('evaluation.detail.notes-hrd.store');
  Route::post('/edit/multiple/token', 'editMultiple_token')->name('evaluation.edit-multiple.token');
  Route::get('/edit/multiple/{token}', 'editMultiple')->name('evaluation.edit-multiple');
  Route::post('/edit/multiple/store', 'editMultiple_store')->name('evaluation.edit-multiple.store');
  Route::get('/decision/reason/{id?}', 'getDecisionReason')->name('evaluation.process.reason');
  Route::get('/export_xlsx', 'process_export_xlsx')->name('evaluation.process.export_xlsx');
});
Route::prefix('evaluation/done')->controller(EvaluationController::class)->middleware('can:hrd.evaluation.read')->group(function () {
  Route::get('/', 'index_done')->name('evaluation.done.index');
  Route::get('/detail/{id?}', 'detail')->name('evaluation.done.detail');
  Route::get('/print/{evaluation}', 'print')->name('evaluation.done.print');
  Route::get('/steps/{id?}', 'getEvaluationSteps')->name('evaluation.done.steps');
  Route::get('/getEvalHistory', 'getEvalHistory')->name('evaluation.done.getEvalHistory');
  Route::get('/decision/reason/{id?}', 'getDecisionReason')->name('evaluation.done.reason');
  Route::get('/resume/print/{token}', 'resume_print')->name('evaluation.done.resume.print');
  Route::post('/resume/print/token', 'resume_token')->name('evaluation.done.resume.print.token');
  Route::get('/export_xlsx', 'done_export_xlsx')->name('evaluation.done.export_xlsx');
});
Route::prefix('evaluation/schedule')->controller(EvaluationController::class)->middleware('can:hrd.evaluation.read')->group(function () {
  Route::get('/', 'index_schedule')->name('evaluation.schedule.index');
  Route::get('/getYearly', 'getYearly')->name('evaluation.schedule.getYearly');
  Route::post('/validate/multiple', 'validateMultiple_schedule')->name('evaluation.schedule.validate-multiple'); 
  Route::post('/create/multiple', 'createMultiple_schedule')->name('evaluation.schedule.create-multiple');
});

// employee requisition
Route::prefix('recruitment/er')->controller(EmployeeRequisitionController::class)->middleware('can:hrd.recruitment.read')->group(function () {
  Route::get('/', 'index')->name('employee-requisition.index');
  Route::get('/detail/{id}', 'detail_hrd')->name('employee-requisition.detail');
  Route::patch('/detail/recruitment/source/{token}', 'detailRecSourceHRDStore')->name('employee-requisition.detail.recruitment.source');
  Route::get('/print/{er}', 'print')->name('employee-requisition.print');
  Route::get('/steps/{id}', 'getERSteps')->name('employee-requisition.steps');
  Route::delete('/delete', 'destroy')->name('employee-requisition.delete')->middleware('can:hrd.employee-requisition.delete');
  Route::post('/steps/selection/{id}', 'stepSelectionStore')->name('employee-requisition.store.steps');
  Route::patch('/detail/close/{id}', 'closeRequisition')->name('employee-requisition.detail.recruitment.close');
  Route::post('/hire/{id}', 'storeHiredCandidates')->name('employee-requisition.detail.recruitment.hire');
});
// job posting
Route::prefix('recruitment/jp')->controller(JobPostingController::class)->middleware('can:hrd.recruitment.read')->group(function () {
  Route::get('/', 'index')->name('job-posting.index');
  Route::get('/form/{id?}', 'form')->name('job-posting.form');
  Route::get('/detail/{id}', 'detail')->name('job-posting.detail');
  Route::post('/store', 'store')->name('job-posting.store');
  Route::delete('/delete', 'destroy')->name('job-posting.destroy')->middleware('can:hrd.job-posting.delete');
  Route::get('/get-requisition/{requisition_id}', 'getRequisition')->name('job-posting.get-requisition');
  Route::post('/publish', 'publishMultiple')->name('job-posting.publish-multiple');
  Route::put('/update-status', 'updateStatus')->name('job-posting.update-status');
});
// candidate
Route::prefix('recruitment/candidate')->controller(CandidateController::class)->middleware('can:hrd.recruitment.read')->group(function () {
  Route::get('/', 'index')->name('candidate.index');
  Route::get('/detail/{id}', 'detail')->name('candidate.detail');
  Route::delete('/delete', 'destroy')->name('candidate.delete')->middleware('can:hrd.candidate.delete');
});
// selection
Route::prefix('recruitment/selection')->controller(SelectionController::class)->middleware('can:hrd.recruitment.read')->group(function () {
  Route::get('/', 'index')->name('selection.index');
  Route::get('/form/{id?}', 'form')->name('selection.form');
  Route::post('/store', 'store')->name('selection.store');
  Route::get('/getSteps/{requisition}', 'getSteps')->name('selection.getSteps');
  Route::post('/getCandidate', 'getCandidates')->name('selection.getCandidates');
  Route::get('/steps/{id}', 'getSelectionSteps')->name('selection.steps');
  Route::delete('/delete', 'destroy')->name('selection.delete')->middleware('can:hrd.selection.delete');
  Route::get('/passed/{id}', 'passed')->name('selection.passed');
  Route::post('/passed/store', 'passed_store')->name('selection.passed.store');
  Route::post('/passed/update/attendance', 'updateAttendance')->name('selection.passed.updateAttendance');
  Route::get('/detail/{id}', 'detail')->name('selection.detail');
  Route::post('/upload/attachment', 'upload_attachment')->name('selection.upload.attachment');
  Route::post('/delete/attachment', 'delete_attachment')->name('selection.delete.attachment');
});