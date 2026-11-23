@extends('layouts.app')

@section('title', 'Organizer — Workshops')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manage Workshops</h1>
        <a href="{{ route('organizer.workshops.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-indigo-700">
            + Create Workshop
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Confirmed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($workshops as $workshop)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $workshop->title }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $workshop->scheduled_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $workshop->capacity }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $workshop->confirmed_count ?? $workshop->confirmedReservationsCount() }} / {{ $workshop->capacity }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($workshop->isPublished())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Published
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('organizer.workshops.show', $workshop) }}"
                               class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>

                            @if (! $workshop->isPublished())
                                <form action="{{ route('organizer.workshops.publish', $workshop) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-900 font-medium">
                                        Publish
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No workshops created yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection