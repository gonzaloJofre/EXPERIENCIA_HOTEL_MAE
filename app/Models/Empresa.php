<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'id_empresa';

    protected $fillable = [
        'id_empresa',
        'empresa'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_empresa', 'id_empresa', 'id_usuario');
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }

    public function encuestas()
    {
        return $this->belongsToMany(Encuesta::class, 'encuesta_empresa', 'id_empresa', 'id_encuesta');
    }
}
