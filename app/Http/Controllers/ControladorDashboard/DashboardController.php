<?php

namespace App\Http\Controllers\ControladorDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Respuesta;
use App\Models\EnvioEncuesta;
use App\Models\Pregunta;
use App\Models\Categoria;

class DashboardController extends Controller{

    public function index(){
        // Configurar encuesta hotel MAE con la data que ncesita 
        $idEncuesta = 3;
        $idEmpresa  = 1;

        // Filtro de período por fecha del dashboard - Por Semana, Por Mes y por Año. Aunque al inicio mostrará toda la información de encuestas
        $periodo = request()->query('periodo', 'todo');

        if(!in_array($periodo, ['todo', 'semanal', 'mensual', 'anual'])){
            $periodo = 'todo';
        }

        //Definir rango de fechas
        $hoy = now();

        switch ($periodo) {
            case 'semanal':
                $fechaInicio = $hoy->copy()->subDays(6)->startOfDay();
                $fechaFin = $hoy->copy()->endOfDay();
                break;

            case 'mensual':
                $fechaInicio = $hoy->copy()->startOfMonth();
                $fechaFin = $hoy->copy()->endOfMonth();
                break;

            case 'anual':
                $fechaInicio = $hoy->copy()->startOfYear();
                $fechaFin = $hoy->copy()->endOfYear();
                break;

            default:
                // Sin fechas: mostrar todo el historial de encuestas
                $fechaInicio = null;
                $fechaFin = null;
                break;
        }

        
        // Encuestas enviadas
        $totalEnviadas = EnvioEncuesta::where('id_encuesta', $idEncuesta)
                            ->where('id_empresa', $idEmpresa)
                            ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                                $query->whereRaw('fecha_envio >= CONVERT(datetime2, ?, 126)',
                                    [$fechaInicio->format('Y-m-d\TH:i:s')]
                                )->whereRaw('fecha_envio < CONVERT(datetime2, ?, 126)',
                                    [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                                );
                            })
                            ->count();

