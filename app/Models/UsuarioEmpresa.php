<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioEmpresa extends Model
{
    protected $table = 'usuario_empresa';
    protected $fillable = [
        'id_usuario',
        'id_empresa'
    ];
}
