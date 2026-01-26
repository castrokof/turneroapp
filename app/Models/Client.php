<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'email',
        'phone',
        'birth_date',
        'gender',
        'is_elderly',
        'is_pregnant',
        'has_disability',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_elderly' => 'boolean',
        'is_pregnant' => 'boolean',
        'has_disability' => 'boolean',
    ];

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getDocumentDisplayAttribute()
    {
        $types = [
            'dni' => 'DNI',
            'passport' => 'Pasaporte',
            'ce' => 'C.E.',
            'ruc' => 'RUC',
            'other' => 'Otro',
        ];

        return ($types[$this->document_type] ?? $this->document_type) . ': ' . $this->document_number;
    }

    // Check if client has priority
    public function hasPriority()
    {
        return $this->is_elderly || $this->is_pregnant || $this->has_disability;
    }

    // Get priority level
    public function getPriorityLevel()
    {
        if ($this->hasPriority()) {
            return 'priority';
        }
        return 'normal';
    }

    // Relationships
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    // Get queue history
    public function getQueueHistory($limit = 10)
    {
        return $this->queues()
            ->with(['serviceType', 'serviceWindow', 'agent'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Scopes
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('document_number', 'like', "%{$term}%")
              ->orWhere('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    public function scopePriority($query)
    {
        return $query->where(function ($q) {
            $q->where('is_elderly', true)
              ->orWhere('is_pregnant', true)
              ->orWhere('has_disability', true);
        });
    }
}
