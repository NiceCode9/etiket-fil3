@extends('layouts.app')

@section('title', $event->name)

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if ($event->poster_image)
                <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->name }}" class="w-full h-48 object-cover">
            @endif

            <div class="p-6">
                <h1 class="text-3xl font-bold mb-4">{{ $event->name }}</h1>
                <p class="text-gray-600 mb-4">{{ $event->venue }}</p>
                <p class="text-gray-500 mb-4">{{ $event->event_date->format('d M Y, H:i') }}</p>
                <p class="text-gray-500 mb-4">{{ $event->description }}</p>

                <h2 class="text-2xl font-bold mt-8 mb-4">Ticket Types</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($event->ticketTypes as $ticketType)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-lg font-semibold">{{ $ticketType->name }}</p>
                                    <p class="text-gray-600">Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('checkout', $event) }}"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                    Checkout
                                </a>
                            </div>
                            @if ($ticketType->war_ticket)
                                <p class="mt-2 text-sm text-orange-600">Promo aktif: {{ $ticketType->war_ticket->name }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
