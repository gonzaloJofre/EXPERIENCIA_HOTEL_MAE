<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorTraerDatos\TraerDatosController;

Route::post('/hotel/reserva', [TraerDatosController::class,'recibirReserva']);
