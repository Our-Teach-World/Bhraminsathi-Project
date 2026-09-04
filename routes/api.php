<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| BhraminSathi API Endpoints
|--------------------------------------------------------------------------
*/

// Passenger API
Route::get('/passenger/buses', [PassengerController::class, 'getNearbyBuses']);
Route::get('/buses/nearby', [PassengerController::class, 'getNearbyBuses']);

// Conductor Shift & GPS Broadcasting API
Route::post('/conductor/session/start', [ConductorController::class, 'startSession']);
Route::post('/conductor/session/stop', [ConductorController::class, 'stopSession']);
Route::post('/conductor/location', [ConductorController::class, 'updateLocation']);

// Admin Intervention Actions API
Route::post('/admin/buses/{id}/remind', [AdminController::class, 'sendReminder']);
Route::post('/admin/buses/{id}/resolve', [AdminController::class, 'resolveError']);
Route::post('/admin/buses/{id}/terminate', [AdminController::class, 'terminateSession']);
