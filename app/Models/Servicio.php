<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model{

    protected $table='servicio';
    protected $primaryKey='id_servicio';

    protected $fillable=[
        'nombre_servicio',
        'descripcion',
        'activo'
    ];

    public function reservas(){
        
        return $this->belongsToMany(
            Reserva::class,
            'reserva_servicio',
            'id_servicio',
            'id_reserva'
        );
    }
}