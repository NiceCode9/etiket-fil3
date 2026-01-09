<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasTenant;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes, HasTenant;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'venue',
        'event_date',
        'event_end_date',
        'poster_image',
        'status',
        'tenant_id',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name);
            }
        });
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Scope untuk event yang published
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Scope untuk event yang akan datang
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }
}
