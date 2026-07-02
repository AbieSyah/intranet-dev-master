<?php

use App\Http\Controllers\Administrator\PermissionController;
use App\Http\Controllers\Administrator\RoleController;
use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\ITAssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PriorityMetricController;
use App\Http\Controllers\RiskRegisterController;
use App\Http\Controllers\ServiceCatalogController;
use App\Http\Controllers\ServiceChangeController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\ItsmPriorityController;
use App\Http\Controllers\KnowledgeBaseController;
// use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

// user management
Route::prefix('user')->controller(UserController::class)->group(function () {
  Route::get('/', 'index')->name('user.index')->middleware('can:administrator.user.read');
  Route::get('/form', 'form')->name('user.form')->middleware('can:administrator.user.read');
  Route::post('/', 'store')->name('user.store')->middleware('can:administrator.user.update');
  Route::post('/store', 'multiple_store')->name('user.multiple.store')->middleware('can:administrator.user.update');
  Route::get('/edit', 'edit')->name('user.edit')->middleware('can:administrator.user.update');
});

//log user activity
Route::prefix('log')->controller(LogController::class)->group(function () {
  Route::get('/', 'index')->name('log.index')->middleware('can:administrator.log.read');
});

//reset password on login
// Route::prefix('resetpassword')->controller(PasswordController::class)->group(function () {
//   Route::get('/', 'index')->name('user.password.index');
//   Route::patch('/update', 'update')->name('user.password.update');
// });

// roles
Route::prefix('role')->controller(RoleController::class)->group(function () {
  Route::get('/', 'index')->name('role.index')->middleware('can:administrator.role.read');
  Route::post('/', 'store')->name('role.store')->middleware('can:administrator.role.update');
  Route::post('/delete', 'destroy')->name('role.destroy')->middleware('can:administrator.role.read');
  Route::get('/form/{id?}', 'form')->name('role.form')->middleware('can:administrator.role.update');
});

// permission
Route::prefix('permission')->controller(PermissionController::class)->group(function () {
  Route::get('/', 'index')->name('permission.index')->middleware('can:administrator.permission.read');
  Route::post('/', 'store')->name('permission.store')->middleware('can:administrator.permission.update');
  Route::get('/edit', 'edit')->name('permission.edit')->middleware('can:administrator.permission.update');
});

Route::prefix('it-asset/maintenance')->name('maintenance.')->group(function() {
  Route::get('/get', [MaintenanceController::class, 'getData'])->name('data');
  Route::resource('/', MaintenanceController::class)->parameters([
    '' => 'id'
  ]);
});

Route::prefix('it-asset')->name('it_asset.')->controller(ITAssetController::class)->group(function() {
  Route::get('/', 'index')->name('index')->middleware("can:itsm.it-asset.read");
  Route::get('/get-it-assets', 'getItAssets')->name('get_assets')->middleware("can:itsm.it-asset.read");
  Route::get('/disposed', 'disposed')->name('disposed')->middleware("can:itsm.it-asset.read");
  Route::get('/owners', 'owners')->name('owners')->middleware("can:itsm.it-asset.read");
  Route::get('/{id}/get', 'getItAsset')->name('get_asset')->middleware("can:itsm.it-asset.read");
  Route::get('/{id}', 'show')->name('show')->middleware("can:itsm.it-asset.read");
  Route::get('/template/download', 'download')->name('download')->middleware('can:itsm.it-asset.read');
  
  Route::post('/import/preview', 'preview')->name('preview')->middleware('can:itsm.it-asset.create');
  Route::post('/import/upsert', 'upsert')->name('import.upsert')->middleware('can:itsm.it-asset.create');
  
  // print QR code
  Route::post('/print/preview', 'printPreview')->name('print-preview')->middleware('can:itsm.it-asset.read');
  Route::post('/print', 'print')->name('print')->middleware('can:itsm.it-asset.read');
  
  Route::get('/movement/{id}', 'movement')->name('movement')->middleware('can:itsm.it-asset.update');
  Route::post('/movement/{id}', 'movementUpdate')->name('movement.update')->middleware('can:itsm.it-asset.update');
  Route::post('/', 'store')->name('store')->middleware('can:itsm.it-asset.create');

  Route::post('/update-status/{id}', 'updateStatus')->name('update-status')->middleware('can:itsm.it-asset.update');

  Route::get('/{itAsset}/edit/{role?}', 'edit')->name('edit')->middleware('can:itsm.it-asset.update');
  Route::put('/{itAsset}/{role?}', 'update')->name('update')->middleware('can:itsm.it-asset.update');
  Route::delete('/{itAsset}', 'destroy')->name('destroy')->middleware('can:itsm.it-asset.delete');
});

Route::prefix('asset-type')->name('asset-type.')->group(function( ){
  Route::resource('/', AssetTypeController::class)->parameters([
    '' => 'id'
  ])->middleware('can:itsm.asset-type.read');
});
Route::get('/get/asset-type', [AssetTypeController::class, 'getData'])->name('asset-type.data')->middleware('can:itsm.asset-type.read');

