<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ComentariosExport implements FromArray, WithTitle
{
    private string $periodo;

    public function __construct(string $periodo = 'todo')
    {
        $this->periodo = $periodo;
    }

    public function title(): string
    {
        return 'Comentarios';
    }

    public function array(): array
    {
        $idEncuesta = 3;
        $idEmpresa  = 1;

        $queryComentarios = DB::table('respuesta')
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
            ->whereRaw("LTRIM(RTRIM(respuesta.respuesta)) <> ''");

        FiltroPeriodoReporte::aplicar(
            $queryComentarios,
            $this->periodo,
            'envio_encuesta.fecha_respuesta'
        );

        $comentarios = $queryComentarios
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
                'Huésped',
                'Pregunta',
                'Comentario'
            ]
        ];

        foreach ($comentarios as $comentario) {

            $nombreHuesped = trim(
                ($comentario->nombre_huesped ?? '') . ' ' .
                ($comentario->apellido_huesped ?? '')
            );

            if ($nombreHuesped === '') {
                $nombreHuesped = 'Sin huésped asociado';
            }

            $datos[] = [
                $nombreHuesped,
                $comentario->pregunta ?? '',
                $comentario->respuesta ?? '',
            ];
        }

        return $datos;
    }
}
