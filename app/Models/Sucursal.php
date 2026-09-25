<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';

    protected $fillable = [
        'id_sucursal',
        'sucursal'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }
}
