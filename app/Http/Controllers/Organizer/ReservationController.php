<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller{
    public function cancel(Reservation $reservation, ReservationService $reservationService): RedirectResponse{
        try {
            $reservationService->cancelReservation($reservation);
            return redirect()->back()->with('success', 'Reservation cancelled successfully');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
