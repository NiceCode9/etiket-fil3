<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('ticketTypes')
            ->published()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
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

        return response()->json([
            'success' => true,
            'data' => $event,
        ]);
    }
}
