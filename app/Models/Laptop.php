<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    protected $fillable = [
        'reference', 'brand', 'model', 'serial_number', 'status', 'notes',
    ];

    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'disponible' => 'success',
            'attribue' => 'default',
            'maintenance' => 'danger',
            default => 'default',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'disponible' => 'Disponible',
            'attribue' => 'Attribué',
            'maintenance' => 'Maintenance',
            default => $this->status,
        };
    }
}
