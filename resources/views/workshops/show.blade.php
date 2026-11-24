@extends('layouts.app')

@section('title', $workshop->title)

@section('content')
    <div class="mb-4">
        <a href="{{ route('workshops.index') }}" class="text-sm text-gray-600 hover:underline">
            &larr; Back to Workshops
        </a>
    </div>

    @php
        $confirmedCount = $workshop->confirmed_count ?? $workshop->confirmedReservationsCount();
        $remainingSeats = max(0, $workshop->capacity - $confirmedCount);
        $hasStarted = $workshop->hasStarted();
    @endphp

    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $workshop->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Scheduled: {{ $workshop->scheduled_at->format('F j, Y \a\t g:i A') }}
                </p>
            </div>

            <div class="text-right">
                @if ($hasStarted)
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                        Workshop Started — Reservations Closed
                    </span>
                @elseif ($remainingSeats > 0)
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        {{ $remainingSeats }} of {{ $workshop->capacity }} seats available
                    </span>
                @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                        Full ({{ $confirmedCount }}/{{ $workshop->capacity }}) — Waitlist Available
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 text-gray-700 whitespace-pre-line">
            {{ $workshop->description }}
        </div>
    </div>

    <div class="max-w-xl bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Reserve Your Spot</h2>

        @if ($hasStarted)
            <div class="p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded text-sm">
                This workshop has already started and is no longer accepting reservations.
            </div>
        @else
            <p class="text-sm text-gray-600 mb-4">
                @if ($remainingSeats > 0)
                    Seats are available. Your reservation will be confirmed immediately.
                @else
                    All seats are currently taken. Submitting this form will place you on the waitlist, and you will be automatically promoted if a spot opens up.
                @endif
            </p>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('workshops.reserve', $workshop) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="attendee_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text"
                           name="attendee_name"
                           id="attendee_name"
                           value="{{ old('attendee_name') }}"
                           required
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label for="attendee_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email"
                           name="attendee_email"
                           id="attendee_email"
                           value="{{ old('attendee_email') }}"
                           required
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md font-medium hover:bg-indigo-700">
                        {{ $remainingSeats > 0 ? 'Confirm Reservation' : 'Join Waitlist' }}
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection