<?php

use App\Http\Controllers\Attendance\AttendanceCalendarController;
use App\Http\Controllers\Attendance\AttendancePermitController;
use App\Http\Controllers\Attendance\AttendancePermitProfileController;
use App\Http\Controllers\Attendance\BusinessTripAllowanceController;
use App\Http\Controllers\Attendance\EarlyOutorLateController;
use App\Http\Controllers\Attendance\EmployeeAttendanceController;
use App\Http\Controllers\Attendance\EmployeeLeaveController;
use App\Http\Controllers\Attendance\EmployeePermitSecurityController;
use App\Http\Controllers\Attendance\GroupEmployeeWorkHourController;
use App\Http\Controllers\Attendance\LeaveSettingController;
use App\Http\Controllers\Attendance\WorkHourController;
use App\Http\Controllers\Attendance\PositioningController;
use App\Http\Controllers\Attendance\TemporaryOutController;
use App\Http\Controllers\Attendance\VacationSettingController;
use App\Models\Attendance\AttendanceCalendar;
use App\Models\Attendance\EmployeeAttendance;
use Illuminate\Support\Facades\Route;

Route::prefix('attendance/master')->group(function () {
    // WORK HOUR
    Route::controller(WorkHourController::class)->prefix('workhour')->group(function () {
        Route::get('/', 'index')->name('workhour.index')->middleware('can:hrd.workhour.read');
        Route::get('/show', 'show')->name('workhour.show');
        Route::get('/create', 'create')->name('workhour.create');
        Route::post('/store', 'store')->name('workhour.store');
        Route::get('/edit', 'edit')->name('workhour.edit');
        Route::post('/update', 'update')->name('workhour.update');
        Route::delete('/delete', 'destroy')->name('workhour.destroy');
    });
    // POSITIONING
    Route::controller(PositioningController::class)->prefix('positioning')->group(function () {
        Route::get('/', 'index')->name('positioning.index')->middleware('can:hrd.positioning.read');
        Route::get('/create', 'create')->name('positioning.create');
        Route::post('/store', 'store')->name('positioning.store');
        Route::get('/edit', 'edit')->name('positioning.edit');
        Route::post('/update', 'update')->name('positioning.update');
        Route::delete('/delete', 'destroy')->name('positioning.destroy');
    });

    Route::controller(AttendanceCalendarController::class)->prefix('attendance-calendar')->group(function(){
        Route::get('/','index')->name('attendance-calendar.index')->middleware('can:hrd.attendance-calendar.read');
        Route::post('/store','store')->name('attendance-calendar.store');
        Route::put('/update/{id}','update')->name('attendance-calendar.update');
        Route::delete('/destroy/{id}','destroy')->name('attendance-calendar.destroy');
        Route::get('/data','data')->name('attendance-calendar.data');
        Route::get('/sync-national','syncNational')->name('attendance-calendar.syncNational');
    });

    Route::controller(LeaveSettingController::class)->prefix('leave-setting')->group(function(){
        Route::get('/','index')->name('leave-setting.index')->middleware('can:hrd.leave-setting.read');
        Route::post('/store','store')->name('leave-setting.store');
        Route::get('/edit','edit')->name('leave-setting.edit');
        Route::post('/update','update')->name('leave-setting.update');
        Route::delete('/destroy','destroy')->name('leave-setting.destroy');
    });

    Route::controller(BusinessTripAllowanceController::class)->prefix('business-trip-allowance')->group(function(){
        Route::get('/','index')->name('business-trip-allowance.index')->middleware('can:hrd.business-trip-allowance.read');
        Route::post('/store','store')->name('business-trip-allowance.store');
        Route::get('/edit/{id}','edit')->name('business-trip-allowance.edit');
        Route::put('/update/{id}','update')->name('business-trip-allowance.update');
        Route::delete('/destroy/{id}','destroy')->name('business-trip-allowance.destroy');
        Route::post('/check-existing', 'checkExisting')->name('business-trip-allowance.check');
    });

});

