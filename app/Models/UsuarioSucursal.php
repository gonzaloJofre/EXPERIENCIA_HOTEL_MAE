<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioSucursal extends Model
{
    protected $table = 'usuario_sucursal';
    protected $fillable = [
        'id_usuario',
        'id_sucursal'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public static function sucursalesUsuario($idUsuario, $idEmpresa)
    {
        return self::where('id_usuario', $idUsuario)
            ->whereHas('sucursal', function ($q) use ($idEmpresa) {
                $q->where('id_empresa', $idEmpresa);
            })
            ->with('sucursal')
            ->get()
            ->pluck('sucursal');
    }

}
