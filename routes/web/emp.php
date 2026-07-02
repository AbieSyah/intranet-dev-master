<?php

use App\Http\Controllers\HRD\MedicalController;
use App\Http\Controllers\HRD\CalendarController;
use App\Http\Controllers\HRD\InternalruleController;
use App\Http\Controllers\HRD\PKBController;
use App\Http\Controllers\HRD\BookingController;
use App\Http\Controllers\Recruitment\EmployeeRequisitionController;
use App\Http\Controllers\HRD\EvaluationController;
use App\Http\Controllers\HRD\TrainingController;
use App\Http\Controllers\Recruitment\SelectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('internal-rule')->controller(InternalruleController::class)->group(function () {
  Route::get('/', 'emp_index')->name('internal-rule.emp.index')->middleware('can:emp.internal-rule.read');
  Route::get('/{id}/pdf', 'emp_lampiran_rule')->name('emp.lampiran.rule')->middleware('can:emp.internal-rule.read');
  Route::get('/{id}/download', 'emp_download_rule')->name('emp.download.rule')->middleware('can:emp.internal-rule.read');
});
Route::prefix('benefit')->controller(InternalruleController::class)->group(function () {
  Route::get('/', 'emp_benefit')->name('benefit.emp.index')->middleware('can:emp.benefit.read');
  Route::get('/{id}/pdf', 'emp_benefit_rule')->name('benefit.emp.rule')->middleware('can:emp.benefit.read');
});
Route::prefix('medical')->controller(MedicalController::class)->group(function () {
  Route::get('/', 'emp_index')->name('medical.emp.index')->middleware('can:emp.medical.read');
});
Route::prefix('pkb')->controller(PKBController::class)->group(function () {
  Route::get('/', 'emp_index')->name('pkb.emp.index')->middleware('can:emp.pkb.read');
});
Route::prefix('booking-room')->controller(BookingController::class)->group(function () {
  Route::get('/', 'emp_index')->name('booking-room.emp.index')->middleware('can:emp.booking-room.read');
  Route::post('/', 'emp_store')->name('booking-room.emp.store')->middleware('can:emp.booking-room.read');
  Route::post('/api-view', 'emp_view')->name('booking-room.emp.view')->middleware('can:emp.booking-room.read');
  Route::post('/update', 'emp_update')->name('booking-room.emp.update')->middleware('can:emp.booking-room.read');
  Route::post('/delete', 'emp_delete')->name('booking-room.emp.delete')->middleware('can:emp.booking-room.read');
  Route::post('/update/series', 'emp_update_series')->name('booking-room.emp.update.series')->middleware('can:emp.booking-room.read');
  Route::post('/delete/series', 'emp_delete_series')->name('booking-room.emp.delete.series')->middleware('can:emp.booking-room.read');
});
Route::prefix('calendar')->controller(CalendarController::class)->group(function () {
  Route::get('/', 'emp_index')->name('calendar.emp.index')->middleware('can:emp.calendar.read');
  // Route::get('/{id}/detail', 'emp_detail')->name('calendar.emp.detail')->middleware('can:emp.calendar.read');
  Route::get('/{id}/download', 'emp_download')->name('calendar.emp.download')->middleware('can:emp.calendar.read');
  Route::post('/view', 'emp_view')->name('calendar.emp.view')->middleware('can:emp.calendar.read');
});
Route::prefix('training')->controller(TrainingController::class)->group(function () {
  // my training
  Route::get('/', 'emp_index')->name('training.emp.index'); //use
  Route::put('/record/jadwal/store', 'emp_jadwal_store')->name('training.emp.jadwal.store'); //use
  //laporan
  Route::get('/laporan', 'emp_index_laporan')->name('training.emp.index.laporan');
  Route::get('/approval/{id}/laporan', 'emp_approval_laporan')->name('training.emp.approval.laporan');
  Route::get('/{id}/create/laporan', 'emp_create_laporan')->name('training.emp.create.laporan');
  Route::put('/store/laporan', 'emp_store_laporan')->name('training.emp.store.laporan');
  Route::put('/store/approval/laporan', 'emp_store_approval_laporan')->name('training.emp.store.approval.laporan');
  Route::get('/back/approval/laporan', 'emp_back_approval_laporan')->name('training.emp.back.approval.laporan');
  //evaluasi
  Route::get('/{id}/evaluasi/laporan', 'emp_evaluasi_laporan')->name('training.emp.evaluasi.laporan');
  Route::post('/check/evaluasi/laporan', 'emp_check_evaluasi_laporan')->name('training.emp.check.evaluasi.laporan');
  Route::put('/store/evaluasi/laporan', 'emp_store_evaluasi_laporan')->name('training.emp.store.evaluasi.laporan');
  //ptt
  ///fkt
  ////pengajuan
  Route::get('/fkt/ptt', 'emp_index_fkt_ptt')->name('training.emp.index.fkt.ptt'); //use
  Route::post('/fkt/ptt/status', 'emp_fkt_ptt_status')->name('training.emp.fkt.ptt.status'); //use
  Route::get('/fkt/ptt/create', 'emp_fkt_ptt_create')->name('training.emp.fkt.ptt.create'); //use
  Route::get('/fkt/ptt/{id}/edit', 'emp_fkt_ptt_edit')->name('training.emp.fkt.ptt.edit'); //use
  Route::get('/fkt/ptt/{id}/detail', 'emp_fkt_ptt_detail')->name('training.emp.fkt.ptt.detail');
  Route::put('/fkt/ptt/store', 'emp_fkt_ptt_store')->name('training.emp.fkt.ptt.store'); //use
  Route::put('/fkt/ptt/update', 'emp_fkt_ptt_update')->name('training.emp.fkt.ptt.update'); //use
  ////approved
  Route::get('/fkt/ptt/approved', 'emp_fkt_ptt_approved')->name('training.emp.fkt.ptt.approved'); //use
  Route::get('/fkt/ptt/approved/{id}/form', 'emp_fkt_ptt_approved_form')->name('training.emp.fkt.ptt.approved.form'); //use
  Route::put('/fpkt/ptt/approved/store', 'emp_fpkt_ptt_approved_store')->name('training.emp.fpkt.ptt.approved.store'); //use
  Route::put('/fpkt/ptt/revised/store', 'emp_fpkt_ptt_revised_store')->name('training.emp.fpkt.ptt.revised.store'); //use
  Route::put('/fpkt/ptt/rejected/store', 'emp_fpkt_ptt_rejected_store')->name('training.emp.fpkt.ptt.rejected.store'); //use
  ////back
  Route::get('/back-pengajuan-ptt', 'emp_fkt_ptt_back')->name('training.emp.fkt.ptt.back'); //use
  Route::get('/back-approve-ptt', 'emp_fkt_ptt_approve_back')->name('training.emp.fkt.ptt.approve.back'); //use
  ///fpkt
  Route::get('/fpkt/ptt/{id}/form', 'emp_fpkt_ptt_form')->name('training.emp.fpkt.ptt.form');
  Route::put('/fpkt/ptt/store', 'emp_fpkt_ptt_store')->name('training.emp.fpkt.ptt.store');
  ///collective ptt
  Route::get('/fpkt/ptt/collective/{id}/form', 'emp_fpkt_ptt_collective')->name('training.emp.fpkt.ptt.collective');
  Route::get('/fpkt/ptt/collective/approve/{id}/form', 'emp_fpkt_ptt_collective_approve')->name('training.emp.fpkt.ptt.collective.approve');
  //pti
  ///fpkt
  ///pengajuan
  Route::get('/fkt/pti', 'emp_index_fkt_pti')->name('training.emp.index.fkt.pti'); //use
  Route::post('/api-select-usulan-program/pti', 'emp_select_usulan_pti')->name('training.emp.select.usulan.pti'); //use
  Route::post('/api-select-pelatihan/pti', 'emp_select_pelatihan_pti')->name('training.emp.select.pelatihan.pti'); //use
  Route::post('/fkt/pti/status', 'emp_fkt_pti_status')->name('training.emp.fkt.pti.status'); //use
  Route::post('/fkt/pti/api-create', 'emp_fkt_pti_select_create')->name('training.emp.fkt.pti.select.create'); //use
  Route::get('/fkt/pti/create', 'emp_fkt_pti_create')->name('training.emp.fkt.pti.create');
  Route::get('/fkt/pti/{id}/edit', 'emp_fkt_pti_edit')->name('training.emp.fkt.pti.edit');
  Route::get('/fkt/pti/{id}/detail', 'emp_fkt_pti_detail')->name('training.emp.fkt.pti.detail'); //use
  Route::put('/fkt/pti/store', 'emp_fkt_pti_store')->name('training.emp.fkt.pti.store'); //use
  Route::put('/fkt/pti/update', 'emp_fkt_pti_update')->name('training.emp.fkt.pti.update');
  ////approved
  Route::get('/fkt/pti/approved', 'emp_fkt_pti_approved')->name('training.emp.fkt.pti.approved'); //use
  Route::put('/fpkt/pti/approved/store', 'emp_fpkt_pti_approved_store')->name('training.emp.fpkt.pti.approved.store'); //use
  ////back
  Route::get('/back-pengajuan-pti', 'emp_fkt_pti_back')->name('training.emp.fkt.pti.back');
  Route::get('/back-pengajuan-approve-pti', 'emp_fkt_pti_approve_back')->name('training.emp.fkt.pti.approve.back');
  ///fpkt
  Route::get('/fpkt/pti/{id}/form', 'emp_fpkt_pti_form')->name('training.emp.fpkt.pti.form'); //use
  Route::put('/fpkt/pti/store', 'emp_fpkt_pti_store')->name('training.emp.fpkt.pti.store'); //use
  ///collective pti
  Route::get('/fpkt/pti/collective/{id}/form', 'emp_fpkt_pti_collective')->name('training.emp.fpkt.pti.collective');
  Route::get('/fpkt/pti/collective/approve/{id}/form', 'emp_fpkt_pti_collective_approve')->name('training.emp.fpkt.pti.collective.approve');
});

