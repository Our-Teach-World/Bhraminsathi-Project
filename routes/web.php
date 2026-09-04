<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| BhraminSathi Web Routes
|--------------------------------------------------------------------------
*/

// 1. Passenger Application View (Default Homepage)
Route::get('/', [PassengerController::class, 'index'])->name('passenger.index');

// 2. Conductor Dashboard View & API endpoints
Route::get('/conductor', [ConductorController::class, 'dashboard']);
Route::get('/conductor/dashboard', [ConductorController::class, 'dashboard'])->name('conductor.dashboard');
Route::post('/api/conductor/register', [ConductorController::class, 'register']);
Route::post('/conductor/register', [ConductorController::class, 'register']);
Route::post('/api/conductor/session/start', [ConductorController::class, 'startSession']);
Route::post('/api/conductor/session/stop', [ConductorController::class, 'stopSession']);
Route::post('/api/conductor/location', [ConductorController::class, 'updateLocation']);

// 3. Admin Dashboard & Panel Views
Route::get('/admin', [AdminController::class, 'overview']);
Route::get('/admin/dashboard', [AdminController::class, 'overview'])->name('admin.dashboard');
Route::get('/admin/bus/{id}', [AdminController::class, 'busDetail'])->name('admin.bus.detail');
Route::get('/admin/session-logs', [AdminController::class, 'sessionLogs'])->name('admin.session.logs');
Route::post('/api/admin/buses/{id}/terminate', [AdminController::class, 'terminateSession']);
Route::post('/admin/buses/{id}/terminate', [AdminController::class, 'terminateSession']);
