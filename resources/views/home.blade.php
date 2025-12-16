@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="bg-indigo-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-4">Discover Amazing Events</h1>
            <p class="text-xl mb-8">Book your tickets now for the best concerts and shows</p>
            <a href="{{ route('events.index') }}"
                class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
                Browse Events
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold mb-8">Upcoming Events</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($upcomingEvents as $event)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @if ($event->poster_image)
                        <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->name }}"
                            class="w-full h-48 object-cover">
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $event->name }}</h3>
                        <p class="text-gray-600 mb-2">{{ $event->venue }}</p>
                        <p class="text-gray-500 mb-4">{{ $event->event_date->format('d M Y, H:i') }}</p>

                        <a href="{{ route('events.show', $event->slug) }}"
                            class="block text-center bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
