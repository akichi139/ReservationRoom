<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\RoomController;
use App\Mail\ResponseMail;
use App\Models\Reserve;
use App\Models\Room;
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

Route::middleware(['auth'])->group(function () {
    Route::resource('reserve', ReserveController::class);
    Route::resource('room', RoomController::class);
    Route::get('/room/{room}/reserve', [RoomController::class, 'reserve'])->name('room.reserve');
    Route::get('/getdatacalendar', [RoomController::class, 'getCalendaEvents'])->name('room.calendar');
    Route::get('/reserve_timeslot/{date?}', [ReserveController::class, 'indextimeslot'])->name('timeslots');
    Route::get('/reserve_update_status/{id}/{status}', [ReserveController::class, 'changePermissionStatus'])->name('updateReserveStatus');
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//admin route
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');

    //admin protect route
    Route::middleware(['auth'])->group(function () {
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/', function () {
            return view('admin.dashboard');
        });
    });
});
