<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioEncuesta extends Model
{
    protected $table = 'envio_encuesta';
    protected $primaryKey = 'id_envio_encuesta';

    protected $fillable = [
        'id_agenda'
        ,'nombre_paciente'
        ,'nombre_dentista'
        ,'sucursal'
        ,'email'
        ,'celular'
        ,'id_paciente'
        ,'id_dentista'
        ,'id_empresa'
        ,'id_sucursal'
        ,'id_encuesta'
        ,'id_estado'
        ,'id_tipo_envio'
        ,'fecha_cita'
        ,'hora_cita'
        ,'id_especialidad'
        ,'especialidad'
        ,'rut'
    ];
    public $timestamps = false;
}
