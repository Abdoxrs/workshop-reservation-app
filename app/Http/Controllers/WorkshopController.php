<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(): View
    {
        $workshops = Workshop::where('status', 'published')
            ->withCount([
                'reservations as confirmed_count' => function ($query) {
                    $query->where('status', 'confirmed');
                },
            ])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('workshops.index', compact('workshops'));
    }

    public function show(Workshop $workshop): View
    {
        abort_if(! $workshop->isPublished(), 404);

        $workshop->loadCount([
            'reservations as confirmed_count' => function ($query) {
                $query->where('status', 'confirmed');
            },
        ]);

        return view('workshops.show', compact('workshop'));
    }
}