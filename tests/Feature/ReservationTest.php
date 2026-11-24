<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Workshop;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_reserve_an_available_workshop(): void{
        $workshop = Workshop::create([
            'title' => 'Laravel Fundamentals',
            'description' => 'Learn core Laravel concepts.',
            'scheduled_at' => now()->addDays(5),
            'capacity' => 10,
            'status' => 'published',
        ]);

        $response = $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'John Doe',
            'attendee_email' => 'john@example.com',
        ]);

        $response->assertRedirect(route('workshops.show', $workshop));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'workshop_id' => $workshop->id,
            'attendee_name' => 'John Doe',
            'attendee_email' => 'john@example.com',
            'status' => 'confirmed',
        ]);
    }

    public function test_duplicate_reservations_for_same_workshop_and_email_are_prevented(): void
    {
        $workshop = Workshop::create([
            'title' => 'API Design',
            'description' => 'Building clean REST APIs.',
            'scheduled_at' => now()->addDays(5),
            'capacity' => 10,
            'status' => 'published',
        ]);

        $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'Test User',
            'attendee_email' => 'test@example.com',
        ])->assertRedirect();

        $secondResponse = $this->from(route('workshops.show', $workshop))
            ->post(route('workshops.reserve', $workshop), [
                'attendee_name' => 'Test User Again',
                'attendee_email' => 'test@example.com',
            ]);

        $secondResponse->assertRedirect(route('workshops.show', $workshop));
        $secondResponse->assertSessionHasErrors('attendee_email');

        $nonCancelledCount = Reservation::where('workshop_id', $workshop->id)
            ->where('attendee_email', 'test@example.com')
            ->where('status', '!=', 'cancelled')
            ->count();

        $this->assertSame(1, $nonCancelledCount);
    }

    public function test_reservation_becomes_waitlisted_when_workshop_is_full(): void
    {
        $workshop = Workshop::create([
            'title' => 'Small Group Coaching',
            'description' => 'Intensive 1-seat workshop.',
            'scheduled_at' => now()->addDays(4),
            'capacity' => 1,
            'status' => 'published',
        ]);

        $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'First Person',
            'attendee_email' => 'first@example.com',
        ])->assertRedirect();

        $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'Second Person',
            'attendee_email' => 'second@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'workshop_id' => $workshop->id,
            'attendee_email' => 'first@example.com',
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('reservations', [
            'workshop_id' => $workshop->id,
            'attendee_email' => 'second@example.com',
            'status' => 'waitlisted',
        ]);
    }

    public function test_reservations_are_rejected_for_draft_or_already_started_workshops(): void
    {
        $draftWorkshop = Workshop::create([
            'title' => 'Unpublished Draft',
            'description' => 'Not ready yet.',
            'scheduled_at' => now()->addDays(5),
            'capacity' => 10,
            'status' => 'draft',
        ]);

        $this->from(route('workshops.index'))
            ->post(route('workshops.reserve', $draftWorkshop), [
                'attendee_name' => 'Early Bird',
                'attendee_email' => 'early@example.com',
            ])
            ->assertRedirect(route('workshops.index'))
            ->assertSessionHasErrors('workshop');

        $pastWorkshop = Workshop::create([
            'title' => 'Past Event',
            'description' => 'Already happened.',
            'scheduled_at' => now()->subHour(),
            'capacity' => 10,
            'status' => 'published',
        ]);

        $this->from(route('workshops.show', $pastWorkshop))
            ->post(route('workshops.reserve', $pastWorkshop), [
                'attendee_name' => 'Latecomer',
                'attendee_email' => 'late@example.com',
            ])
            ->assertRedirect(route('workshops.show', $pastWorkshop))
            ->assertSessionHasErrors('workshop');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_cancelling_confirmed_reservation_promotes_oldest_eligible_waitlisted_reservation(): void
    {
        $workshop = Workshop::create([
            'title' => 'Concurrency Workshop',
            'description' => 'Testing waitlist FIFO promotion.',
            'scheduled_at' => now()->addDays(3),
            'capacity' => 1,
            'status' => 'published',
        ]);

        // Reservation A (Confirmed)
        $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'Person A',
            'attendee_email' => 'a@example.com',
        ]);

        // Reservation B (Oldest Waitlisted)
        $this->travel(1)->minutes();
        $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'Person B',
            'attendee_email' => 'b@example.com',
        ]);

        // Reservation C (Newer Waitlisted)
        $this->travel(1)->minutes();
        $this->post(route('workshops.reserve', $workshop), [
            'attendee_name' => 'Person C',
            'attendee_email' => 'c@example.com',
        ]);

        $reservationA = Reservation::where('attendee_email', 'a@example.com')->firstOrFail();
        $reservationB = Reservation::where('attendee_email', 'b@example.com')->firstOrFail();
        $reservationC = Reservation::where('attendee_email', 'c@example.com')->firstOrFail();

        $this->patch(route('organizer.reservations.cancel', $reservationA))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('cancelled', $reservationA->fresh()->status);
        $this->assertSame('confirmed', $reservationB->fresh()->status);
        $this->assertSame('waitlisted', $reservationC->fresh()->status);

        $confirmedCount = $workshop->reservations()->where('status', 'confirmed')->count();
        $this->assertSame($workshop->capacity, $confirmedCount);
    }

    public function test_confirmed_reservations_never_exceed_workshop_capacity(): void
    {
        $workshop = Workshop::create([
            'title' => 'Capacity Boundary Test',
            'description' => 'Ensuring strict capacity enforcement.',
            'scheduled_at' => now()->addDays(6),
            'capacity' => 2,
            'status' => 'published',
        ]);

        $service = app(ReservationService::class);

        // Simulate back-to-back transactional service calls
        DB::transaction(function () use ($service, $workshop): void {
            $first = $service->makeReservation($workshop, 'User 1', 'user1@example.com');
            $second = $service->makeReservation($workshop, 'User 2', 'user2@example.com');
            $third = $service->makeReservation($workshop, 'User 3', 'user3@example.com');

            $this->assertSame('confirmed', $first->status);
            $this->assertSame('confirmed', $second->status);
            $this->assertSame('waitlisted', $third->status);
        });

        $confirmedCount = $workshop->reservations()->where('status', 'confirmed')->count();
        $this->assertSame(2, $confirmedCount);
    }
}