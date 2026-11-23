<?php

use App\Http\Controllers\Organizer\ReservationController as OrganizerReservationController;
use App\Http\Controllers\Organizer\WorkshopController as OrganizerWorkshopController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Visitor routes (public)
Route::get('/', [WorkshopController::class, 'index'])->name('home');
Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])->name('workshops.show');
Route::post('/workshops/{workshop}/reserve', [ReservationController::class, 'store'])->name('workshops.reserve');

// Organizer routes
Route::prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/workshops', [OrganizerWorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/create', [OrganizerWorkshopController::class, 'create'])->name('workshops.create');
    Route::post('/workshops', [OrganizerWorkshopController::class, 'store'])->name('workshops.store');
    Route::patch('/workshops/{workshop}/publish', [OrganizerWorkshopController::class, 'publish'])->name('workshops.publish');
    Route::get('/workshops/{workshop}', [OrganizerWorkshopController::class, 'show'])->name('workshops.show');

    Route::patch('/reservations/{reservation}/cancel', [OrganizerReservationController::class, 'cancel'])->name('reservations.cancel');
});

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json(['status' => 'ok'], 200);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'degraded'], 503);
    }
})->name('health');