<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_id',
        'from_window_id',
        'to_window_id',
        'from_agent_id',
        'to_agent_id',
        'reason',
    ];

    // Relationships
    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function fromWindow()
    {
        return $this->belongsTo(ServiceWindow::class, 'from_window_id');
    }

    public function toWindow()
    {
        return $this->belongsTo(ServiceWindow::class, 'to_window_id');
    }

    public function fromAgent()
    {
        return $this->belongsTo(User::class, 'from_agent_id');
    }

    public function toAgent()
    {
        return $this->belongsTo(User::class, 'to_agent_id');
    }
}
