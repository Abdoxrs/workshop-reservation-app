<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Workshop Reservation')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
    <nav class="bg-white shadow mb-8">
        <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('workshops.index') }}" class="text-lg font-bold text-indigo-600">
                WorkshopHub
            </a>
            <div class="space-x-4 text-sm font-medium">
                <a href="{{ route('workshops.index') }}" class="text-gray-600 hover:text-indigo-600">
                    Visitor View
                </a>
                <a href="{{ route('organizer.workshops.index') }}" class="text-gray-600 hover:text-indigo-600">
                    Organizer Dashboard
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 pb-12">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>