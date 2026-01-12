<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('ticketTypes')
            ->published()
            ->upcoming();

        // Filter: Search (by name, description, venue)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        // Filter: Location (mapped to venue)
        if ($request->filled('location')) {
            $location = $request->location;
            $query->where('venue', 'like', "%{$location}%");
        }

        // Filter: Price Range
        if ($request->filled('price_range') && $request->price_range !== 'all') {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) === 2) {
                $minPrice = (int) $priceRange[0];
                $maxPrice = (int) $priceRange[1];

                // Filter berdasarkan harga tiket termurah
                $query->whereHas('ticketTypes', function ($q) use ($minPrice, $maxPrice) {
                    $q->whereBetween('price', [$minPrice, $maxPrice]);
                });
            }
        }

        // Filter: Period
        if ($request->filled('period')) {
            $now = now();

            switch ($request->period) {
                case 'today':
                    $query->whereDate('event_date', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('event_date', [
                        $now->startOfWeek()->toDateString(),
                        $now->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('event_date', $now->month)
                        ->whereYear('event_date', $now->year);
                    break;
            }
        }

        // Sort
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'price_low':
                // Sort by cheapest ticket price
                $query->leftJoin('ticket_types', function ($join) {
                    $join->on('events.id', '=', 'ticket_types.event_id');
                })
                    ->selectRaw('events.*, MIN(ticket_types.price) as min_price')
                    ->groupBy('events.id')
                    ->orderBy('min_price', 'asc');
                break;

            case 'price_high':
                // Sort by highest ticket price
                $query->leftJoin('ticket_types', function ($join) {
                    $join->on('events.id', '=', 'ticket_types.event_id');
                })
                    ->selectRaw('events.*, MAX(ticket_types.price) as max_price')
                    ->groupBy('events.id')
                    ->orderBy('max_price', 'desc');
                break;

            case 'popular':
                // Sort by most orders (requires orders table)
                $query->withCount('orders')
                    ->orderBy('orders_count', 'desc')
                    ->orderBy('event_date', 'asc');
                break;

            case 'newest':
            default:
                $query->orderBy('created_at', 'desc')
                    ->orderBy('event_date', 'asc');
                break;
        }

        // Pagination
        $events = $query->paginate(12)->withQueryString();

        // Calculate min price for each event (untuk tampilan)
        $events->getCollection()->transform(function ($event) {
            $event->min_price = $event->ticketTypes->min('price') ?? 0;
            return $event;
        });

        return view('events.events', compact('events'));
    }

    public function show($slug)
    {
        $event = Event::with([
            'ticketTypes' => function ($query) {
                $query->where('is_active', true)
                    ->where('available_quota', '>', 0);
            },
            'ticketTypes.warTickets'
        ])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Add current price to each ticket type
        $event->ticketTypes->each(function ($ticketType) {
            $ticketType->current_price = $ticketType->getCurrentPrice();
            $ticketType->war_ticket = $ticketType->getActiveWarTicket();
            $ticketType->is_available = $ticketType->isAvailable(1);
        });

        return view('events.show', compact('event'));
    }
}
