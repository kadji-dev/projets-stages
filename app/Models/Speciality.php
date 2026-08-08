<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    protected $fillable = ['field_id', 'code', 'label'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
