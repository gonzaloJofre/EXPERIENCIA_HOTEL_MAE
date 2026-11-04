<?php

namespace App\Reportes;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteHotelMae implements WithMultipleSheets{

    private string $periodo;

    public function __construct(string $periodo = 'todo'){
        $periodosPermitidos = [
            'todo',
            'semanal',
            'mensual',
            'anual',
        ];

        $this->periodo = in_array($periodo, $periodosPermitidos, true)
            ? $periodo
            : 'todo';
    }

    public function sheets(): array{
        return [
            new ResumenExport($this->periodo),
            new ParticipantesExport($this->periodo),
            new RespuestasExport($this->periodo),
            new ComentariosExport($this->periodo),
        ];
    }
}