Route::prefix('asset-disposal')->name('asset-disposal.')->controller(AssetDisposalController::class)->group(function() {
  Route::get('/', 'index')->name('index')->middleware("can:itsm.asset-disposal.read");
  Route::get('/get-disposal', 'getDisposalRequests')->name('get-disposal')->middleware("can:itsm.asset-disposal.read");
  Route::get('/view/{id}/{type?}', 'show')->name('show')->middleware("can:itsm.asset-disposal.read"); // this is to show the detail of asset disposal request. type = review to change into review mode for approver.
  Route::get('/revision/{id}', 'revision')->name('revision')->middleware("can:itsm.asset-disposal.read"); // this is for requester to make a revision
  Route::delete('/{id}', 'cancel')->name('cancel')->middleware("can:itsm.asset-disposal.read");
  Route::post('/preview', 'preview')->name('preview')->middleware("can:itsm.asset-disposal.create"); // Show form to submit a request
  Route::post('/store', 'store')->name('store')->middleware("can:itsm.asset-disposal.create");
});

Route::prefix('service-desk')->name('service-management.')->controller(ServiceManagementController::class)->group(function() {
  Route::get('/employee/{id}/assets', 'employeeAsset')->name('getEmpAsset');
  Route::get('/', 'index')->name('index');
 
  Route::get('/it-initiate', 'createInitiative')->name('initiate');
  Route::get('/{id}/analize', 'analysis')->name('analysis');
  Route::get('{id}/messages', 'getMessages')->name('get-messages');
  Route::get('/{id}/{role}', 'workspace')->name('workspace');
  Route::get('/closed', 'closedTickets')->name('closed');
  Route::post('/resend-notification/{id}/{role?}', 'resendNotification')->name('resend-notification');
  Route::post('{id}/subject-update', 'updateSubject')->name('update-subject');
  Route::post('/get-public-link/{ticketId}/{role}', 'getPublicLink')->name('get-public-link');
  Route::post('/{id}/add-asset', 'addAsset')->name('add-asset');
  Route::post('/{id}/remove-asset', 'removeAsset')->name('remove-asset');

  Route::get('/change-management', function() {
    return 'test';
  })->name('change');
});

Route::prefix('change-management')->name('service-change.')->group(function() {
  Route::get('/get', [ServiceChangeController::class, 'getData'])->name('data');
  Route::resource('/', ServiceChangeController::class)->parameters([
    '' => 'id'
  ])->middleware('can:itsm.service-change.read');
});

Route::prefix('service-catalog')->name('service-catalog.')->group(function() {
  Route::get('/get', [ServiceCatalogController::class, 'getData'])->name('data');
  Route::resource('/', ServiceCatalogController::class)->parameters([
    '' => 'id'
  ])->middleware('can:itsm.priority.read');
});

Route::prefix('priority')->name('priority.')->group(function() {
  Route::get('/get', [ItsmPriorityController::class, 'getData'])->name('data')->middleware('can:itsm.priority.read');
  Route::resource('/', ItsmPriorityController::class)->parameters([
    '' => 'id'
  ])->middleware('can:itsm.priority.read');
});

Route::prefix('risk-register')->name('risk-register.')->group(function() {
  Route::get('/get', [RiskRegisterController::class, 'getData'])->name('data')->middleware('can:itsm.priority.read');
  Route::resource('/', RiskRegisterController::class)->parameters([
    '' => 'id'
  ])->middleware('can:itsm.priority.read');
});

Route::prefix('knowledge-base')->name('knowledge-base.')->group(function() {
  Route::get('/get', [KnowledgeBaseController::class, 'getData'])->name('data');
  Route::post('/upsert/{id?}', [KnowledgeBaseController::class, 'upsert'])->name('upsert')->middleware(['can:itsm.knowledge-base.create', 'can:itsm.knowledge-base.update']);
  Route::post('/update-status/{id}', [KnowledgeBaseController::class, 'updateStatus'])->name('update-status')->can('itsm.knowledge-base.update');
  Route::delete('media/{id}', [KnowledgeBaseController::class, 'deleteMedia'])->name('media.delete')->can('itsm.knowledge-base.update');
  Route::resource('/', KnowledgeBaseController::class)->parameters([
    '' => 'id'
  ])->except(['show'])->middleware('can:itsm.knowledge-base.read');
});

Route::prefix('priority-metric')->name('priority-metric.')->group(function() {
  Route::get('/get', [PriorityMetricController::class, 'getData'])->name('data')->middleware('can:itsm.priority.read');
  Route::post('/check-duplicate', [PriorityMetricController::class, 'checkDuplicate'])->name('check-duplicate')->middleware('can:itsm.priority.create');
  Route::post('/upsert/{id?}', [PriorityMetricController::class, 'upsert'])->name('upsert')->middleware('can:itsm.priority.update');
  Route::resource('/', PriorityMetricController::class)->parameters([
    '' => 'id'
  ])->middleware('can:itsm.priority.read');
});