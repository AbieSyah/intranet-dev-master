<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\GuestController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\HRD\EvaluationController;
use App\Http\Controllers\Recruitment\EmployeeRequisitionController;
use App\Http\Controllers\Recruitment\SelectionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\ServiceDeskController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\ServiceTicketController;
use App\Models\Log;
use App\Http\Controllers\Attendance\AttendancePermitProfileController;
use App\Http\Controllers\Attendance\BusinessTrip\CancellationController;
use App\Http\Controllers\Attendance\BusinessTrip\IndexController;
use App\Http\Controllers\Attendance\BusinessTrip\ProposeController;
use App\Http\Controllers\Attendance\BusinessTrip\ReportController;
use App\Http\Controllers\Attendance\ClaimOvertimeController;
use App\Http\Controllers\Attendance\EmployeeLeaveProfileController;
use App\Http\Controllers\Attendance\lateHistoriesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/maintenance', function () {
//     return view('errors.503');
// })->name('maintenance');

$isMaintenance = false;
$now = Carbon::now();
$eventStart = env('MAINTENANCE_EVENT_START')
    ? Carbon::parse(env('MAINTENANCE_EVENT_START'))
    : null;
$eventEnd = env('MAINTENANCE_EVENT_END')
    ? Carbon::parse(env('MAINTENANCE_EVENT_END'))
    : null;
if ($eventStart && $eventEnd && $now->between($eventStart, $eventEnd)) {
    $allowedIps = explode(',', env('MAINTENANCE_ALLOWED_IPS', '127.0.0.1'));
    if (!in_array(request()->ip(), $allowedIps)) {
        $isMaintenance = true;
    }
}
if ($isMaintenance) {
    Route::get('/', function () {
        return view('errors.503');
    });
    Route::any('/{any}', function (Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'logout',
                'description' => !empty($user->employee->fullname)
                    ? 'user "'.$user->employee->fullname.'" logout maintenance'
                    : 'user "'.$user->name.'" logout maintenance',
            ]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return redirect('/');
    })->where('any', '.*');
    return;
}

