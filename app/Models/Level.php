<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['field_id', 'speciality_id', 'code', 'label', 'order'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    // Accès pratique au cursus via la filière (pas de colonne cursus_id directe).
    public function cursus()
    {
        return $this->field?->cursus;
    }
}
