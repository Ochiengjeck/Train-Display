<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DisplayController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Device registration form
Route::get('/', [DeviceController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [DeviceController::class, 'registerDevice'])->name('register.device');


// Display interface
Route::get('/display', [DisplayController::class, 'showDisplay'])->name('display');
Route::post('/refresh-display', [DisplayController::class, 'refreshDisplay'])->name('refresh.display');