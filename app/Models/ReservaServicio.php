<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReservaServicio extends Model{

    protected $table='reserva_servicio';
    protected $primaryKey='id_reserva_servicio';
    public $timestamps=false;

    protected $fillable=[
        'id_reserva',
        'id_servicio'
    ];

    //Relaciones
    public function reserva(){
        return $this->belongsTo(Reserva::class,'id_reserva');
    }

    public function servicio(){
        return $this->belongsTo(Servicio::class,'id_servicio');
    }
} 