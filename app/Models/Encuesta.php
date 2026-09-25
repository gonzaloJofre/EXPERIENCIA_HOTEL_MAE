<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuesta';
    protected $primaryKey = 'id_encuesta';

    protected $fillable = [
        'id_encuesta',
        'encuesta'
    ];

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class, 'id_encuesta', 'id_encuesta');
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'encuesta_empresa', 'id_encuesta', 'id_empresa');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,'id_usuario', 'id_usuario');
    }
}
