<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
  Route::get('/', 'profile_index')->name('profile.index');
  Route::post('/', 'profile_store')->name('profile.store');
});
