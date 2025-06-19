<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deteccion extends Model
{   
    protected $table = 'detecciones';
    protected $fillable = ['objeto_id', 'archivo', 'cantidad_detectada', 'resultado'];

    public function objeto()
    {
        return $this->belongsTo(Objeto::class);
    }
}
