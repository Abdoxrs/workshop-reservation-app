@extends('layouts.app')

@section('title', 'Upcoming Workshops')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Upcoming Workshops</h1>
        <p class="text-sm text-gray-600 mt-1">Browse published workshops and reserve your spot.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($workshops as $workshop)
            @php
                $confirmedCount = $workshop->confirmed_count ?? $workshop->confirmedReservationsCount();
                $remainingSeats = max(0, $workshop->capacity - $confirmedCount);
                $hasStarted = $workshop->hasStarted();
            @endphp

            <div class="bg-white shadow rounded-lg p-6 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start gap-2">
                        <h2 class="text-lg font-bold text-gray-900">
                            <a href="{{ route('workshops.show', $workshop) }}" class="hover:text-indigo-600">
                                {{ $workshop->title }}
                            </a>
                        </h2>

                        @if ($hasStarted)
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700 shrink-0">
                                Closed
                            </span>
                        @elseif ($remainingSeats > 0)
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 shrink-0">
                                {{ $remainingSeats }} {{ Str::plural('seat', $remainingSeats) }} left
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 shrink-0">
                                Full (Waitlist Open)
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                        {{ $workshop->scheduled_at->format('F j, Y \a\t g:i A') }}
                    </p>

                    <p class="text-sm text-gray-700 mt-3 line-clamp-3">
                        {{ $workshop->description }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs text-gray-500">
                        Capacity: {{ $confirmedCount }} / {{ $workshop->capacity }}
                    </span>

                    <a href="{{ route('workshops.show', $workshop) }}"
                       class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-indigo-700">
                        View &amp; Reserve
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white shadow rounded-lg p-8 text-center text-gray-500">
                No published workshops are available right now.
            </div>
        @endforelse
    </div>
@endsection