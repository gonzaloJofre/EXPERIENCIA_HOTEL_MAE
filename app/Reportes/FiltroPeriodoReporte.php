<?php

namespace App\Reportes;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

class FiltroPeriodoReporte{

    public static function aplicar(Builder $query, string $periodo, string $columnaFecha): Builder {

        // Evitar que se utilicen columnas no autorizadas y validar que así sea
        $columnasPermitidas = [
            'envio_encuesta.fecha_envio',
            'envio_encuesta.fecha_respuesta',
        ];

        if(!in_array($columnaFecha, $columnasPermitidas, true)){
            throw new \InvalidArgumentException('Columna de fecha no permitida.');
        }

        $hoy = now();

        switch ($periodo) {

            case 'semanal':
                $fechaInicio = $hoy->copy()->subDays(6)->startOfDay();
                $fechaFin = $hoy->copy()->addDay()->startOfDay();
                break;

            case 'mensual':
                $fechaInicio = $hoy->copy()->startOfMonth();
                $fechaFin = $hoy->copy()->addMonth()->startOfMonth();
                break;

            case 'anual':
                $fechaInicio = $hoy->copy()->startOfYear();
                $fechaFin = $hoy->copy()->addYear()->startOfYear();
                break;

            default:
                return $query;
        }

        return $query
            ->whereRaw(
                "$columnaFecha >= CONVERT(datetime2, ?, 126)",
                [$fechaInicio->format('Y-m-d\TH:i:s')]
            )
            ->whereRaw(
                "$columnaFecha < CONVERT(datetime2, ?, 126)",
                [$fechaFin->format('Y-m-d\TH:i:s')]
            );
    }
}