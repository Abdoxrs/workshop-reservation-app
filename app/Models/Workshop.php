<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'scheduled_at',
        'capacity',
        'status',
    ];

    protected function casts(): array{
        return [
            'scheduled_at' => 'datetime',
            'capacity' => 'integer',];
    }

    public function reservations(): HasMany{
        return $this->hasMany(Reservation::class);
    }

    public function isPublished(): bool{
        return $this->status === 'published';
    }

    public function hasStarted(): bool{
        return $this->scheduled_at->isPast();
    }

    public function confirmedReservationsCount(): int{
        return $this->reservations()->confirmed()->count();
    }

    public function availableSeats(): int{
        return max(0, $this->capacity - $this->confirmedReservationsCount());
    }

    public function isFull(): bool{
        return $this->availableSeats() <=0;
    }
}
