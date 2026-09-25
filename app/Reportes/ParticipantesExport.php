<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ParticipantesExport implements FromArray, WithTitle
{
    public function title(): string
    {
        return 'Participantes';
    }

    public function array(): array
    {
        $idEncuesta = 3;
        $idEmpresa  = 1;

        $envios = DB::table('envio_encuesta')
                ->leftJoin(
                    'reserva',
                    'reserva.id_reserva',
                    '=',
                    'envio_encuesta.id_reserva'
                )
                ->leftJoin(
                    'huesped',
                    'huesped.id_huesped',
                    '=',
                    'reserva.id_huesped'
                )
                ->where('envio_encuesta.id_encuesta', $idEncuesta)
                ->where('envio_encuesta.id_empresa', $idEmpresa)
                ->select(
                    'envio_encuesta.id_envio_encuesta',
                    'envio_encuesta.id_estado',
                    'envio_encuesta.id_reserva',
                    'reserva.num_habitacion',
                    'reserva.fecha_ingreso',
                    'reserva.fecha_salida',
                    'huesped.nombre_huesped',
                    'huesped.apellido_huesped',
                    'huesped.correo',
                    'huesped.telefono'
                )
                ->orderBy('envio_encuesta.id_envio_encuesta')
                ->get();

        $datos = [
            [
                'ID envío',
                'Huésped',
                'Correo',
                'Teléfono',
                'Habitación',
                'Reserva',
                'Fecha ingreso',
                'Fecha salida',
                'Servicios',
                'Estado'
            ]
        ];

        foreach($envios as $envio){

            $nombreHuesped = trim(($envio->nombre_huesped ?? '') . ' ' . ($envio->apellido_huesped ?? ''));

            if($nombreHuesped === ''){
                $nombreHuesped = 'Sin huésped asociado';
            }

            $servicios = [];

            if (!empty($envio->id_reserva)){

                $servicios = DB::table('reserva_servicio')
                                ->join(
                                    'servicio',
                                    'servicio.id_servicio',
                                    '=',
                                    'reserva_servicio.id_servicio'
                                )
                                ->where(
                                    'reserva_servicio.id_reserva',
                                    $envio->id_reserva
                                )
                                ->where('servicio.activo', 1)
                                ->orderBy('servicio.id_servicio')
                                ->pluck('servicio.nombre_servicio')
                                ->toArray();
            }

            $serviciosTexto = implode("\n", $servicios);

            $estado = (int) $envio->id_estado === 2 ? 'Respondida' : 'Pendiente';

            $datos[] = [
                $envio->id_envio_encuesta,
                $nombreHuesped,
                $envio->correo ?? '',
                $envio->telefono ?? '',
                $envio->num_habitacion ?? '',
                $envio->id_reserva ?? '',
                $envio->fecha_ingreso ?? '',
                $envio->fecha_salida ?? '',
                $serviciosTexto,
                $estado,
            ];
        }
        return $datos;
    }
}

