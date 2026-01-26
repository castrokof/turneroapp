<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'prefix',
        'color',
        'estimated_time',
        'description',
        'is_active',
        'requires_appointment',
        'daily_limit',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_appointment' => 'boolean',
        'estimated_time' => 'integer',
        'daily_limit' => 'integer',
        'display_order' => 'integer',
    ];

    // Relationships
    public function serviceWindows()
    {
        return $this->belongsToMany(ServiceWindow::class, 'service_type_service_window');
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function dailyCounters()
    {
        return $this->hasMany(DailyCounter::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // Get today's counter
    public function getTodayCounter()
    {
        return $this->dailyCounters()
            ->whereDate('date', today())
            ->first();
    }

    // Check if daily limit reached
    public function isDailyLimitReached()
    {
        if (!$this->daily_limit) {
            return false;
        }

        $counter = $this->getTodayCounter();
        return $counter && $counter->total_generated >= $this->daily_limit;
    }

    // Get pending queues count for today
    public function getPendingCountToday()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->where('status', 'pending')
            ->count();
    }
}
