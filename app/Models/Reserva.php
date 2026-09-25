<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model{
    
    protected $table = 'reserva';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_huesped',
        'id_sucursal',
        'num_habitacion',
        'fecha_ingreso',
        'fecha_salida',
        'id_estado_reserva'
    ];

    //Relaciones
    public function huesped(){
        return $this->belongsTo(Huesped::class, 'id_huesped');
    }

    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function servicios(){
        return $this->belongsToMany(Servicio::class, 'reserva_servicio', 'id_reserva', 'id_servicio');
    }

    public function reservaServicios(){
        return $this->hasMany(ReservaServicio::class, 'id_reserva');
    }
}