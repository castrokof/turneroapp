<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'service_window_id',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function serviceWindow()
    {
        return $this->belongsTo(ServiceWindow::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class, 'agent_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Role checks
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isViewer()
    {
        return $this->role === 'viewer';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    // Get queues being attended today
    public function todayQueues()
    {
        return $this->queues()->whereDate('queue_date', today());
    }

    // Get current queue being attended
    public function currentQueue()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->whereIn('status', ['called', 'in_progress'])
            ->first();
    }
}
