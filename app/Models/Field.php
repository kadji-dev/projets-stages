<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['cursus_id', 'code', 'label'];

    public function cursus()
    {
        return $this->belongsTo(Cursus::class);
    }

    public function specialities()
    {
        return $this->hasMany(Speciality::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class)->orderBy('order');
    }
}