//evaluations
Route::prefix('evaluation')->controller(EvaluationController::class)->group(function () {
  Route::get('/', 'emp_index')->name('evaluation.emp.index')->middleware('can:emp.evaluation.read');
  Route::post('/approve/multiple', 'approveMultiple')->name('evaluation.emp.approveMultiple')->middleware('can:emp.evaluation.read');
  Route::get('/approve/multiple/print/{token}', 'approveMultiple_print')->name('evaluation.emp.approveMultiple.print');
  Route::post('/approve/multiple/print/token', 'approveMultiple_token')->name('evaluation.emp.approveMultiple.print.token');
  Route::get('/evaluations/process', 'getProcess')->name('evaluation.emp.process');
  Route::get('/evaluations/countprocess', 'countProcess')->name('evaluation.emp.countprocess');
  Route::get('/evaluations/done', 'getDone')->name('evaluation.emp.done');
  Route::get('/detail/{id?}', 'emp_detail')->name('evaluation.emp.detail');
  Route::get('/print/{evaluation}', 'print')->name('evaluation.emp.print');
  Route::get('/steps/{id?}', 'getEvaluationSteps')->name('evaluation.emp.steps');

  Route::get('/form/{token}', 'evaluate')->name('evaluate.emp.public');
  Route::post('/form/store/{token}', 'evaluate_store')->name('evaluate.emp.store');
  Route::post('/revice/{token}', 'revice')->name('evaluate.emp.revice');
});

