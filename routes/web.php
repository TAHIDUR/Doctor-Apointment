<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::get('/get-doctor', [HomeController::class, 'getDoctor']);

Route::get('/get-schedule', [HomeController::class, 'getSchedule']);

Route::get('/check-appointment', [HomeController::class, 'checkAppointment']);

Route::get('/set-appointment', [HomeController::class, 'setAppointment']);

Route::get('/delete-appointment', [HomeController::class, 'deleteAppointment']);

