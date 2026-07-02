<?php

use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\Recruitment\EmployeeRequisitionController;
use App\Http\Controllers\HRD\EvaluationController;
use App\Http\Controllers\Recruitment\JobPostingController;
use App\Http\Controllers\Security\GuestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\ServiceChangeController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\ITAssetController;

// Tamu
Route::get('/form-kunjungan-tamu', [GuestController::class, 'guest_form'])->name('guest.form');
Route::post('/form-kunjungan-tamu', [GuestController::class, 'guest_form_save'])->name('guest.form-save');
// Record Pelatihan
Route::get('/{id}/training/qrcode/{ttd1}', [QrcodeController::class, 'training_laporan_qrcode_ttd'])->name('public.laporan-training.qrcode-ttd');
Route::get('/{id}/training/qrcode2/{ttd2}', [QrcodeController::class, 'training_laporan_qrcode_ttd2'])->name('public.laporan-training.qrcode-ttd2');
Route::get('/{id}/training/qrcode3/{ttd3}', [QrcodeController::class, 'training_laporan_qrcode_ttd3'])->name('public.laporan-training.qrcode-ttd3');
Route::get('/{id}/training/qrcode4/{ttd4}', [QrcodeController::class, 'training_laporan_qrcode_ttd4'])->name('public.laporan-training.qrcode-ttd4');
Route::get('/{id}/training/qrcode5/{ttd5}', [QrcodeController::class, 'training_laporan_qrcode_ttd5'])->name('public.laporan-training.qrcode-ttd5');
Route::get('/{id}/training/qrcode6/{ttd6}', [QrcodeController::class, 'training_laporan_qrcode_ttd6'])->name('public.laporan-training.qrcode-ttd6');
Route::get('/{id}/training/qrcode7/{ttd7}', [QrcodeController::class, 'training_laporan_qrcode_ttd7'])->name('public.laporan-training.qrcode-ttd7');
// FKP PDF
Route::get('/{id}/fkp/pdf', [QrcodeController::class, 'training_fkp_pdf'])->name('public.training.fkp.pdf');
//QRCODE FKP
Route::get('/fkp/{code}/{id}/pemohon', [QrcodeController::class, 'qr_code_pemohon_fkp'])->name('public.training.qrcode.fkp.pemohon');
Route::get('/fkp/{code}/{id}/checker', [QrcodeController::class, 'qr_code_checker_fkp'])->name('public.training.qrcode.fkp.checker');
Route::get('/fkp/{code}/{id}/verified', [QrcodeController::class, 'qr_code_verified_fkp'])->name('public.training.qrcode.fkp.verified');
// FPKP PDF
Route::get('/{id}/fpkp/pdf', [QrcodeController::class, 'training_fpkp_pdf'])->name('public.training.fpkp.pdf');
// QRCODE FPKP
Route::get('/fpkp/{code}/{id}/peserta', [QrcodeController::class, 'qr_code_peserta_fpkp'])->name('public.training.qrcode.fpkp.peserta');
Route::get('/fpkp/{code}/{id}/atasan', [QrcodeController::class, 'qr_code_atasan_fpkp'])->name('public.training.qrcode.fpkp.atasan');
Route::get('/fpkp/{code}/{id}/dept-head', [QrcodeController::class, 'qr_code_dept_head_fpkp'])->name('public.training.qrcode.fpkp.dept-head');
Route::get('/fpkp/{code}/{id}/hrd', [QrcodeController::class, 'qr_code_hrd_fpkp'])->name('public.training.qrcode.fpkp.hrd');
Route::get('/fpkp/{code}/{id}/bod1', [QrcodeController::class, 'qr_code_bod1_fpkp'])->name('public.training.qrcode.fpkp.bod1');
Route::get('/fpkp/{code}/{id}/bod2', [QrcodeController::class, 'qr_code_bod2_fpkp'])->name('public.training.qrcode.fpkp.bod2');

// QRCODE Approval Evaluation
Route::get('/pub/evaluation/approval/{token}', [EvaluationController::class, 'qr_code_approval'])->name('evaluation.qrcode.approval');

// QRCODE Approval Employee Requisition
Route::get('/pub/er/approval/{token}', [EmployeeRequisitionController::class, 'qr_code_approval'])->name('employee-requisition.qrcode.approval');

// Job Posting
Route::get('/recruitment/job/{code?}', [JobPostingController::class, 'public'])->name('job-posting.public');
Route::get('/recruitment/job/get/{publish_code}', [JobPostingController::class, 'getPublicDetail']);

// ITSM
Route::get('/disposal/review/{id}', [AssetDisposalController::class, 'buyerReview'])
   ->name('disposal.public-review')
   ->middleware('signed');

// The action when buyer click the "Sign/Confirm" button
Route::post('/disposal/confirm/{id}', [AssetDisposalController::class, 'buyerConfirm'])
   ->name('disposal.public-confirm')
   ->middleware('signed');

// type = submitter/approver/buyer. Approver means id = ApprovalPathId, buyer = AssetDisposalId
Route::get('/verify/{id}/{type}/{pathId?}/{logId?}', [AssetDisposalController::class, 'verifySignature'])
   ->name("asset-disposal.verify-signature")
   ->middleware('signed');

// Route::get('/service-ticket/approve/{id}/{role}/{approverId?}', function($id, $role, $approverId) {
//    dd($id, $role, $approverId);
// })
Route::get('/service-ticket/{id}/{role}/{approverId?}', [ServiceManagementController::class, 'workspace'])
   ->name('service-ticket.approve-workspace')
   ->middleware('signed');
   
Route::post('/service-management/{id}/approve', [ServiceManagementController::class, 'approve'])
   ->name('service-management.approve')
   ->middleware('signed');

Route::get('/service-change/{id}/{approverId}', [ServiceChangeController::class, 'changeManagement'])
   ->name('service-change.public.index')
   ->middleware('signed');

Route::post('/service-change/{id}/approve', [ServiceChangeController::class, 'approve'])
   ->name('service-change.public.approve')
   ->middleware('signed');
   
Route::get('/report/{id}/service-desk/request-approval', [ServiceManagementController::class, 'requestApprovalReport'])
   ->name('service-desk.request-approval.report')
   ->middleware('signed');

Route::get('/asset/{assetCode}/qrcode', [ITAssetController::class, 'assetQRCode'])
   ->name('public.asset.qrcode')
   ->middleware('signed');