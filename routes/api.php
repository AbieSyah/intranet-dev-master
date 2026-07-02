<?php

use App\Http\Controllers\Recruitment\CandidateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeApiController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\RoomApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\MedicalApiController;
use App\Http\Controllers\Api\PkbApiController;
use App\Http\Controllers\Api\ITAssetApiController;
Use App\Http\Controllers\Api\BiometricApiController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\ServiceTicketApiController;
use App\Http\Controllers\Api\KnowledgebaseApiController;
use App\Http\Controllers\Api\LeaveApiController;
use App\Http\Controllers\Api\PermitApiController;
use App\Http\Controllers\Api\OvertimeApiController;
use App\Http\Controllers\Api\LateApiController;
use App\Http\Controllers\Api\BussinesTripApiController;
use App\Http\Controllers\Api\BusinessReportApiController;
use App\Http\Controllers\Api\BusinessCancellationApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/recruitment/job/{code?}', [CandidateController::class, 'publicStore'])->name('job-posting.public.store');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-permissions', function (Request $request) {
        return response()->json([
        'success' => true,
        'data' => $request->user()
            ->getAllPermissions()
            ->pluck('name')
        ]);
    });

    Route::get('/me', function (Request $request) {
        $user = $request->user();

        $user->load([
            'employee.department',
            'employee.area',
            'employee.section',
            'employee.position',
            'employee.level',
            'employee.building',
            'employee.milestones'
        ]);
        $employeeData = $user->employee ? $user->employee->toArray() : null;

        // if ($employeeData && isset($employeeData['appraisals'])) {
        //     unset($employeeData['appraisals']);
        // }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'employee' => $employeeData
            ]
        ]);
    });

    Route::get('/me/avatar', [AuthController::class, 'showAvatar']);
    Route::post('/me/avatar', [AuthController::class, 'updateAvatar']);
    Route::get('/employees', [EmployeeApiController::class, 'index']);

    Route::get('/news', [NewsApiController::class, 'index']);
    Route::get('/news/{id}/thumbnail', [NewsApiController::class, 'showThumbnail']);
    Route::get('/news/{id}/gambar', [NewsApiController::class, 'showGambar']);
    Route::get('/news/{id}/lampiran', [NewsApiController::class, 'showLampiran']);
    // Route::get('/news/{id}/link-video', [NewsApiController::class, 'showVideo']);

    Route::get('/rooms', [RoomApiController::class, 'index']);
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::get('/my-bookings', [BookingApiController::class, 'myBooking']);
    Route::post('/bookings', [BookingApiController::class, 'store']);
    Route::put('/bookings/entry/{id}', [BookingApiController::class, 'update']);
    // Route::put('/bookings/series/{kode}', [BookingApiController::class, 'updateSeries']);
    Route::delete('/bookings/entry/{id}', [BookingApiController::class, 'delete']);
    // Route::delete('/bookings/series/{kode}', [BookingApiController::class, 'deleteSeries']);
    // Route::post('/bookings', [BookingApiController::class, 'store']);

    Route::get('/medical', [MedicalApiController::class, 'index']);
    Route::get('/medical/{id}/file', [MedicalApiController::class, 'showFile']);

    Route::get('/pkb', [PkbApiController::class, 'index']);
    Route::get('/pkb/{id}/file', [PkbApiController::class, 'showFile']);

    Route::get('/my-assets',[ITAssetApiController::class, 'myAssets']);

    Route::get('/tickets', [ServiceTicketApiController::class, 'index']);
    Route::post('/tickets', [ServiceTicketApiController::class, 'store']);
    Route::post('/tickets/{id}/message', [ServiceTicketApiController::class, 'sendMessage']);
    Route::get('/tickets/approval', [ServiceTicketApiController::class, 'getapprovalTickets']);
    Route::post('/tickets/{id}/approve', [ServiceTicketApiController::class, 'approveTicket']);
    Route::get('/tickets/catalogs', [ServiceTicketApiController::class, 'getCatalogs']);

    Route::get('/knowledge-bases', [KnowledgeBaseApiController::class, 'index']);

    Route::post('/biometric/challenge', [BiometricApiController::class, 'challenge']);
    Route::post('/biometric/register', [BiometricApiController::class, 'register']);
    Route::post('/biometric/verify', [BiometricApiController::class, 'verify']);
    Route::delete('/biometric/remove', [BiometricApiController::class, 'destroy']);

    Route::get('/attendance/today', [AttendanceApiController::class, 'today']);
    Route::post('/attendance/challenge', [AttendanceApiController::class, 'challenge']);
    Route::get('/employee-attendance', [AttendanceApiController::class, 'index']);
    Route::get('/my-employee-attendance', [AttendanceApiController::class, 'myAttendance']);
    Route::post('/attendance/checkin', [AttendanceApiController::class, 'checkin']);
    Route::post('/attendance/checkout', [AttendanceApiController::class, 'checkout']);

    // Route::get('/attendance/positioning', [AttendanceApiController::class, 'getPositioning']);
    // Route::get('/attendance/workhour', [AttendanceApiController::class, 'getWorkHour']);

    Route::get('/leave', [LeaveApiController::class, 'index']);
    Route::get('/leave/my-requests', [LeaveApiController::class, 'myRequests']);
    Route::get('/leave/my-approvals', [LeaveApiController::class, 'myApprovals']);
    Route::get('/leave/my-approvals-history', [LeaveApiController::class, 'myApprovalsHistory']);
    Route::get('/leave/my-line-approvals', [LeaveApiController::class, 'myLineApprovals']);
    Route::get('/leave/types', [LeaveApiController::class, 'getLeaveSettings']);
    Route::post('/leave', [LeaveApiController::class, 'store']);
    Route::post('/leave/{id}/approve', [LeaveApiController::class, 'approve']);
    Route::post('/leave/calculate-personal', [LeaveApiController::class, 'calculatePersonalLeave']);
    Route::post('/leave/calculate-normative', [LeaveApiController::class, 'calculateNormativeLeave']);

    Route::get('/my-permits', [PermitApiController::class, 'myPermits']);
    Route::get('/my-permits-history', [PermitApiController::class, 'myPermitsHistory']);
    Route::get('/pending-approvals', [PermitApiController::class, 'getPendingApprovals']);
    Route::get('/history-approvals', [PermitApiController::class, 'getApprovalHistory']);
    Route::post('/permit', [PermitApiController::class, 'storePermit']);
    Route::post('/permit/{id}/approve', [PermitApiController::class, 'approve']);
    Route::post('/permit/{id}/reject', [PermitApiController::class, 'reject']);

    Route::get('/overtime', [OvertimeApiController::class, 'getMyOvertime']);
    Route::get('/overtime/claims', [OvertimeApiController::class, 'getMyClaimOvertime']);
    Route::get('/overtime/approvals', [OvertimeApiController::class, 'getNeedApprovalOvertime']);
    Route::get('/overtime/approvals/history', [OvertimeApiController::class, 'getApprovalHistoryOvertime']);
    Route::get('/overtime/claims/history', [OvertimeApiController::class, 'getMyClaimOvertimeHistory']);
    Route::post('/claim-overtime', [OvertimeApiController::class, 'claimOvertime']);
    Route::post('/claim-overtime/{id}/approve', [OvertimeApiController::class, 'approveClaimOvertime']);
    Route::post('/claim-overtime/{id}/reject', [OvertimeApiController::class, 'rejectClaimOvertime']);

    Route::get('/late/my-late', [LateApiController::class, 'myLate']);
    Route::get('/late/my-late-history', [LateApiController::class, 'myLateHistory']);
    Route::get('/late/my-approvals', [LateApiController::class, 'myApprovals']);
    Route::get('/late/my-approvals-history', [LateApiController::class, 'myApprovalHistory']);
    Route::post('/late/{id}/approve', [LateApiController::class, 'knowledgeHead']);

    Route::get('/business-trip/pending', [BussinesTripApiController::class, 'getPendingBusinessTrips']);
    // Route::get('/business-trip/allowances', [BussinesTripApiController::class, 'getMyBusinessTripAllowances']);
    Route::get('/business-trip/approvals', [BussinesTripApiController::class, 'getBusinessTripsNeedMyApproval']);
    Route::get('/business-trip/approved', [BussinesTripApiController::class, 'getApprovedBusinessTrips']);
    Route::get('/business-trip/approval-history', [BussinesTripApiController::class, 'getApprovalHistory']);
    Route::get('/business-trip/allowance-detail', [BussinesTripApiController::class, 'getAllowanceDetail']);
    Route::get('/business-trip/approver-list', [BussinesTripApiController::class, 'getApproverList']);
    Route::get('/business-trip/generate-document-number', [BussinesTripApiController::class, 'generateDocNumber']);
    Route::post('/business-trip/store', [BussinesTripApiController::class, 'store']);
    Route::post('/business-trip/process-approval', [BussinesTripApiController::class, 'handleApproval']);
    Route::put('/business-trip/update/{id}', [BussinesTripApiController::class, 'update']);

    Route::get('/business-trip/reportable-trips', [BusinessReportApiController::class, 'getReportableTrips']);
    Route::get('/business-trip/claim-approvers', [BusinessReportApiController::class, 'getClaimApprovers']);
    Route::get('/business-trip/my-reports', [BusinessReportApiController::class, 'getMyReports']);
    Route::get('/business-trip/reports-need-my-approval', [BusinessReportApiController::class, 'getReportsNeedMyApproval']);
    Route::get('/business-trip/report-approval-history', [BusinessReportApiController::class, 'getApprovalHistory']);
    Route::get('/business-trip/meal-data/{tripId}', [BusinessReportApiController::class, 'getMealData']);
    Route::get('/business-trip/report/{id}', [BusinessReportApiController::class, 'show']);
    Route::post('/business-trip/submit-report', [BusinessReportApiController::class, 'submitReport']);
    Route::post('/business-trip/update-report/{id}', [BusinessReportApiController::class, 'updateReport']);
    Route::post('/business-trip/report-approval', [BusinessReportApiController::class, 'handleApproval']);

    Route::get('/business-trip/cancellable-trips', [BusinessCancellationApiController::class, 'getCancellableTrips']);
    Route::get('/business-trip/my-cancellations', [BusinessCancellationApiController::class, 'getMyCancellations']);
    Route::get('/business-trip/cancellations-need-my-approval', [BusinessCancellationApiController::class, 'getCancellationsNeedMyApproval']);
    Route::get('/business-trip/cancellation-approval-history', [BusinessCancellationApiController::class, 'getCancellationApprovalHistory']);
    Route::post('/business-trip/submit-cancellation', [BusinessCancellationApiController::class, 'submitCancellation']);
    Route::post('/business-trip/cancellation-approval', [BusinessCancellationApiController::class, 'handleApproval']);
});

Route::post('/biometric/login-challenge', [BiometricApiController::class, 'loginChallenge']);
Route::post('/biometric/login-verify', [BiometricApiController::class, 'loginVerify']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
// Di dalam routes/api.php
Route::post('/force-logout/request', [AuthController::class, 'requestForceLogout']);
Route::post('/force-logout/verify', [AuthController::class, 'verifyForceLogout']);

Route::post('/login-direct', [AuthController::class, 'loginDirect']);
Route::middleware('auth:sanctum')->post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/forgot-password', [AuthController::class, 'requestForgotPassword']);
Route::post('/verify-forgot-otp', [AuthController::class, 'verifyForgotOtp']);
Route::post('/new-password', [AuthController::class, 'resetNewPassword']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);



