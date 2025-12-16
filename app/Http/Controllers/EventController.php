<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('ticketTypes')
            ->published()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = Event::with(['ticketTypes.warTickets'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Add current price to each ticket type
        $event->ticketTypes->each(function ($ticketType) {
            $ticketType->current_price = $ticketType->getCurrentPrice();
            $ticketType->war_ticket = $ticketType->getActiveWarTicket();
        });

        return view('events.show', compact('event'));
    }
}
