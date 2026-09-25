<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPregunta extends Model
{
    protected $table = 'tipo_pregunta';
    protected $primaryKey = 'id_tipo_pregunta';

    protected $fillable = [
        'id_tipo_pregunta',
        'tipo_pregunta',
        'etiqueta'
    ];

    public function preguntas()
    {
        return $this->BelongsTo(Pregunta::class, 'id_tipo_pregunta', 'id_tipo_pregunta');
    }
}