// recruitment employee requisition
Route::prefix('recruitment')->controller(EmployeeRequisitionController::class)->group(function () {
  Route::get('/', 'emp_index')->name('recruitment.emp.index')->middleware('can:emp.recruitment.read');
  Route::get('/er/my-er/form/{id?}', 'emp_form')->name('recruitment.emp.er.form')->middleware('can:emp.recruitment.read');
  Route::post('/er/my-er/form', 'store')->name('recruitment.emp.er.form.store')->middleware('can:emp.recruitment.read');
  Route::get('/er/my-er/detail/{id?}', 'detail')->name('recruitment.emp.er.detail')->middleware('can:emp.recruitment.read');
  Route::get('/er/approve-er/count', 'countApproveER')->name('recruitment.emp.er.approve-er.count');
  Route::get('/er/done-er', 'getDoneER')->name('recruitment.emp.er.done-er');
  Route::get('/er/my-er/steps/{id?}', 'getERSteps')->name('recruitment.emp.er.my-er.steps');
  Route::delete('/er/my-er/delete', 'destroy')->name('recruitment.emp.er.my-er.destroy');
  Route::get('/print/{er}', 'print')->name('recruitment.emp.er.print');
  Route::get('/er/process-combined', 'getProcessCombinedER')->name('recruitment.emp.er.process-combined');
  Route::get('/er/reason/{id?}', 'getDecisionReason')->name('recruitment.emp.er.reason');
  Route::post('/er/approve/multiple', 'approveMultiple')->name('recruitment.emp.er.approveMultiple');
  Route::get('/er/approve-er/review/{token}', 'review')->name('recruitment.emp.er.approve.form');
  Route::post('/er/approve-er/review/store/{token}', 'review_store')->name('recruitment.emp.er.approve.form.store');
  Route::post('/er/approve-er/reject/{token}', 'reject')->name('recruitment.emp.er.approve.reject');
});
// recruitment selection
Route::prefix('recruitment/selection')->controller(SelectionController::class)->group(function () {
  Route::get('/process', 'getProcessSelection')->name('recruitment.emp.selection.process');
  Route::get('/process/count', 'countProcessSelection')->name('recruitment.emp.selection.process.count');
  Route::get('/done', 'getDoneSelection')->name('recruitment.emp.selection.done');
  Route::get('/review/{token}', 'review')->name('recruitment.emp.selection.review');
  Route::post('/review/store/{token}', 'review_store')->name('recruitment.emp.selection.review.store');
  Route::get('/detail/{token}', 'profile_detail')->name('recruitment.emp.selection.detail');
});
// recruitment result
Route::prefix('recruitment/result')->controller(SelectionController::class)->group(function () {
  Route::get('/', 'getResult')->name('recruitment.emp.result');
  Route::get('/detail/{id?}', 'result_detail')->name('recruitment.emp.result.detail');
});