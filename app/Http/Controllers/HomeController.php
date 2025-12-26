<?php

namespace App\Http\Controllers;

use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::published()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->take(8)
            ->get();

        return view('home', compact('upcomingEvents'));
    }

    public function about()
    {
        return view('about');
    }
}
