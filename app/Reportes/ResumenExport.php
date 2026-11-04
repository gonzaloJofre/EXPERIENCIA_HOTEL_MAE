<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResumenExport implements FromArray, WithTitle
{
    private string $periodo;

    public function __construct(string $periodo = 'todo')
    {
        $this->periodo = $periodo;
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function array(): array
    {
        $idEncuesta = 3;
        $idEmpresa  = 1;

        // Encuestas enviadas: se utiliza fecha_envio.
        $queryEnviadas = DB::table('envio_encuesta')
            ->where('id_encuesta', $idEncuesta)
            ->where('id_empresa', $idEmpresa);

        FiltroPeriodoReporte::aplicar(
            $queryEnviadas,
            $this->periodo,
            'envio_encuesta.fecha_envio'
        );

        $totalEnviadas = $queryEnviadas->count();

        // Encuestas respondidas: se utiliza fecha_respuesta.
        $queryRespondidas = DB::table('envio_encuesta')
            ->where('id_encuesta', $idEncuesta)
            ->where('id_empresa', $idEmpresa)
            ->where('id_estado', 2);

        FiltroPeriodoReporte::aplicar(
            $queryRespondidas,
            $this->periodo,
            'envio_encuesta.fecha_respuesta'
        );

        $totalRespondidas = $queryRespondidas->count();

        // Encuestas pendientes: se utiliza fecha_envio.
        $queryPendientes = DB::table('envio_encuesta')
            ->where('id_encuesta', $idEncuesta)
            ->where('id_empresa', $idEmpresa)
            ->where('id_estado', 1);

        FiltroPeriodoReporte::aplicar(
            $queryPendientes,
            $this->periodo,
            'envio_encuesta.fecha_envio'
        );

        $totalPendientes = $queryPendientes->count();

        $porcentajeRespuesta = $totalEnviadas > 0
            ? round(($totalRespondidas / $totalEnviadas) * 100, 1)
            : 0;

        return [
            ['REPORTE HOTEL MAE'],
            ['Resumen general de encuestas'],
            [],
            ['Indicador', 'Valor'],
            ['Encuestas enviadas', $totalEnviadas],
            ['Encuestas respondidas', $totalRespondidas],
            ['Encuestas pendientes', $totalPendientes],
            ['Tasa de respuesta', $porcentajeRespuesta . '%'],
        ];
    }
}
