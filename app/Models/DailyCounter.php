<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'service_type_id',
        'last_number',
        'total_generated',
    ];

    protected $casts = [
        'date' => 'date',
        'last_number' => 'integer',
        'total_generated' => 'integer',
    ];

    // Relationships
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    // Get or create counter for today
    public static function getForToday($serviceTypeId)
    {
        return static::firstOrCreate(
            [
                'date' => today(),
                'service_type_id' => $serviceTypeId,
            ],
            [
                'last_number' => 0,
                'total_generated' => 0,
            ]
        );
    }

    // Reset all counters for today
    public static function resetToday()
    {
        return static::whereDate('date', today())
            ->update(['last_number' => 0, 'total_generated' => 0]);
    }
}
