<?php

namespace App\Reportes;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResumenExport implements FromArray, WithTitle
{
    public function title(): string{
        return 'Resumen';
    }

    public function array(): array{

        $idEncuesta = 3;
        $idEmpresa  = 1;

        $totalEnviadas = DB::table('envio_encuesta')
                        ->where('id_encuesta', $idEncuesta)
                        ->where('id_empresa', $idEmpresa)
                        ->count();

                    $totalRespondidas = DB::table('envio_encuesta')
                        ->where('id_encuesta', $idEncuesta)
                        ->where('id_empresa', $idEmpresa)
                        ->where('id_estado', 2)
                        ->count();

                    $totalPendientes = DB::table('envio_encuesta')
                        ->where('id_encuesta', $idEncuesta)
                        ->where('id_empresa', $idEmpresa)
                        ->where('id_estado', 1)
                        ->count();

        $porcentajeRespuesta = $totalEnviadas > 0 ? round(($totalRespondidas / $totalEnviadas) * 100, 1) : 0;

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
