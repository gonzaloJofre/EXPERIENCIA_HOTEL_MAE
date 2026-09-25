<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class RespuestasExport implements FromArray, WithTitle
{
    public function title(): string{
        return 'Respuestas';
    }

    public function array(): array{
        $idEncuesta = 3;
        $idEmpresa  = 1;

        $respuestas = DB::table('respuesta')
                        ->join(
                            'pregunta',
                            'pregunta.id_pregunta',
                            '=',
                            'respuesta.id_pregunta'
                        )
                        ->leftJoin(
                            'categoria',
                            'categoria.id_categoria',
                            '=',
                            'pregunta.id_categoria'
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
                        ->leftJoin(
                            'tipo_pregunta',
                            'tipo_pregunta.id_tipo_pregunta',
                            '=',
                            'pregunta.id_tipo_pregunta'
                        )
                        ->where('pregunta.id_encuesta', $idEncuesta)
                        ->where('respuesta.id_empresa', $idEmpresa)
                        ->where('envio_encuesta.id_estado', 2)
                        ->select(
                            'respuesta.id_respuesta',
                            'respuesta.id_envio_encuesta',
                            'huesped.nombre_huesped',
                            'huesped.apellido_huesped',
                            'categoria.categoria',
                            'pregunta.pregunta',
                            'tipo_pregunta.tipo_pregunta',
                            'respuesta.respuesta'
                        )
                        ->orderBy('respuesta.id_envio_encuesta')
                        ->orderBy('respuesta.id_respuesta')
                        ->get();

        $datos = [
            [
                'ID respuesta',
                'ID envío',
                'Huésped',
                'Categoría',
                'Pregunta',
                'Tipo de pregunta',
                'Respuesta'
            ]
        ];

        foreach ($respuestas as $respuesta) {

            $nombreHuesped = trim(($respuesta->nombre_huesped ?? '') . ' ' . ($respuesta->apellido_huesped ?? ''));

            if($nombreHuesped === ''){
                $nombreHuesped = 'Sin huésped asociado';
            }

            $datos[] = [
                $respuesta->id_respuesta,
                $respuesta->id_envio_encuesta,
                $nombreHuesped,
                $respuesta->categoria ?? '',
                $respuesta->pregunta ?? '',
                $respuesta->tipo_pregunta ?? '',
                $respuesta->respuesta ?? '',
            ];
        }
        return $datos;
    }
}