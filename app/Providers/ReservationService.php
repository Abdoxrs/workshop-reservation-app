<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function makeReservation(Workshop $workshop, string $name, string $email): Reservation{
        if(! $workshop->isPublished()) {
            throw ValidationException::withMessages([
                'workshop' =>'reservations are only allowed for published workshops only'
            ]);
        }
        if ($workshop->hasStarted()) {
            throw ValidationException::withMessages([
                'workshop' =>'this workshop has already started n is no longer accepting reservations'
            ]);
        }
        
        $duplictes = $workshop->reservations()->where('attendee_email', $email)
            ->where('status', '!=', 'cancelled')
            ->exists(); // t or f
        if ($duplictes) {
            throw ValidationException::withMessages([
                'attendee_email' => 'You have already reserved this workshop.',
            ]);
        }

        return DB::transaction(function () use ($workshop, $name, $email): Reservation {
            $lockedWorkshop = Workshop::whereKey($workshop->id)->lockForUpdate()->firstOrFail();

            $confirmedCount = $lockedWorkshop->reservations()->confirmed()->count();

            $status = $confirmedCount < $lockedWorkshop->capacity
                ? 'confirmed'
                : 'waitlisted';

            return $lockedWorkshop->reservations()->create([
                'attendee_name' => $name,
                'attendee_email' => $email,
                'status' => $status,
            ]);
        });
    }

    public function cancelReservation(Reservation $reservation): void{
        if ($reservation->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'reservation' => 'Only confirmed reservations can be cancelled',
            ]);
        }

        DB::transaction(function () use ($reservation): void {
            $lockedWorkshop = Workshop::whereKey($reservation->workshop_id)
                ->lockForUpdate()
                ->firstOrFail();

            $reservation->update(['status' => 'cancelled']);

            $oldestWaitlisted = $lockedWorkshop->reservations()->waitlisted()
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')->first();

            if ($oldestWaitlisted) {
                $oldestWaitlisted->update(['status' => 'confirmed']);
            }
        });
    }
}