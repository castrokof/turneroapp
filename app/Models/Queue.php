<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Queue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'client_id',
        'service_type_id',
        'service_window_id',
        'agent_id',
        'priority',
        'status',
        'is_express',
        'notes',
        'called_at',
        'started_at',
        'completed_at',
        'wait_time',
        'service_time',
        'queue_date',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'queue_date' => 'date',
        'is_express' => 'boolean',
        'wait_time' => 'integer',
        'service_time' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CALLED = 'called';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ABSENT = 'absent';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_CANCELLED = 'cancelled';

    const PRIORITY_EMERGENCY = 'emergency';
    const PRIORITY_PRIORITY = 'priority';
    const PRIORITY_SCHEDULED = 'scheduled';
    const PRIORITY_NORMAL = 'normal';

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function serviceWindow()
    {
        return $this->belongsTo(ServiceWindow::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function transfers()
    {
        return $this->hasMany(QueueTransfer::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En espera',
            'called' => 'Llamado',
            'in_progress' => 'En atención',
            'completed' => 'Finalizado',
            'absent' => 'Ausente',
            'transferred' => 'Trasladado',
            'cancelled' => 'Cancelado',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'called' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'absent' => 'secondary',
            'transferred' => 'dark',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'emergency' => 'Emergencia',
            'priority' => 'Prioritario',
            'scheduled' => 'Cita',
            'normal' => 'Normal',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'emergency' => 'danger',
            'priority' => 'warning',
            'scheduled' => 'info',
            'normal' => 'secondary',
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    // Get wait time in human readable format
    public function getWaitTimeFormatted()
    {
        if (!$this->wait_time) {
            return '-';
        }

        $minutes = floor($this->wait_time / 60);
        $seconds = $this->wait_time % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }
        return "{$seconds}s";
    }

    // Get service time in human readable format
    public function getServiceTimeFormatted()
    {
        if (!$this->service_time) {
            return '-';
        }

        $minutes = floor($this->service_time / 60);
        $seconds = $this->service_time % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }
        return "{$seconds}s";
    }

    // Get current wait time (for pending queues)
    public function getCurrentWaitTime()
    {
        if ($this->status !== 'pending') {
            return $this->wait_time;
        }

        return now()->diffInSeconds($this->created_at);
    }

    // Get current service time (for in_progress queues)
    public function getCurrentServiceTime()
    {
        if (!$this->started_at) {
            return 0;
        }

        if ($this->completed_at) {
            return $this->service_time;
        }

        return now()->diffInSeconds($this->started_at);
    }

    // Clear display cache when queue state changes (for TV displays)
    protected static function clearDisplayCache()
    {
        // Clear all display cache keys (with different service_type_id and show_next combinations)
        Cache::forget('display_data__5');
        Cache::forget('display_data__8');
        Cache::forget('display_data_null_5');
        Cache::forget('display_data_null_8');

        // Also clear specific service type caches (1-10)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("display_data_{$i}_5");
            Cache::forget("display_data_{$i}_8");
        }
    }

    // State transitions
    public function call($windowId, $agentId)
    {
        $this->update([
            'status' => self::STATUS_CALLED,
            'service_window_id' => $windowId,
            'agent_id' => $agentId,
            'called_at' => now(),
            'wait_time' => now()->diffInSeconds($this->created_at),
        ]);

        self::clearDisplayCache();
    }

    public function startService()
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);

        self::clearDisplayCache();
    }

    public function complete($notes = null)
    {
        $data = [
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'service_time' => now()->diffInSeconds($this->started_at),
        ];

        if ($notes) {
            $data['notes'] = $notes;
        }

        $this->update($data);
        self::clearDisplayCache();
    }

    public function markAbsent()
    {
        $this->update([
            'status' => self::STATUS_ABSENT,
            'completed_at' => now(),
        ]);

        self::clearDisplayCache();
    }

    public function cancel()
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'completed_at' => now(),
        ]);

        self::clearDisplayCache();
    }

    public function transfer($toWindowId, $reason = null)
    {
        // Create transfer record
        QueueTransfer::create([
            'queue_id' => $this->id,
            'from_window_id' => $this->service_window_id,
            'to_window_id' => $toWindowId,
            'from_agent_id' => $this->agent_id,
            'reason' => $reason,
        ]);

        self::clearDisplayCache();

        // Reset queue
        $this->update([
            'status' => self::STATUS_PENDING,
            'service_window_id' => null,
            'agent_id' => null,
            'called_at' => null,
            'started_at' => null,
        ]);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('queue_date', today());
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CALLED, self::STATUS_IN_PROGRESS]);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_ABSENT, self::STATUS_CANCELLED]);
    }

    public function scopeForWindow($query, $windowId)
    {
        return $query->where(function ($q) use ($windowId) {
            $q->where('service_window_id', $windowId)
              ->orWhereNull('service_window_id');
        });
    }

    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw("CASE priority
            WHEN 'emergency' THEN 1
            WHEN 'priority' THEN 2
            WHEN 'scheduled' THEN 3
            WHEN 'normal' THEN 4
            ELSE 5
            END")
            ->orderBy('created_at');
    }

    // Static methods
    public static function generateTicketNumber($serviceTypeId)
    {
        $serviceType = ServiceType::findOrFail($serviceTypeId);
        $today = today();

        $counter = DailyCounter::firstOrCreate(
            [
                'date' => $today,
                'service_type_id' => $serviceTypeId,
            ],
            [
                'last_number' => 0,
                'total_generated' => 0,
            ]
        );

        $counter->increment('last_number');
        $counter->increment('total_generated');

        return $serviceType->prefix . '-' . str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);
    }
}
