<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'attendee_name',
        'attendee_email',
        'status',
    ];

    
    public function workshop(): BelongsTo{
        return $this->belongsTo(Workshop::class);
    }


    public function scopeConfirmed(Builder $query): Builder {
        return $query->where('status', 'confirmed');
    }
    public function scopeWaitlisted(Builder $query): Builder{
        return $query->where('status', 'waitlisted');
    }
    public function scopeCancelled(Builder $query): Builder {
        return $query->where('status', 'cancelled');
    }
}