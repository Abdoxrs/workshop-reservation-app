<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function makeReservation(Workshop $workshop, string $name, string $email): Reservation
    {
        if (! $workshop->isPublished()) {
            throw ValidationException::withMessages([
                'workshop' => 'Reservations are allowed for published workshops only.',
            ]);
        }

        if ($workshop->hasStarted()) {
            throw ValidationException::withMessages([
                'workshop' => 'This workshop has already started and is no longer accepting reservations.',
            ]);
        }

        $normalizedEmail = strtolower(trim($email));

        return DB::transaction(function () use ($workshop, $name, $normalizedEmail): Reservation {
            $lockedWorkshop = Workshop::whereKey($workshop->id)
                ->lockForUpdate()
                ->firstOrFail();

            $duplicates = $lockedWorkshop->reservations()
                ->where('attendee_email', $normalizedEmail)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($duplicates) {
                throw ValidationException::withMessages([
                    'attendee_email' => 'You have already reserved this workshop.',
                ]);
            }

            $confirmedCount = $lockedWorkshop->reservations()->confirmed()->count();

            $status = $confirmedCount < $lockedWorkshop->capacity
                ? 'confirmed'
                : 'waitlisted';

            return $lockedWorkshop->reservations()->create([
                'attendee_name' => trim($name),
                'attendee_email' => $normalizedEmail,
                'status' => $status,
            ]);
        });
    }

    public function cancelReservation(Reservation $reservation): void
    {
        if ($reservation->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'reservation' => 'Only confirmed reservations can be cancelled.',
            ]);
        }

        DB::transaction(function () use ($reservation): void {
            $lockedWorkshop = Workshop::whereKey($reservation->workshop_id)
                ->lockForUpdate()
                ->firstOrFail();

            $reservation->update(['status' => 'cancelled']);

            $oldWaitL = $lockedWorkshop->reservations()
                ->waitlisted()
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            if ($oldWaitL) {
                $oldWaitL->update(['status' => 'confirmed']);
            }
        });
    }
}