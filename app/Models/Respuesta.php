<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $table = 'respuesta';
    protected $primaryKey = 'id_respuesta';
    public $timestamps = false;

    protected $fillable = [
        'respuesta',
        'id_pregunta',
        'id_envio_encuesta',
        'id_empresa'
    ];

    public function preguntas()
    {
        return $this->BelongsTo(Pregunta::class, 'id_pregunta', 'id_pregunta');
    }

}
