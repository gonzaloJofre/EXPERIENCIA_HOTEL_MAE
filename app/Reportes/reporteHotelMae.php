<?php

namespace App\Reportes;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteHotelMae implements WithMultipleSheets{

    public function sheets(): array{
        return [
            new ResumenExport(),
            new ParticipantesExport(),
            new RespuestasExport(),
            new ComentariosExport(),
        ];
    }
}