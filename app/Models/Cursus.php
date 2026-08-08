<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cursus extends Model
{
    protected $fillable = ['code', 'label', 'duration_years'];

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    // Dérivé : Cursus -> Field -> Level (pas de colonne directe, mais utilisable avec withCount('levels')).
    public function levels()
    {
        return $this->hasManyThrough(Level::class, Field::class);
    }
}
