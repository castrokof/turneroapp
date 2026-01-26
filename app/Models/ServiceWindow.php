<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceWindow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'current_agent_id',
        'location',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    // Relationships
    public function currentAgent()
    {
        return $this->belongsTo(User::class, 'current_agent_id');
    }

    public function serviceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'service_type_service_window');
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['active', 'inactive']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // Get current queue being attended
    public function getCurrentQueue()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->whereIn('status', ['called', 'in_progress'])
            ->first();
    }

    // Check if window is available
    public function isAvailable()
    {
        return $this->status === 'active' && !$this->getCurrentQueue();
    }

    // Get pending queues count
    public function getPendingCount()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->where('status', 'pending')
            ->count();
    }

    // Activate window with agent
    public function activate($agentId)
    {
        $this->update([
            'status' => 'active',
            'current_agent_id' => $agentId,
        ]);
    }

    // Deactivate window
    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'current_agent_id' => null,
        ]);
    }

    // Pause window
    public function pause()
    {
        $this->update(['status' => 'paused']);
    }
}
