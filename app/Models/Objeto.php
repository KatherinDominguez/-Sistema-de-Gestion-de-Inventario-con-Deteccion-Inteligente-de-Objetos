<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    protected $fillable = ['nombre', 'forma', 'color','categoria'];
    public function detecciones()
    {
        return $this->hasMany(Deteccion::class);
    }
}
