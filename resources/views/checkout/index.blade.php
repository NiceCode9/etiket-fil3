@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Checkout - {{ $event->name }}</h1>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Event Information</h2>
                <p class="mb-2"><strong>Venue:</strong> {{ $event->venue }}</p>
                <p class="mb-2"><strong>Date:</strong> {{ $event->event_date->format('d M Y, H:i') }}</p>
            </div>

            <form method="POST" action="{{ route('checkout.process', $event) }}" class="bg-white rounded-lg shadow p-6">
                @csrf

                <h2 class="text-xl font-bold mb-4">Your Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" class="w-full border rounded px-3 py-2"
                               value="{{ old('full_name') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded px-3 py-2"
                               value="{{ old('email') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone_number" class="w-full border rounded px-3 py-2"
                               value="{{ old('phone_number') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Identity Type</label>
                        <select name="identity_type" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Identity Type</option>
                            <option value="ktp" @selected(old('identity_type') === 'ktp')>KTP</option>
                            <option value="sim" @selected(old('identity_type') === 'sim')>SIM</option>
                            <option value="passport" @selected(old('identity_type') === 'passport')>Passport</option>
                            <option value="lainnya" @selected(old('identity_type') === 'lainnya')>Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Identity Number</label>
                        <input type="text" name="identity_number" class="w-full border rounded px-3 py-2"
                               value="{{ old('identity_number') }}" required>
                    </div>
                </div>

                <h2 class="text-xl font-bold mb-4">Select Tickets</h2>
                <div class="space-y-4 mb-6">
                    @foreach ($event->ticketTypes as $i => $ticketType)
                        <div class="border rounded p-4 flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $ticketType->name }}</p>
                                <p class="text-gray-600">Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}</p>
                                @if ($ticketType->war_ticket)
                                    <p class="text-sm text-orange-600">Promo aktif: {{ $ticketType->war_ticket->name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="items[{{ $i }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                <input type="number" name="items[{{ $i }}][quantity]" min="0" value="{{ old('items.' . $i . '.quantity', 0) }}"
                                       class="w-24 border rounded px-3 py-2">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                    Continue to Payment
                </button>
            </form>
        </div>
    </div>
@endsection
