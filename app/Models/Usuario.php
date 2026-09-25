<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\Usuario;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombres',
        'ape_paterno', 
        'ape_materno',
        'email',
        'contrasena',
        'id_rol'
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mapea el campo de contraseña personalizado
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'usuario_empresa', 'id_usuario', 'id_empresa');
    }

    public function encuestas()
    {
        return $this->hasMany(Encuesta::class,'id_usuario', 'id_usuario');
    }

    /**
     * Mutador para hashear la contraseña automáticamente
     */
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }
}