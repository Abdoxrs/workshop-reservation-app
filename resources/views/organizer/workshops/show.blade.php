@extends('layouts.app')

@section('title', 'Workshop Details — ' . $workshop->title)

@section('content')
    <div class="mb-4">
        <a href="{{ route('organizer.workshops.index') }}" class="text-sm text-gray-600 hover:underline">
            &larr; Back to Workshops
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $workshop->title }}</h1>
                    @if ($workshop->isPublished())
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Draft
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Scheduled: {{ $workshop->scheduled_at->format('F j, Y \a\t g:i A') }}
                </p>
            </div>

            @if (! $workshop->isPublished())
                <form action="{{ route('organizer.workshops.publish', $workshop) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-700">
                        Publish Workshop
                    </button>
                </form>
            @endif
        </div>

        <p class="mt-4 text-gray-700">{{ $workshop->description }}</p>

        @php
            $confirmedCount = $workshop->reservations->where('status', 'confirmed')->count();
            $waitlistedCount = $workshop->reservations->where('status', 'waitlisted')->count();
        @endphp

        <div class="mt-6 pt-4 border-t border-gray-200 flex space-x-8 text-sm">
            <div>
                <span class="text-gray-500">Confirmed:</span>
                <span class="font-bold text-gray-900">{{ $confirmedCount }} / {{ $workshop->capacity }}</span>
            </div>
            <div>
                <span class="text-gray-500">Waitlisted:</span>
                <span class="font-bold text-amber-600">{{ $waitlistedCount }}</span>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-bold mb-4">Reservations</h2>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendee Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($workshop->reservations as $reservation)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $reservation->attendee_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $reservation->attendee_email }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($reservation->status === 'confirmed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Confirmed
                                </span>
                            @elseif ($reservation->status === 'waitlisted')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    Waitlisted
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                    Cancelled
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            @if ($reservation->status === 'confirmed')
                                <form action="{{ route('organizer.reservations.cancel', $reservation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            onclick="return confirm('Cancel this reservation?')"
                                            class="text-red-600 hover:text-red-900 font-medium">
                                        Cancel
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No reservations for this workshop yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection