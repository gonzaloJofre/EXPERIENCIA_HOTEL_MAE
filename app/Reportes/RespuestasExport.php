<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class RespuestasExport implements FromArray, WithTitle
{
    public function title(): string
    {
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
                'Huésped',
                'Categoría',
                'Pregunta',
                'Respuesta'
            ]
        ];

        foreach($respuestas as $respuesta){

            $nombreHuesped = trim(($respuesta->nombre_huesped ?? '') . ' ' . ($respuesta->apellido_huesped ?? ''));

            if($nombreHuesped === ''){
                $nombreHuesped = 'No identificado';
            }

            $respuestaTexto = trim((string) ($respuesta->respuesta ?? ''));

            $tipoPregunta = trim((string) ($respuesta->tipo_pregunta ?? ''));
            $preguntaTexto = trim((string) ($respuesta->pregunta ?? ''));

            if($tipoPregunta === 'Checkbox'){

                $escalaCheckbox = [
                    1 => 'Muy insatisfecho',
                    2 => 'Insatisfecho',
                    3 => 'Neutro',
                    4 => 'Satisfecho',
                    5 => 'Muy satisfecho',
                ];

                $valor = (int) $respuestaTexto;

                if (isset($escalaCheckbox[$valor])){
                    $respuestaTexto = $escalaCheckbox[$valor];
                }
            }
            elseif (strcasecmp($tipoPregunta, 'Si/No') === 0 || strcasecmp($tipoPregunta, 'Sí/No') === 0){

                $valor = strtolower($respuestaTexto);

                if ($valor === '1' || $valor === 'si' || $valor === 'sí') {
                    $respuestaTexto = 'Sí';
                } elseif ($valor === '0' || $valor === 'no') {
                    $respuestaTexto = 'No';
                }
            }
            elseif (str_contains(strtolower($tipoPregunta), 'multiple') || str_contains(strtolower($tipoPregunta), 'múltiple')){

                $valor = $respuestaTexto;
                $json = json_decode($valor, true);

                if(json_last_error() === JSON_ERROR_NONE && is_array($json)){

                    $elementos = [];

                    foreach ($json as $elemento){

                        if (is_array($elemento)){
                            $elementos[] = implode(', ', $elemento);
                        } else {
                            $elementos[] = (string) $elemento;
                        }
                    }

                    $respuestaTexto = implode(', ', $elementos);
                }
            }

            if($respuestaTexto === ''){
                $respuestaTexto = 'Sin respuesta';
            }

            $datos[] = [
                $nombreHuesped,
                $respuesta->categoria ?? '',
                $preguntaTexto,
                $respuestaTexto,
            ];
        }
        return $datos;
    }
}