/*
|--------------------------------------------------------------------------
| Route Normal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');

Auth::routes([
    'register' => false,
    'reset' => true,
    'verify' => false,
]);

//reset password via email
Route::post('/passwordemail', [PasswordController::class, 'reset_password_email'])->name('reset.password.email');

Route::group(['middleware' => 'auth'], function () {
    //home
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/{id}/pdf', [HomeController::class, 'home_lampiran'])->name('home.lampiran');
    Route::get('/home/search', [HomeController::class, 'search'])->name('search');
    Route::get('/home/search/news', [HomeController::class, 'emp_search'])->name('emp.search');
    Route::get('/home/{id}/detail', [HomeController::class, 'detail'])->name('home.detail');
    Route::get('/home/{id}/emp/detail', [HomeController::class, 'emp_detail'])->name('emp.detail');
    Route::get('/profile', [HomeController::class, 'index_profile'])->name('emp.profile');
    Route::get('/profile/getEvalHistory', [EvaluationController::class, 'getEvalHistory'])->name('emp.profile.getEvalHistory');
    Route::post('/upload', [HomeController::class, 'profile_upload'])->name('profile.upload');
    Route::get('/comingsoon', [HomeController::class, 'comingsoon'])->name('comingsoon');
    Route::get('/test2', [TestController::class, 'index'])->name('test');
    //profile home
    Route::get('/myhome', [HomeController::class, 'profile_home'])->name('profile.home');
    //profile home search
    Route::get('/myhome/search', [HomeController::class, 'profile_search'])->name('profile.search');
    //profile home detail
    Route::get('/myhome/{id}/detail', [HomeController::class, 'profile_home_detail'])->name('profile.home.detail');
    //myprofile
    Route::get('/myprofile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/myprofile/getEvalHistory', [EvaluationController::class, 'getEvalHistory'])->name('profile.getEvalHistory');
    //myrule
    Route::get('/myrule', [HomeController::class, 'profile_internal_rule'])->name('profile.internal.rule');
    //mybenefit
    Route::get('/mybenefit', [HomeController::class, 'profile_benefit'])->name('profile.benefit');
    //mycalendar
    Route::get('/mycalendar', [HomeController::class, 'profile_calendar'])->name('profile.calendar');
    // Route::get('/{id}/detail/calendar', [HomeController::class, 'profile_calendar_detail'])->name('profile.calendar.detail');
    Route::get('/{id}/download/calendar', [HomeController::class, 'profile_calendar_download'])->name('profile.calendar.download');
    Route::post('/view/calendar', [HomeController::class, 'profile_calendar_view'])->name('profile.calendar.view');
    //mymedical
    Route::get('/mymedical', [HomeController::class, 'profile_medical'])->name('profile.medical');
    //lampiran mcu
    Route::post('/profile-lampiran-pdf', [HomeController::class, 'profile_lampiran_pdf'])->name('profile.lampiran.pdf');
    Route::get('/{id}/profile-lampiran-mcu', [HomeController::class, 'profile_lampiran_mcu'])->name('profile.lampiran.mcu');
    Route::get('/{id}/profile-download-mcu', [HomeController::class, 'profile_download_mcu'])->name('profile.download.mcu');
    //pkb
    Route::get('/mypkb', [HomeController::class, 'profile_pkb'])->name('profile.pkb');
    Route::get('/download/pkb', [HomeController::class, 'profile_pkb_download'])->name('profile.pkb.download');
    //booking room
    Route::get('/booking-room', [HomeController::class, 'profile_booking'])->name('profile.booking');
    Route::post('/bookingroom', [HomeController::class, 'profile_booking_store'])->name('profile.booking.store');
    Route::post('/api-view', [HomeController::class, 'profile_booking_view'])->name('profile.booking.view');
    Route::post('/bookingroom/update', [HomeController::class, 'profile_booking_update'])->name('profile.booking.update');
    Route::post('/bookingroom/delete', [HomeController::class, 'profile_booking_delete'])->name('profile.booking.delete');
    Route::post('/bookingroom/update/series', [HomeController::class, 'profile_booking_update_series'])->name('profile.booking.update.series');
    Route::post('/bookingroom/delete/series', [HomeController::class, 'profile_booking_delete_series'])->name('profile.booking.delete.series');
    //training
    Route::get('/mytraining', [HomeController::class, 'profile_training'])->name('profile.training'); //use
    Route::get('/mytraining/{id}/status', [HomeController::class, 'profile_training_status'])->name('profile.training.status');
    Route::get('/mytraining/{id}/detail', [HomeController::class, 'profile_training_detail'])->name('profile.training.detail');
    Route::get('/{id}/sertifikat', [HomeController::class, 'profile_training_sertifikat'])->name('profile.training.sertifikat');
    Route::get('/{id}/materi', [HomeController::class, 'profile_training_materi'])->name('profile.training.materi');
    Route::put('/jadwal/store', [HomeController::class, 'profile_training_jadwal_store'])->name('profile.training.jadwal.store');
    //laporan training
    Route::get('/mytraining/laporan', [HomeController::class, 'profile_training_laporan'])->name('profile.training.laporan');
    Route::post('/mytraining/status-laporan', [HomeController::class, 'profile_training_status_laporan'])->name('profile.training.status.laporan');
    Route::get('/mytraining/{id}/create-laporan', [HomeController::class, 'profile_training_create_laporan'])->name('profile.training.create.laporan');
    Route::put('/mytraining/laporan/store', [HomeController::class, 'profile_training_laporan_store'])->name('profile.training.laporan.store');
    Route::get('/mytraining/{id}/evaluasi-laporan', [HomeController::class, 'profile_training_evaluasi_laporan'])->name('profile.training.evaluasi.laporan');
    Route::put('/mytraining/evaluasi-laporan/store', [HomeController::class, 'profile_training_evaluasi_laporan_store'])->name('profile.training.evaluasi.laporan.store');
    Route::post('/mytraining/api/evaluasi-laporan/check', [HomeController::class, 'profile_training_evaluasi_laporan_check'])->name('profile.training.evaluasi.laporan.check');
    Route::get('/mytraining/approval/{id}/form', [HomeController::class, 'profile_training_approval_laporan'])->name('profile.training.approval.laporan');
    Route::put('/mytraining/laporan/approval/store', [HomeController::class, 'profile_training_laporan_approval_store'])->name('profile.training.laporan.approval.store');
    Route::get('/mytraining/{id}/laporan/pdf', [HomeController::class, 'profile_training_laporan_pdf'])->name('profile.training.laporan.pdf');
    Route::get('/back-approval-laporan', [HomeController::class, 'profile_back_approval_laporan'])->name('profile.back.approval.laporan');
    //fkt ptt
    Route::get('/mytraining/fkt/ptt', [HomeController::class, 'profile_training_fkt_ptt'])->name('profile.training.fkt.ptt'); //use
    Route::get('/mytraining/fkt/ptt/approved', [HomeController::class, 'profile_training_fkt_ptt_approved'])->name('profile.training.fkt.ptt.approved'); //use
    Route::get('/mytraining/fkt/ptt/approved/{id}/form', [HomeController::class, 'profile_training_fkt_ptt_approved_form'])->name('profile.training.fkt.ptt.approved.form'); //use
    Route::put('/mytraining/fkt/ptt/approved/store', [HomeController::class, 'profile_training_fkt_ptt_approved_store'])->name('profile.training.fkt.ptt.approved.store'); //use
    Route::put('/mytraining/fkt/ptt/revised/store', [HomeController::class, 'profile_training_fkt_ptt_revised_store'])->name('profile.training.fkt.ptt.revised.store'); //use
    Route::put('/mytraining/fkt/ptt/rejected/store', [HomeController::class, 'profile_training_fkt_ptt_rejected_store'])->name('profile.training.fkt.ptt.rejected.store'); //use
    Route::get('/mytraining/fkt/ptt/form', [HomeController::class, 'profile_training_fkt_form_ptt'])->name('profile.training.fkt.form.ptt');
    Route::get('/mytrainingfkt/ptt/{id}/form', [HomeController::class, 'profile_training_fkt_edit_ptt'])->name('profile.training.fkt.edit.ptt');
    Route::get('/mytrainingfkt/ptt/{id}/detail', [HomeController::class, 'profile_training_fkt_ptt_detail'])->name('profile.training.fkt.ptt.detail'); //use
    Route::put('/mytraining/fkt/ptt/form', [HomeController::class, 'profile_training_fkt_ptt_store'])->name('profile.training.fkt.ptt.store'); //use
    Route::put('/mytraining/fkt/ptt/update', [HomeController::class, 'profile_training_fkt_ptt_update'])->name('profile.training.fkt.ptt.update'); //use
    Route::post('/mytraining/fkt/ptt/status', [HomeController::class, 'profile_status_fkt_ptt'])->name('profile.status.fkt.ptt'); //use
    Route::get('/back-pengajuan-ptt', [HomeController::class, 'profile_back_fkt_ptt'])->name('profile.back.fkt.ptt');
    Route::get('/back-approve-ptt', [HomeController::class, 'profile_back_approve_fkt_ptt'])->name('profile.back.approve.fkt.ptt');
    //fpkt ptt
    Route::get('/mytrainingfpkt/ptt/{id}/form', [HomeController::class, 'profile_training_fpkt_ptt'])->name('profile.training.fpkt.ptt');
    Route::put('/mytraining/fpkt/ptt/form', [HomeController::class, 'profile_training_fpkt_ptt_store'])->name('profile.training.fpkt.ptt.store'); //use
    //fpkt
    Route::get('/mytrainingfpkt/{id}/form', [HomeController::class, 'profile_training_fpkt'])->name('profile.training.fpkt');
    Route::put('/mytraining/fpkt/form', [HomeController::class, 'profile_training_fpkt_store'])->name('profile.training.fpkt.store');
    //collective ptt
    Route::get('/mytrainingcollective/ptt/{id}/form', [HomeController::class, 'profile_training_collective_ptt'])->name('profile.training.collective.ptt'); 
    //approval atasan penilai
    Route::get('/mytrainingcollective/ptt/approve/{id}/form', [HomeController::class, 'profile_training_collective_approve_ptt'])->name('profile.training.collective.approve.ptt');
    //pdf fkt and fpkt ptt
    Route::get('/mytraining/{id}/fkt/ptt/pdf', [HomeController::class, 'profile_training_fkt_ptt_pdf'])->name('profile.training.fkt.ptt.pdf');
    Route::get('/mytraining/{id}/fpkt/ptt/pdf', [HomeController::class, 'profile_training_fpkt_ptt_pdf'])->name('profile.training.fpkt.ptt.pdf');
    Route::get('/mytraining/{id}/fpkt/ptt/print', [HomeController::class, 'profile_training_fpkt_ptt_print'])->name('profile.training.fpkt.ptt.print');
    
    //pti use
    Route::get('/mytraining/fkt/pti', [HomeController::class, 'profile_training_fkt_pti'])->name('profile.training.fkt.pti'); //use
    Route::get('/mytraining/fkt/pti/approved', [HomeController::class, 'profile_training_fkt_pti_approved'])->name('profile.training.fkt.pti.approved');//use
    Route::post('/mytraining/fkt/pti/api-select-usulan-program', [HomeController::class, 'profile_training_fkt_pti_select_usulan'])->name('profile.training.fkt.pti.select.usulan');
    Route::post('/mytraining/fkt/pti/api-select-pelatihan', [HomeController::class, 'profile_training_fkt_pti_select_pelatihan'])->name('profile.training.fkt.pti.select.pelatihan');
    Route::post('/mytraining/fkt/pti/api-create', [HomeController::class, 'profile_training_fkt_pti_select_create'])->name('profile.training.fkt.pti.select.create');
    Route::put('/mytraining/fkt/pti/store', [HomeController::class, 'profile_training_fkt_pti_store'])->name('profile.training.fkt.pti.store');
    Route::post('/mytraining/fkt/pti/status', [HomeController::class, 'profile_status_fkt_pti'])->name('profile.status.fkt.pti');
    Route::get('/mytrainingfkt/pti/{id}/detail', [HomeController::class, 'profile_training_fkt_pti_detail'])->name('profile.training.fkt.pti.detail');
    Route::get('/mytraining/fpkt/pti/{id}/form', [HomeController::class, 'profile_training_fpkt_pti'])->name('profile.training.fpkt.pti');
    Route::put('/mytraining/fpkt/pti/store', [HomeController::class, 'profile_training_fpkt_pti_store'])->name('profile.training.fpkt.pti.store');
    Route::get('/back-pengajuan-pti', [HomeController::class, 'profile_back_fkt_pti'])->name('profile.back.fkt.pti');
    //pti approve use
    Route::put('/mytraining/fkt/pti/approved/store', [HomeController::class, 'profile_training_fkt_pti_approved_store'])->name('profile.training.fkt.pti.approved.store');
    Route::get('/back-pengajuan-approve-pti', [HomeController::class, 'profile_back_fkt_approve_pti'])->name('profile.back.fkt.approve.pti');
    //pti reject use
    Route::post('/mytraining/fkt/pti/reject/store', [HomeController::class, 'profile_training_fkt_pti_reject_store'])->name('profile.training.fkt.pti.reject.store');
    
    Route::get('/mytraining/fkt/pti/approved/{id}/form', [HomeController::class, 'profile_training_fkt_pti_approved_form'])->name('profile.training.fkt.pti.approved.form');
    Route::get('/mytrainingfkt/pti/{id}/form', [HomeController::class, 'profile_training_fkt_edit_pti'])->name('profile.training.fkt.edit.pti');
    Route::get('/mytraining/fkt/pti/form', [HomeController::class, 'profile_training_fkt_form_pti'])->name('profile.training.fkt.form.pti');
    // Route::put('/mytraining/fkt/pti/form', [HomeController::class, 'profile_training_fkt_pti_store'])->name('profile.training.fkt.pti.store');
    Route::put('/mytraining/fkt/pti/update', [HomeController::class, 'profile_training_fkt_pti_update'])->name('profile.training.fkt.pti.update');
    //fpkt pti
    //collective pti
    Route::get('/mytrainingcollective/pti/{id}/form', [HomeController::class, 'profile_training_collective_pti'])->name('profile.training.collective.pti');
    Route::get('/mytrainingcollective/pti/approve/{id}/form', [HomeController::class, 'profile_training_collective_approve_pti'])->name('profile.training.collective.approve.pti');
    //pdf fkt and fpkt ptt
    Route::get('/mytraining/{id}/fkt/pti/pdf', [HomeController::class, 'profile_training_fkt_pti_pdf'])->name('profile.training.fkt.pti.pdf');
    Route::get('/mytraining/{id}/fpkt/pti/pdf', [HomeController::class, 'profile_training_fpkt_pti_pdf'])->name('profile.training.fpkt.pti.pdf');
    Route::get('/mytraining/{id}/fpkt/pti/print', [HomeController::class, 'profile_training_fpkt_pti_print'])->name('profile.training.fpkt.pti.print');

    //collective training
    Route::get('/mytrainingcollective/{id}/form', [HomeController::class, 'profile_training_collective'])->name('profile.training.collective');
    //back training
    Route::get('/back-verification', [HomeController::class, 'back_profile_training'])->name('back.profile.training');
    Route::get('/back-pengajuan', [HomeController::class, 'back_profile_fkt'])->name('back.profile.fkt');

    //fkt and fpkt verified
    Route::get('/mytraining/verified', [HomeController::class, 'profile_training_verified'])->name('profile.training.verified');
    Route::get('/mytraining/{id}/verified/detail', [HomeController::class, 'profile_training_verified_detail'])->name('profile.training.verified.detail');
    Route::get('/mytraining/{id}/fkt/pdf', [HomeController::class, 'profile_training_fkt_pdf'])->name('profile.training.fkt.pdf');
    Route::get('/mytraining/{id}/fpkt/pdf', [HomeController::class, 'profile_training_fpkt_pdf'])->name('profile.training.fpkt.pdf');
    Route::put('/mytraining/verified/store', [HomeController::class, 'profile_training_verified_store'])->name('profile.training.verified.store');

    //qrcode training
    Route::get('/mytraining/{code}/{id}/pemohon', [HomeController::class, 'qr_code_pemohon'])->name('profile.training.qrcode.pemohon');
    Route::get('/mytraining/{code}/{id}/checker', [HomeController::class, 'qr_code_checker'])->name('profile.training.qrcode.checker');
    Route::get('/mytraining/{code}/{id}/verified', [HomeController::class, 'qr_code_verified'])->name('profile.training.qrcode.verified');
    Route::get('/mytraining/{code}/{id}/approval', [HomeController::class, 'qr_code_approval'])->name('profile.training.qrcode.approval');
    Route::get('/mytraining/{code}/{id}/fpkt', [HomeController::class, 'qr_code_fpkt'])->name('profile.training.qrcode.fpkt');

    //service desk
    Route::get('/myservice-desk', [ServiceDeskController::class, 'index'])->name('myservice-desk.index')->middleware('can:emp.service-desk.read');
    Route::get('/myservice-desk/create', [ServiceDeskController::class, 'create'])->name('myservice-desk.create')->middleware('can:emp.service-desk.create');
    Route::get('/myservice-desk/{id}/{role}', [ServiceManagementController::class, 'workspace'])->name('myservice-desk.workspace')->middleware('can:emp.service-desk.read');
    Route::get('/myknowledge-base/{id}', [KnowledgeBaseController::class, 'show'])->name('myknowledge-base.show');
    Route::get('/myservice-tickets/data', [ServiceTicketController::class, 'getData'])->name('myservice-ticket.data')->middleware('can:emp.service-desk.read');
    
    //evaluations
    Route::prefix('myevaluation')->controller(EvaluationController::class)->group(function () {
        Route::get('/', 'profile_index')->name('profile.evaluation');
        Route::post('/approve/multiple', 'approveMultiple')->name('profile.evaluation.approveMultiple');
        Route::get('/approve/multiple/print/{token}', 'approveMultiple_print')->name('profile.evaluation.approveMultiple.print');
        Route::post('/approve/multiple/print/token', 'approveMultiple_token')->name('profile.evaluation.approveMultiple.print.token');
        Route::get('/process', 'profile_getProcess')->name('profile.evaluation.process');
        Route::get('/countprocess', 'profile_countProcess')->name('profile.evaluation.countprocess');
        Route::get('/done', 'profile_getDone')->name('profile.evaluation.done');
        Route::get('/detail/{id?}', 'profile_detail')->name('profile.evaluation.detail');
        Route::get('/print/{evaluation}', 'print')->name('profile.evaluation.print');
        Route::get('/steps/{id?}', 'getEvaluationSteps')->name('profile.evaluation.steps');

        Route::get('/form/{token}', 'evaluate')->name('profile.evaluate.public');
        Route::post('/form/store/{token}', 'evaluate_store')->name('profile.evaluate.store');
        Route::post('/revice/{token}', 'revice')->name('profile.evaluate.revice');
    });
    // recruitment employee requisition
    Route::prefix('myrecruitment')->controller(EmployeeRequisitionController::class)->group(function () {
        Route::get('/', 'profile_index')->name('recruitment.profile.index');
        Route::get('/er/form/{id?}', 'profile_form')->name('recruitment.profile.er.form');
        Route::post('/er/form', 'store')->name('recruitment.profile.er.form.store');
        Route::get('/er/my-er/detail/{id?}', 'detail')->name('recruitment.profile.er.detail');
        Route::get('/er/approve-er/count', 'countApproveER')->name('recruitment.profile.er.approve-er.count');
        Route::get('/er/done-er', 'getDoneER')->name('recruitment.profile.er.done-er');
        Route::get('/er/my-er/steps/{id?}', 'getERSteps')->name('recruitment.profile.er.my-er.steps');
        Route::delete('/er/my-er/delete', 'destroy')->name('recruitment.profile.er.my-er.destroy');
        Route::get('/print/{er}', 'print')->name('recruitment.profile.er.print');
        Route::get('/er/process-combined', 'getProcessCombinedER')->name('recruitment.profile.er.process-combined');
        Route::get('/er/reason/{id?}', 'getDecisionReason')->name('recruitment.profile.er.reason');
        Route::post('/er/approve/multiple', 'approveMultiple')->name('recruitment.profile.er.approveMultiple');
        Route::get('/er/approve-er/review/{token}', 'review')->name('recruitment.profile.er.approve.form');
        Route::post('/er/approve-er/review/store/{token}', 'review_store')->name('recruitment.profile.er.approve.form.store');
        Route::post('/er/approve-er/reject/{token}', 'reject')->name('recruitment.profile.er.approve.reject');
    });
    // recruitment selection
    Route::prefix('myrecruitment/selection')->controller(SelectionController::class)->group(function () {
        Route::get('/process', 'getProcessSelection')->name('recruitment.profile.selection.process');
        Route::get('/process/count', 'countProcessSelection')->name('recruitment.profile.selection.process.count');
        Route::get('/done', 'getDoneSelection')->name('recruitment.profile.selection.done');
        Route::get('/review/{token}', 'review')->name('recruitment.profile.selection.review');
        Route::post('/review/store/{token}', 'review_store')->name('recruitment.profile.selection.review.store');
        Route::get('/detail/{token}', 'profile_detail')->name('recruitment.profile.selection.detail');
    });
    // recruitment result
    Route::prefix('myrecruitment/result')->controller(SelectionController::class)->group(function () {
        Route::get('/', 'getResult')->name('recruitment.profile.result');
        Route::get('/detail/{id?}', 'result_detail')->name('recruitment.profile.result.detail');
    });

    //select doctor
    Route::post('/select-doctor', [HomeController::class, 'select_doctor'])->name('select.doctor');
    //privacy policy
    Route::post('/privacy-policy', [HomeController::class, 'privacy_policy'])->name('privacy.policy');
    Route::get('/privacypolicy', [HomeController::class, 'disclaimer'])->name('disclaimer');
    //reset password on login
    Route::prefix('resetpassword')->controller(PasswordController::class)->group(function () {
        Route::get('/', 'index')->name('user.password.index');
        Route::patch('/update', 'update')->name('user.password.update');
    });
    //update password years
    Route::prefix('updatepassword')->controller(PasswordController::class)->group(function () {
        Route::get('/', 'index_password')->name('password.index');
        Route::patch('/update', 'update_password_year')->name('password.update.year');
    });

    Route::group(['prefix' => 'about', 'middleware'=>'can:about.read'], function () {
        Route::get('/', [AboutController::class, 'index'])->name('about.index');
        Route::post('/', [AboutController::class, 'store']);
        Route::delete('/{id}', [AboutController::class, 'destroy'])->name('about.destroy');
    });

    Route::prefix('service-desk')->name('service-desk.')->group(function() {
        Route::get('/{id}/{role}', [ServiceManagementController::class, 'workspace'])->name('workspace');
        Route::post('/delete-message-media/{id}', [ServiceManagementController::class, 'deleteMessageMedia'])->name('delete-message-media');
        Route::post('{id}/{role}', [ServiceManagementController::class, 'saveMessage'])->name('send-message');
        Route::resource('/', ServiceDeskController::class)->parameters([
            '' => 'id'
        ]);
    });
    Route::controller(AttendancePermitProfileController::class)->prefix('attendance-permit')->group(function(){
        Route::get('/index','profileIndex')->name('attendance-permit.profile-index');
        Route::get('/pending-count','pendingCount')->name('attendance-permit.pending-count');
        Route::get('/approval/{token}', 'profileIndex')->name('attendance.approval');
        Route::post('/approve', 'approve')->name('attendance-permit.profile-approve');
        Route::post('/reject', 'reject')->name('attendance-permit.profile-reject');
        Route::get('/data-my','dataMy')->name('attendance-permit.data.my');
        Route::get('/data-approval','dataApproval')->name('attendance-permit.data-approval');
        Route::get('/data-approval-history','dataApprovalHistory')->name('attendance-permit.data-approval-history');
        Route::get('/create','profileCreate')->name('attendance-permit.profile-create');
        Route::post('/store','profileStore')->name('attendance-permit.profile-store');
    });
    Route::controller(lateHistoriesController::class)->prefix('attendance-late')->group(function(){
        Route::get('/index','profileIndex')->name('attendance-late.profile-index');
        Route::get('/pending-count','pendingCount')->name('attendance-late.pending-count');
        // Route::get('/profile', 'profileIndex')->name('attendance-late.knowledge');
        Route::get('/data-my','myData')->name('attendance-late.data-my');
        Route::get('/data-head-knowledge','dataHeadKnowledge')->name('attendance-late.data-head-knowledge');
        Route::get('/data-head-History','dataHeadHistory')->name('attendance-late.data-head-history');
        Route::post('/knowledge/{id}', 'knowledge')->name('attendance-late.knowledge');
    });

    Route::controller(EmployeeLeaveProfileController::class)->prefix('employee-leave.profile')->group(function(){
        Route::get('/','index')->name('leave-request.profile-index');
        Route::get('/data-my','myData')->name('leave-request.data.my');
        Route::get('/data-approval','dataApproval')->name('leave-request.data-approval');
        Route::get('/data-approval-history','dataApprovalHistory')->name('leave-request.data-history');
        Route::get('/create','create')->name('leave-request.profile-create');

        Route::get('/attendance/approval/{token}', 'index')->name('leave-request.approval');
        Route::post('/store','store')->name('leave-request.profile-store');
        //  SINGLE
        Route::post('/single-process-approval', 'singleProcessApproval')->name('leave-request.single-process-approval');
        //  BULK
        Route::post('/bulk-process-approval', 'bulkProcessApproval')->name('leave-request.bulk-process-approval');
        Route::get('/leave-request-edit','leaveRequestEdit')->name('employee-leave.leave-request-edit');
        Route::put('/leave-request-update','leaveRequestUpdate')->name('employee-leave.leave-request-update');
        Route::delete('/leave-request-destroy','leaveRequestDestroy')->name('employee-leave.leave-request-destroy');
        Route::get('/pending-count','pendingCount')->name('leave-request.pending-count');
        Route::get('/calculate-days','calculateLeaveDays')->name('leave-request.profile-calculate-days');
        Route::get('/calculate-normatif','calculateNormatif')->name('leave-request.profile-calculate-normatif');
    });

    Route::controller(ClaimOvertimeController::class)->prefix('claim-overtime')->group(function(){
        Route::get('/index','index')->name('claim-overtime.index');
        Route::get('/approval/{token}', 'index')->name('claim-overtime.approval');
        Route::get('/claim/{token}', 'index')->name('claim-overtime.claim');
        Route::get('/pending-count','pendingCount')->name('claim-overtime.pending-count');
        Route::get('/data-my','dataMy')->name('claim-overtime.data-my');
        Route::get('/data-my-history','dataMyHistory')->name('claim-overtime.data-my-history');
        Route::get('/data-approval','dataApproval')->name('claim-overtime.data-approval');
        Route::get('/data-approval-history','dataApprovalHistory')->name('claim-overtime.data-history-overtime');
        Route::post('/claim','claimOvertime')->name('claim-overtime.claimOvertimeNow');
        Route::get('/pending-count','pendingCount')->name('claim-overtime.pending-count');
        Route::post('/single-process-approval', 'singleProcessApproval')->name('claim-overtime.single-process-approval');
        //  BULK
        Route::post('/bulk-process-approval', 'bulkProcessApproval')->name('claim-overtime.bulk-process-approval');
    });
    Route::controller(IndexController::class)->prefix('business-trip')->group(function(){
        Route::get('/index','index')->name('business-trip.profile-index');
        Route::get('/approval/{token}', 'index')->name('business-trip.approval');
        Route::get('/waiting-pending-count','waitingPendingCount')->name('business-trip.waiting-pending-count');
        // PROPOSE
        Route::get('/my-business-trip-data','myBusinessTripData')->name('business-trip.my-business-trip-data');
        Route::get('/my-business-trip-detail/{id}','myBusinessTripDetail')->name('business-trip.my-business-trip-detail');
        Route::get('/approval-data','approvalData')->name('business-trip.approval-data');
        Route::get('/approval-history','approvalHistory')->name('business-trip.approval-history');
        Route::get('/approval-detail/{id}','approvalDetail')->name('business-trip.approval-detail');
        Route::post('/single-process-approval', 'singleProcessApproval')->name('business-trip.single-process-approval');
        Route::post('/bulk-process-approval', 'bulkProcessApproval')->name('business-trip.bulk-process-approval');
        // REPORT
        Route::get('/my-report-claim-data','myReportClaimData')->name('business-trip.my-report-claim-data');
        Route::get('/my-report-claim-detail/{id}','myReportClaimDetail')->name('business-trip.my-report-claim-detail');
        Route::get('/report-claim-approval','ReportClaimApproval')->name('business-trip.report-claim-data');
        Route::get('/report-claim-detail/{id}','reportClaimDetail')->name('business-trip.report-claim-detail');
        Route::get('/report-claim-history','reportClaimHistory')->name('business-trip.report-claim-history');
        Route::post('/single-process-report', 'singleProcessReport')->name('business-trip.single-process-Report');
        // CANCELLATION
        Route::get('/my-cancellation','myCancellation')->name('business-trip.my-cancellation');
        Route::get('/my-cancellation/{id}','myCancellationDetail')->name('business-trip.my-cancellation-detail');
        Route::get('/cancellation-approval','cancellationApproval')->name('business-trip.cancellation-approval');
        Route::get('/cancellation-detail/{id}','cancellationDetail')->name('business-trip.cancellation-detail');
        Route::get('/cancellation-history','cancellationHistory')->name('business-trip.cancellation-history');
        Route::post('/single-process-cancellation', 'singleProcessCancel')->name('business-trip.single-process-cancel');

        Route::controller(ProposeController::class)->prefix('propose')->group(function(){
            Route::get('/create','create')->name('business-trip.propose-create');
            Route::post('/store','store')->name('business-trip.propose-store');
            Route::get('/get-allowance','getAllowance')->name('business-trip.get-allowance');
            Route::get('/get-approver','getApprover')->name('business-trip.get-approver');
            Route::get('/generate-document-number', 'generateDocumentNumberAjax')->name('business-trip.generate-document-number');
            Route::get('/edit{id}','edit')->name('business-trip.propose-edit');
            Route::put('/update{id}','update')->name('business-trip.propose-update');
        });

        Route::controller(ReportController::class)->prefix('report')->group(function(){
            Route::get('/create','create')->name('business-trip-report.create');
            Route::post('/store','store')->name('business-trip-report.store');
            ROute::get('/document-detail/{id}', 'documentDetail')->name('business-trip-report.document-detail');
            ROute::get('/meal-data/{id}', 'mealData')->name('business-trip-report.meal-data');
            Route::get('/claim-approver','claimApprover')->name('business-trip.claim-approver');
            Route::get('/edit{id}','edit')->name('business-trip-report.edit');
            Route::put('/update{id}','update')->name('business-trip.report.update');
        });

        Route::controller(CancellationController::class)->prefix('cancellation')->group(function(){
            Route::get('/create/{id}','create')->name('business-trip-cancellation.create');
            Route::post('/store','store')->name('business-trip-cancellation.store');
            Route::get('/expense','businessTripExpense')->name('business-trip-cancellation.expense');
        });
    });

    Route::prefix('e-sign')
        ->controller(\App\Http\Controllers\ESign\ESignController::class)
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('e-sign.dashboard');
            Route::get('/daftar-surat', 'daftarSurat')->name('e-sign.daftar-surat');
            Route::get('/jenis-surat', 'jenisSurat')->name('e-sign.jenis-surat');
            Route::get('/template/{jenis}', 'template')->name('e-sign.template');
        });

    //administrator
    Route::prefix('/administrator')
        ->middleware('can:administrator.menu')
        ->group(function () {
            require __DIR__ . '/web/administrator.php';
        });

    //hrd
    Route::prefix('/hrd')
        ->middleware('can:hrd.menu')
        ->group(function () {
            require __DIR__ . '/web/hrd.php';
            require __DIR__ . '/web/attendance.php';
        });

    //employee
    Route::prefix('/employee')
        ->middleware('can:emp.menu')
        ->group(function () {
            require __DIR__ . '/web/emp.php';
        });

    //security
    Route::prefix('/security')
        ->group(function () {
            require __DIR__ . '/web/security.php';
        });

    Route::patch('/security/set-waktu-keluar/{id?}', [GuestController::class, 'set_waktu_keluar'])->name('guest.set-waktu-keluar');

    Route::prefix('/service-tickets')->name('service-ticket.')->group(function() {
        Route::get('/data', [ServiceTicketController::class, 'getData'])->name('data')->middleware('can:emp.service-desk.read');
        Route::post('/uploads', [ServiceTicketController::class, 'uploads'])->name('uploads')->middleware('can:emp.service-desk.create');
        Route::post('/delete-upload', [ServiceTicketController::class, 'deleteUpload'])->name('delete-upload')->middleware('can:emp.service-desk.delete');
        Route::post('/tickets/{ticket}/change-status', [ServiceTicketController::class, 'changeStatus'])->name('change-status')->middleware('can:itsm.service-desk.update');
        Route::post('/{id}/verify', [ServiceTicketController::class, 'verify'])->name('verify')->middleware('can:itsm.service-desk.analyze');
        Route::post('/{it_initiative?}', [ServiceTicketController::class, 'store'])->name('store');
        Route::put('{id}/close', [ServiceTicketController::class, 'close'])->name('close')->middleware('can:itsm.service-desk.update');
        Route::put('/{id}/reopen', [ServiceTicketController::class, 'reopenTicket'])->name('reopen')->middleware('can:itsm.service-desk.update');
        Route::post('/{id}/{role}/cancel', [ServiceTicketController::class, 'cancel'])->name('cancel')->middleware('can:emp.service-desk.cancel');
        Route::post('/request-approval/{id}', [ServiceTicketController::class, 'requestApproval'])->name('request-approval')->middleware('can:itsm.service-desk.update');

    });

    Route::prefix('/asset-disposal')->name('asset-disposal.')->group(function() {
        Route::get('/view/{id}/review', [AssetDisposalController::class, 'show'])->name('review');
        Route::post('/feedback/{id}', [AssetDisposalController::class, 'feedback'])->name('feedback');
    });

    Route::get('/{id}', [KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');

    //profile
    // Route::prefix('/profile')
    //     ->group(function () {
    //         require __DIR__ . '/web/profile.php';
    //     });
});

// public
require __DIR__ . '/web/public.php';