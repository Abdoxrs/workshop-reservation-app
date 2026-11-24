<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(
        Request $request,
        Workshop $workshop,
        ReservationService $reservationService
    ): RedirectResponse {
        $validated = $request->validate([
            'attendee_name' => ['required', 'string', 'max:255'],
            'attendee_email' => ['required', 'email', 'max:255'],
        ]);

        $reservation = $reservationService->makeReservation(
            $workshop,
            $validated['attendee_name'],
            $validated['attendee_email']
        );

        $message = $reservation->status === 'confirmed'
            ? 'Your seat has been confirmed!'
            : 'The workshop is currently full. You have been added to the waitlist.';

        return redirect()
            ->route('workshops.show', $workshop)
            ->with('success', $message);
    }
}