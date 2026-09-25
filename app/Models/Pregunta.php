<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $table = 'pregunta';
    protected $primaryKey = 'id_pregunta';

    protected $fillable = [
        'id_pregunta',
        'pregunta'
    ];

    public function preguntas(){
        return $this->BelongsTo(Encuesta::class, 'id_encuesta', 'id_encuesta');
    }

    public function tipo_pregunta(){
        return $this->HasOne(TipoPregunta::class, 'id_tipo_pregunta', 'id_tipo_pregunta');
    }

    public function categoria(){
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

}
