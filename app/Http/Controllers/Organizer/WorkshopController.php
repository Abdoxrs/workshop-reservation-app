<?php

namespace App\Http\Controllers\Organizer;
use App\Models\Workshop;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkshopController extends Controller{
    public function index(){
    $workshops = Workshop::withCount([
        'reservations as confirmed_count' => function ($query) {
            $query->where('status', 'confirmed');
        },])
        ->orderBy('scheduled_at', 'desc')
        ->get();

    return view('organizer.workshops.index', compact('workshops'));
    }
    public function create(){
        return view('organizer.workshops.create');
    }
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'capacity' => 'required|integer|min:1',
        ]);

        Workshop::create($request->only(['title', 'description', 'scheduled_at', 'capacity']));
        return redirect()->route('organizer.workshops.index');
    }
    public function show(Workshop $workshop){
        //eager loading reservations
    $workshop->load([
        'reservations' => function ($query) {
            $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        },
    ]);

    return view('organizer.workshops.show', compact('workshop'));
    }
    public function publish(Workshop $workshop){
        $workshop->update(['status' => 'published']);
        return redirect()->back()->with('success', 'Workshop published successfully');
    }
}