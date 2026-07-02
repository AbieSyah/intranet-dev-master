<?php

use App\Http\Controllers\Attendance\EmployeePermitSecurityController;
use App\Http\Controllers\Security\GuestController;
use Illuminate\Support\Facades\Route;

Route::prefix('tamu')->group(function () {
    Route::get('/', [GuestController::class, 'index'])->name('guest.index');
    Route::get('/data', [GuestController::class, 'data'])->name('guest.data');
    Route::get('/detail/{id?}', [GuestController::class, 'detail'])->name('guest.detail');

    Route::post('/store', [GuestController::class, 'store'])->name('guest.store');
    Route::get('/{id}/status', [GuestController::class, 'status'])->name('guest.status');
    
    Route::get('/print/{id?}', [GuestController::class, 'print'])->name('guest.print');

    Route::delete('/{id?}', [GuestController::class, 'delete'])->name('guest.delete');

    Route::get('/security-form/{id?}', [GuestController::class, 'security_form'])->name('guest.security-form');
    Route::post('/security-form/{id?}', [GuestController::class, 'security_form_save'])->name('guest.security-form-store');

});

Route::controller(EmployeePermitSecurityController::class)->prefix('employee-attendance-record')->group(function(){
    Route::get('/index','index')->name('employee-permit.security-index');
    Route::get('/records','attendanceRecords')->name('employee-permit.security-records');
    Route::get('/late','late')->name('employee-permit.security-late');
    Route::get('/late-histories','lateHistories')->name('employee-permit.security-late-histories');
    Route::post('/security-permit-knowledge/{id}','securityPermitKnowledge')->name('employee-permit.security-permit-knowledge');
    Route::post('/security-late-knowledge/{id}','securityLateKnowledge')->name('employee-permit.security-late-knowledge');
});