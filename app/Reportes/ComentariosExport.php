<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ComentariosExport implements FromArray, WithTitle
{
    public function title(): string{
        return 'Comentarios';
    }

    public function array(): array{

        $idEncuesta = 3;
        $idEmpresa  = 1;

        $comentarios = DB::table('respuesta')
                        ->join(
                            'pregunta',
                            'pregunta.id_pregunta',
                            '=',
                            'respuesta.id_pregunta'
                        )
                        ->join(
                            'envio_encuesta',
                            'envio_encuesta.id_envio_encuesta',
                            '=',
                            'respuesta.id_envio_encuesta'
                        )
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
                        ->where('pregunta.id_encuesta', $idEncuesta)
                        ->where('pregunta.id_tipo_pregunta', 3)
                        ->where('respuesta.id_empresa', $idEmpresa)
                        ->where('envio_encuesta.id_estado', 2)
                        ->whereNotNull('respuesta.respuesta')
                        ->whereRaw("LTRIM(RTRIM(respuesta.respuesta)) <> ''")
                        ->select(
                            'respuesta.id_respuesta',
                            'respuesta.id_envio_encuesta',
                            'huesped.nombre_huesped',
                            'huesped.apellido_huesped',
                            'pregunta.pregunta',
                            'respuesta.respuesta'
                        )
                        ->orderBy('respuesta.id_respuesta', 'desc')
                        ->get();

        $datos = [
            [
                'ID respuesta',
                'ID envío',
                'Huésped',
                'Pregunta',
                'Comentario'
            ]
        ];

        foreach ($comentarios as $comentario) {

            $nombreHuesped = trim(($comentario->nombre_huesped ?? '') . ' ' . ($comentario->apellido_huesped ?? ''));

            if($nombreHuesped === ''){
                $nombreHuesped = 'Sin huésped asociado';
            }

            $datos[] = [
                $comentario->id_respuesta,
                $comentario->id_envio_encuesta,
                $nombreHuesped,
                $comentario->pregunta ?? '',
                $comentario->respuesta ?? '',
            ];
        }
        return $datos;
    }
}
