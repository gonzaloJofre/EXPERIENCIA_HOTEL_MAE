<?php

namespace App\Http\Controllers\ControladorTraerDatos;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\ReservaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraerDatosController extends Controller{
    
    //Simula una reserva que se realiza en ekl hotel 
    public function recibirReserva(Request $request){

        //Validación para los datos que van llegando
        $request->validate([
            'id_reserva' => 'required|integer|exists:reserva,id_reserva',
            'servicios' => 'required|array',
            'servicios.*' => 'integer|exists:servicio,id_servicio',
        ]);

        //Busca la reserva
        $reserva = Reserva::find($request->id_reserva);

        //Desde aquí empieza la transacción desde la bbdd para simular la API 
        DB::beginTransaction();
        try {
            //Validar que no se repitan los mismos servicios para una reserva / En la bbdd también se valida con Unique
            foreach ($request->servicios as $idServicio){
                ReservaServicio::firstOrCreate([
                    'id_reserva' => $reserva->id_reserva,
                    'id_servicio' => $idServicio
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'Reserva recibida correctamente.',
                'id_reserva' => $reserva->id_reserva,
                'servicios' => $request->servicios
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'mensaje' => 'No fue posible registrar la reserva.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}