<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model{

    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'categoria',
        'id_servicio'
    ];

    public function preguntas(){
        return $this->hasMany(Pregunta::class, 'id_categoria', 'id_categoria' );
    }
}