Route::prefix('attendance/sub-menu')->group(function (){
    Route::controller(EmployeeAttendanceController::class)->prefix('employee-attendance')->group(function () {
        Route::get('/index', 'index')->name('employee-attendance.index')->middleware('can:hrd.employee-attendance.read');
        // ->middleware('can:hrd.employee-attendance.read');
        Route::get('/late', 'late')->name('employee-attendance.late');
        Route::get('/view','view')->name('employee-attendance.view');
        Route::put('/update/{id}', 'update')->name('employee-attendance.update')->middleware('can:hrd.employee-attendance.update');
        Route::post('/knowledge/{id}', 'knowledge')->name('employee-attendance-late.knowledge');
        // Route::delete('/delete/{id}', 'destroy')->name('employee-attendance.destroy')->middleware('can:hrd.employee-attendance.delete');
    });

    Route::controller(GroupEmployeeWorkHourController::class)->prefix('group-employee-workhour')->group(function(){
        Route::get('/','index')->name('group-employee-workhour.index')->middleware('can:hrd.group-employee-workhour.read');
        Route::get('/create','create')->name('group-employee-workhour.create')->middleware('can:hrd.group-employee-workhour.create');
        Route::post('/store','store')->name('group-employee-workhour.store');
        Route::get('/edit/{id}','edit')->name('group-employee-workhour.edit')->middleware('can:hrd.group-employee-workhour.edit');
        Route::put('/update','update')->name('group-employee-workhour.update');
        Route::delete('/destroy/{id}','destroy')->name('group-employee-workhour.destroy')->middleware('can:hrd.group-employee-workhour.destroy');
        Route::get('/employee-by-group/{id}', 'employeeByGroup')->name('group-employee-workhour.employeeByGroup');
        Route::get('/employee-by-group-transfer-in', 'employeeByGroupForTransferIn')->name('group-employee-workhour.employeeByGroupForTransferIn');
        Route::get('/employee-list', 'employeeList')->name('group-employee-workhour.employeeList');
        Route::get('/find-employee', 'findEmployee')->name('group-employee-workhour.findEmployee');
        Route::get('/get-groups', 'getGroups')->name('group-employee-workhour.getGroups');
        // Route::post('/transfer-to', 'transferTo')->name('group-employee-workhour.transferTo')->middleware('can:hrd.group-employee-workhour.transfer-to');
        Route::post('/transfer-to', 'transferTo')->name('group-employee-workhour.transferTo');
        Route::post('/transfer-in', 'transferIn')->name('group-employee-workhour.transferIn');
        Route::post('/transfer-out', 'transferOut')->name('group-employee-workhour.transferOut');
    });

    Route::controller(EmployeeLeaveController::class)->prefix('employee-leave')->group(function(){
        //LEAVE BALANCE
        Route::get('/leave-balance','leaveBalanceIndex')->name('employee-leave.leave-balance-index');
        Route::get('/leave-balance-create','leaveBalanceCreate')->name('employee-leave.leave-balance-create')->middleware('can:hrd.employee-leave.leave-balance-create');
        Route::post('/leave-balance-store','leaveBalanceStore')->name('employee-leave.leave-balance-store');
        Route::put('/leave-balance-update','leaveBalanceUpdate')->name('employee-leave.leave-balance-update');
        Route::delete('/leave-balance-destroy','leaveBalanceDestroy')->name('employee-leave.leave-balance-destroy')->middleware('can:hrd.employee-leave.leave-balance-destroy');
        Route::post('/leave-balance-get-selected-employees','getSelectedEmployees')->name('employee-leave.get-selected-employees');
        // Route::get('/leave-balance-edit','leaveBalanceEdit')->name('employee-leave.leave-balance-edit')->middleware('can:hrd.employee-leave.leave-balance-edit');

        //LEAVE HISTORY
        Route::get('/leave-history','leaveHRDIndex')->name('employee-leave.leave-hrd-index');
        Route::get('/leave-history-create','leaveHRDCreate')->name('employee-leave.leave-hrd-create');
        Route::post('/leave-history-store','leaveHRDStore')->name('employee-leave.leave-hrd-store');
        Route::delete('/leave-history-destroy','leaveHRDDestroy')->name('employee-leave.leave-hrd-destroy');
        Route::get('/leave-history-calculate-normatif','calculateNormatif')->name('employee-leave.calculate-normatif');
    });

    Route::controller(AttendancePermitController::class)->prefix('employee-permission')->group(function(){
        Route::get('/index','index')->name('attendance-permit.index');
        Route::get('/Overtime','indexOvertime')->name('attendance-permit.index-overtime');
        Route::get('/create','create')->name('attendance-permit.create');
        Route::get('/store','store')->name('attendance-permit.store');
        Route::get('/detail/{id}','detail')->name('attendance-permit.detail');
        Route::post('/hrd_knowledge/{id}','hrdKnowledge')->name('attendance-permit.hrd_knowledge');
        Route::post('/overtime-knowledge/{id}','overtimeKnowledge')->name('attendance-permit.overtime_knowledge');
        Route::get('/overtime-detail/{id}','overtimeDetail')->name('attendance-permit.overtime_detail');

        Route::get('/businessTrip','IndexBusinessTrip')->name('index.business-trip');
        Route::get('/businessTrip/Detail/{id}','businessTripDetail')->name('detail.business-trip');
        Route::get('/businessTripReport','IndexBusinessReport')->name('index.business-trip-report');
        Route::get('/businessTripReport/Detail/{id}','BusinessReportDetail')->name('detail.business-trip-report');
        Route::get('/businessTripCancel','IndexBusinessCancellation')->name('index.business-trip-cancel');
        Route::get('/businessTripCancel/Detail/{id}','BusinessCancellationDetail')->name('detail.business-trip-cancel');
    });
});
