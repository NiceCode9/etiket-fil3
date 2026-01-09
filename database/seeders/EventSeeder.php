<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Get tenant IDs (assuming TenantSeeder runs first)
        $tenant1 = \App\Models\Tenant::where('slug', 'sample-tenant')->first();
        $tenant2 = \App\Models\Tenant::where('slug', 'music-festival-org')->first();
        $tenant3 = \App\Models\Tenant::where('slug', 'tech-summit-org')->first();

        Event::create([
            'name' => 'Surabaya Music Festival 2025',
            'slug' => 'surabaya-music-festival-2025',
            'description' => 'The biggest music festival in East Java featuring international and local artists',
            'venue' => 'GBK Surabaya',
            'event_date' => now()->addMonths(2),
            'event_end_date' => now()->addMonths(2)->addDays(2),
            'poster_image' => 'events/music-festival.jpg',
            'status' => 'published',
            'tenant_id' => $tenant2?->id ?? $tenant1?->id,
        ]);

        Event::create([
            'name' => 'Tech Summit Indonesia 2025',
            'slug' => 'tech-summit-indonesia-2025',
            'description' => 'Annual technology conference bringing together industry leaders and innovators',
            'venue' => 'Surabaya Convention Center',
            'event_date' => now()->addMonths(3),
            'event_end_date' => now()->addMonths(3)->addDays(1),
            'poster_image' => 'events/tech-summit.jpg',
            'status' => 'published',
            'tenant_id' => $tenant3?->id ?? $tenant1?->id,
        ]);

        Event::create([
            'name' => 'Food Carnival 2025',
            'slug' => 'food-carnival-2025',
            'description' => 'Celebrate culinary diversity with food from across Indonesia',
            'venue' => 'Taman Bungkul',
            'event_date' => now()->addMonth(),
            'event_end_date' => now()->addMonth()->addDays(3),
            'poster_image' => 'events/food-carnival.jpg',
            'status' => 'published',
            'tenant_id' => $tenant1?->id,
        ]);

        Event::create([
            'name' => 'Stand Up Comedy Night',
            'slug' => 'stand-up-comedy-night',
            'description' => 'Hilarious night with Indonesia\'s top comedians',
            'venue' => 'Ballroom Hotel Majapahit',
            'event_date' => now()->addWeeks(3),
            'event_end_date' => now()->addWeeks(3),
            'poster_image' => 'events/comedy-night.jpg',
            'status' => 'published',
            'tenant_id' => $tenant1?->id,
        ]);

        Event::create([
            'name' => 'Future Event (Draft)',
            'slug' => 'future-event-draft',
            'description' => 'This event is still in planning',
            'venue' => 'TBD',
            'event_date' => now()->addMonths(6),
            'event_end_date' => now()->addMonths(6),
            'poster_image' => null,
            'status' => 'draft',
            'tenant_id' => $tenant1?->id,
        ]);
    }
}