        // Encuestas respondidas
        $totalRespondidas = EnvioEncuesta::where('id_encuesta', $idEncuesta)
                            ->where('id_empresa', $idEmpresa)
                            ->where('id_estado', 2)
                            ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                                $query->whereRaw('fecha_respuesta >= CONVERT(datetime2, ?, 126)',
                                    [$fechaInicio->format('Y-m-d\TH:i:s')]
                                )->whereRaw('fecha_respuesta < CONVERT(datetime2, ?, 126)',
                                    [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                                );
                            })
                            ->count();

        // Encuestas pendientes
        $totalPendientes = EnvioEncuesta::where('id_encuesta', $idEncuesta)
                            ->where('id_empresa', $idEmpresa)
                            ->where('id_estado', 1)
                            ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                                $query->whereRaw('fecha_envio >= CONVERT(datetime2, ?, 126)',
                                    [$fechaInicio->format('Y-m-d\TH:i:s')]
                                )->whereRaw('fecha_envio < CONVERT(datetime2, ?, 126)',
                                    [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                                );
                            })
                            ->count();


        // Porcentaje de respuestas por if ternario
        $porcentajeRespuesta = $totalEnviadas > 0 ? round(($totalRespondidas / $totalEnviadas) * 100, 1) : 0;

        //Resultado por sección
        $respuestasEscala = Respuesta::query()
                            ->join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                            ->join('categoria', 'categoria.id_categoria', '=', 'pregunta.id_categoria')
                            ->join('envio_encuesta', 'envio_encuesta.id_envio_encuesta', '=', 'respuesta.id_envio_encuesta')
                            ->where('pregunta.id_encuesta', $idEncuesta)
                            ->where('pregunta.id_tipo_pregunta', 2)
                            ->where('respuesta.id_empresa', $idEmpresa)
                            ->where('envio_encuesta.id_estado', 2)
                            ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                                $query->whereRaw('envio_encuesta.fecha_respuesta >= CONVERT(datetime2, ?, 126)',
                                    [$fechaInicio->format('Y-m-d\TH:i:s')]
                                )->whereRaw('envio_encuesta.fecha_respuesta < CONVERT(datetime2, ?, 126)',
                                    [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                                );
                            })
                            ->select(
                                'categoria.id_categoria',
                                'categoria.categoria',
                                DB::raw('AVG(CAST(respuesta.respuesta AS FLOAT)) AS promedio')
                            )
                            ->groupBy('categoria.id_categoria','categoria.categoria')
                            ->orderBy('categoria.id_categoria')
                            ->get();

        $satisfaccionGeneral = round($respuestasEscala->avg('promedio'), 2);

        // Ordenar las áreas de mayor a menor satisfacción
        $respuestasEscala = $respuestasEscala
                            ->sortByDesc('promedio')
                            ->values();

        $labelsAreas = $respuestasEscala
                        ->pluck('categoria')
                        ->values();

        $datosAreas = $respuestasEscala
                        ->pluck('promedio')
                        ->map(function ($valor) {
                            return round($valor, 2);
                        })
                        ->values();


        //Detalle Resultados por área
        $detalleAreas = [];

        foreach($respuestasEscala as $area){

            $preguntasArea = Respuesta::query()
                                ->join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                                ->join('envio_encuesta', 'envio_encuesta.id_envio_encuesta', '=', 'respuesta.id_envio_encuesta')
                                ->where('pregunta.id_encuesta', $idEncuesta)
                                ->where('pregunta.id_tipo_pregunta', 2)
                                ->where('pregunta.id_categoria', $area->id_categoria)
                                ->where('respuesta.id_empresa', $idEmpresa)
                                ->where('envio_encuesta.id_estado', 2)
                                ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin){
                                    $query->whereRaw('envio_encuesta.fecha_respuesta >= CONVERT(datetime2, ?, 126)',
                                        [$fechaInicio->format('Y-m-d\TH:i:s')]
                                    )->whereRaw('envio_encuesta.fecha_respuesta < CONVERT(datetime2, ?, 126)',
                                        [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                                    );
                                })
                                ->select(
                                    'pregunta.id_pregunta',
                                    'pregunta.pregunta',
                                    DB::raw('AVG(CAST(respuesta.respuesta AS FLOAT)) AS promedio')
                                )
                                ->groupBy(
                                    'pregunta.id_pregunta',
                                    'pregunta.pregunta'
                                )
                                ->orderBy('promedio', 'desc')
                                ->get();

            $mejorPregunta = $preguntasArea->first();
            $peorPregunta = $preguntasArea->count() > 1 ? $preguntasArea->last() : null;

            $detalleAreas[] = [
                'categoria' => $area->categoria,
                'promedio' => round($area->promedio, 2),

                'mejorPregunta' => $mejorPregunta ? $mejorPregunta->pregunta : null,
                'mejorPreguntaPromedio' => $mejorPregunta ? round($mejorPregunta->promedio, 2) : null,

                'peorPregunta' => $peorPregunta ? $peorPregunta->pregunta : null,
                'peorPreguntaPromedio' => $peorPregunta ? round($peorPregunta->promedio, 2) : null,
            ];
        }
        
        // Nivel de satisfacción del huéspedes por área según el período seleccionado
        $expresionPeriodo = in_array($periodo, ['todo', 'anual'])
                            ? "DATEFROMPARTS(YEAR(envio_encuesta.fecha_respuesta), MONTH(envio_encuesta.fecha_respuesta), 1)"
                            : "CAST(envio_encuesta.fecha_respuesta AS DATE)";

        $respuestasAreasMensuales = Respuesta::query()
                                    ->join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                                    ->join('categoria', 'categoria.id_categoria', '=', 'pregunta.id_categoria')
                                    ->join('envio_encuesta', 'envio_encuesta.id_envio_encuesta', '=', 'respuesta.id_envio_encuesta')
                                    ->where('pregunta.id_encuesta', $idEncuesta)
                                    ->where('pregunta.id_tipo_pregunta', 2)
                                    ->where('respuesta.id_empresa', $idEmpresa)
                                    ->where('envio_encuesta.id_estado', 2)
                                    ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                                        $query->whereRaw('envio_encuesta.fecha_respuesta >= CONVERT(datetime2, ?, 126)',
                                            [$fechaInicio->format('Y-m-d\TH:i:s')]
                                        )->whereRaw('envio_encuesta.fecha_respuesta < CONVERT(datetime2, ?, 126)',
                                            [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                                        );
                                    })
                                    ->whereNotNull('envio_encuesta.fecha_respuesta')
                                    ->select(
                                        'categoria.id_categoria',
                                        'categoria.categoria',
                                        DB::raw("$expresionPeriodo AS fecha_periodo"),
                                        DB::raw('AVG(CAST(respuesta.respuesta AS FLOAT)) AS promedio')
                                    )
                                    ->groupBy(
                                        'categoria.id_categoria',
                                        'categoria.categoria'
                                    )
                                    ->groupByRaw($expresionPeriodo)
                                    ->orderBy('categoria.id_categoria')
                                    ->get();
        
        // Construir las fechas que aparecerán en el gráfico
        $fechasGrafico = [];

        if ($periodo === 'todo'){

            // Obtener el primer y último mes con respuestas históricas
            $primerRegistro = $respuestasAreasMensuales
                ->min('fecha_periodo');

            $ultimoRegistro = $respuestasAreasMensuales
                ->max('fecha_periodo');

            if ($primerRegistro && $ultimoRegistro){

                $cursor = \Carbon\Carbon::parse($primerRegistro)->startOfMonth();
                $limite = \Carbon\Carbon::parse($ultimoRegistro)->startOfMonth();

                while ($cursor <= $limite) {
                    $fechasGrafico[] = [
                        'key' => $cursor->format('Y-m-d'),
                        'label' => $cursor->translatedFormat('M Y'),
                    ];

                    $cursor->addMonth();
                }
            }

        }elseif ($periodo === 'anual'){

            $cursor = $fechaInicio->copy()->startOfMonth();
            $limite = $fechaFin->copy()->startOfMonth();

            while ($cursor <= $limite){
                $fechasGrafico[] = [
                    'key' => $cursor->format('Y-m-d'),
                    'label' => $cursor->translatedFormat('F'),
                ];

                $cursor->addMonth();
            }

        }else{

            $cursor = $fechaInicio->copy()->startOfDay();
            $limite = $fechaFin->copy()->startOfDay();

            while ($cursor <= $limite) {
                $fechasGrafico[] = [
                    'key' => $cursor->format('Y-m-d'),
                    'label' => $cursor->format('d/m'),
                ];

                $cursor->addDay();
            }
        }


        // Etiquetas del gráfico
        $labelsAreasMensuales = collect($fechasGrafico)
            ->pluck('label')
            ->values();

        // Categorías que tendrán una línea en el gráfico
        $categoriasAreas = $respuestasAreasMensuales
            ->sortBy('id_categoria')
            ->pluck('categoria')
            ->unique()
            ->values();

        // Organizar los promedios por categoría y fecha
        $promediosPorCategoria = [];

        foreach ($respuestasAreasMensuales as $registro) {

            $fechaKey = \Carbon\Carbon::parse($registro->fecha_periodo)
                ->format('Y-m-d');

            $promediosPorCategoria[$registro->categoria][$fechaKey] =
                round($registro->promedio, 2);
        }

        // Datos para cada línea del gráfico
        $datosAreasMensuales = [];

        foreach ($categoriasAreas as $categoria) {

            $datosAreasMensuales[$categoria] = [];

            foreach ($fechasGrafico as $fecha) {

                $datosAreasMensuales[$categoria][] =
                    $promediosPorCategoria[$categoria][$fecha['key']] ?? null;
            }
        }


        // Respuestas NPS.. considerar una sola respuesta por envío
        $respuestasNps = Respuesta::query()
                        ->join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                        ->join('envio_encuesta', 'envio_encuesta.id_envio_encuesta', '=', 'respuesta.id_envio_encuesta')
                        ->where('pregunta.id_encuesta', $idEncuesta)
                        ->where('pregunta.id_tipo_pregunta', 1)
                        ->where('respuesta.id_empresa', $idEmpresa)
                        ->where('envio_encuesta.id_estado', 2)
                        ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                            $query->whereRaw('envio_encuesta.fecha_respuesta >= CONVERT(datetime2, ?, 126)',
                                [$fechaInicio->format('Y-m-d\TH:i:s')]
                            )->whereRaw('envio_encuesta.fecha_respuesta < CONVERT(datetime2, ?, 126)',
                                [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                            );
                        })
                        ->select(
                            'respuesta.id_respuesta',
                            'respuesta.id_envio_encuesta',
                            'respuesta.respuesta'
                        )
                        ->orderByDesc('respuesta.id_respuesta')
                        ->get()
                        ->unique('id_envio_encuesta')
                        ->pluck('respuesta')
                        ->values();

        $totalNps = $respuestasNps->count();

        $promotores = $respuestasNps
                    ->filter(fn ($valor) => (int) $valor >= 9)
                    ->count();

        $neutros = $respuestasNps
                    ->filter(fn ($valor) => (int) $valor >= 7 && (int) $valor <= 8)
                    ->count();

        $detractores = $respuestasNps
                    ->filter(fn ($valor) => (int) $valor <= 6)
                    ->count();

        $porcentajePromotores = $totalNps > 0
            ? round(($promotores / $totalNps) * 100, 1)
            : 0;

        $porcentajeNeutros = $totalNps > 0
            ? round(($neutros / $totalNps) * 100, 1)
            : 0;

        $porcentajeDetractores = $totalNps > 0
            ? round(($detractores / $totalNps) * 100, 1)
            : 0;

        // Promedio de recomendación sobre 10
        $npsValor = $totalNps > 0
            ? round($respuestasNps->avg(fn ($valor) => (float) $valor), 1)
            : null;


        // Respueta a comentarios
        $comentarios = Respuesta::query()
                        ->join('pregunta','pregunta.id_pregunta','=','respuesta.id_pregunta')
                        ->join('envio_encuesta','envio_encuesta.id_envio_encuesta','=','respuesta.id_envio_encuesta')
                        ->where('pregunta.id_encuesta', $idEncuesta)
                        ->where('pregunta.id_tipo_pregunta', 3)
                        ->where('respuesta.id_empresa', $idEmpresa)
                        ->where('envio_encuesta.id_estado', 2)
                        ->when($fechaInicio !== null, function ($query) use ($fechaInicio, $fechaFin) {
                            $query->whereRaw('envio_encuesta.fecha_respuesta >= CONVERT(datetime2, ?, 126)',
                                [$fechaInicio->format('Y-m-d\TH:i:s')]
                            )->whereRaw('envio_encuesta.fecha_respuesta < CONVERT(datetime2, ?, 126)',
                                [$fechaFin->copy()->addSecond()->format('Y-m-d\TH:i:s')]
                            );
                        })
                        ->whereNotNull('respuesta.respuesta')
                        ->whereRaw("LTRIM(RTRIM(respuesta.respuesta)) <> ''")
                        ->select(
                            'respuesta.respuesta',
                            'pregunta.pregunta'
                        )
                        ->orderBy('respuesta.id_respuesta', 'desc')
                        ->get();

        // Datos generales
        return view('dashboard.index',[
            //Periodo seleccionado en el reporte
            'periodo'              => $periodo,

            // Participación
            'totalEnviadas'        => $totalEnviadas,
            'totalRespondidas'     => $totalRespondidas,
            'totalPendientes'      => $totalPendientes,
            'porcentajeRespuesta'  => $porcentajeRespuesta,

            // Satisfacción
            'satisfaccionGeneral'  => $satisfaccionGeneral,
            'labelsAreas'          => $labelsAreas,
            'datosAreas'           => $datosAreas,
            'detalleAreas'         => $detalleAreas,

            // Comparación mensual por área
            'labelsAreasMensuales' => $labelsAreasMensuales,
            'datosAreasMensuales'  => $datosAreasMensuales,

            // NPS
            'npsValor'             => $npsValor,
            'promotores'           => $promotores,
            'neutros'              => $neutros,
            'detractores'          => $detractores,
            'porcentajePromotores' => $porcentajePromotores,
            'porcentajeNeutros'    => $porcentajeNeutros,
            'porcentajeDetractores'=> $porcentajeDetractores,

            // Comentarios
            'comentarios' => $comentarios,
        ]);
    }
}
