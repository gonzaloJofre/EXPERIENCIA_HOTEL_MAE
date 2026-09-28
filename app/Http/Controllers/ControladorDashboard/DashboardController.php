<?php

namespace App\Http\Controllers\ControladorDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Respuesta;
use App\Models\Pregunta;
use App\Models\Categoria;

class DashboardController extends Controller{

    public function index(){
        // Configurar encuesta hotel MAE con la data que ncesita 
        $idEncuesta = 3;
        $idEmpresa  = 1;

        // Encuestas enviadas
        $totalEnviadas = DB::table('envio_encuesta')
                            ->where('id_encuesta', $idEncuesta)
                            ->where('id_empresa', $idEmpresa)
                            ->count();

        // Total encuestas respondidas
        $totalRespondidas = DB::table('envio_encuesta')
                            ->where('id_encuesta', $idEncuesta)
                            ->where('id_empresa', $idEmpresa)
                            ->where('id_estado', 2)
                            ->count();

        // Encuestas que se respondieron
        $totalPendientes = DB::table('envio_encuesta')
                            ->where('id_encuesta', $idEncuesta)
                            ->where('id_empresa', $idEmpresa)
                            ->where('id_estado', 1)
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

        // Comparación mensual de satisfacción por área
        $respuestasAreasMensuales = Respuesta::query()
                            ->join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                            ->join('categoria', 'categoria.id_categoria', '=', 'pregunta.id_categoria')
                            ->join('envio_encuesta', 'envio_encuesta.id_envio_encuesta', '=', 'respuesta.id_envio_encuesta')
                            ->where('pregunta.id_encuesta', $idEncuesta)
                            ->where('pregunta.id_tipo_pregunta', 2)
                            ->where('respuesta.id_empresa', $idEmpresa)
                            ->where('envio_encuesta.id_estado', 2)
                            ->whereNotNull('envio_encuesta.fecha_respuesta')
                            ->whereYear('envio_encuesta.fecha_respuesta', now()->year)
                            ->select(
                                'categoria.id_categoria',
                                'categoria.categoria',
                                DB::raw("YEAR(envio_encuesta.fecha_respuesta) AS anio"),
                                DB::raw("MONTH(envio_encuesta.fecha_respuesta) AS mes"),
                                DB::raw('AVG(CAST(respuesta.respuesta AS FLOAT)) AS promedio')
                            )
                            ->groupBy(
                                'categoria.id_categoria',
                                'categoria.categoria',
                                DB::raw("YEAR(envio_encuesta.fecha_respuesta)"),
                                DB::raw("MONTH(envio_encuesta.fecha_respuesta)")
                            )
                            ->orderBy('anio')
                            ->orderBy('mes')
                            ->get();


        // MESES DEL GRÁFICO
        $anioActual = now()->year;

        // Todos los meses del año
        $mesesAreas = collect(range(1, 12))
                        ->map(function ($mes) use ($anioActual) {
                            return sprintf('%04d-%02d', $anioActual, $mes);
                        });

        // Nombre meses
        $nombresMeses = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        // Etiquetas que aparecerán en el gráfico
        $labelsAreasMensuales = collect(range(1, 12))
                                ->map(function ($mes) use ($nombresMeses) {

                                    return $nombresMeses[$mes];
                                })
                                ->values();

        // Obtener las áreas que tendrán una línea en el gráfico
        $categoriasAreas = $respuestasAreasMensuales
                            ->sortBy('id_categoria')
                            ->pluck('categoria')
                            ->unique()
                            ->values();


        // Datos para cada línea del gráfico
        $datosAreasMensuales = [];

        foreach ($categoriasAreas as $categoria) {

            $datosAreasMensuales[$categoria] = [];

            foreach ($mesesAreas as $mes) {

                $registro = $respuestasAreasMensuales
                            ->first(function ($item) use ($categoria, $mes) {

                                $mesRegistro = sprintf(
                                    '%04d-%02d',
                                    $item->anio,
                                    $item->mes
                                );

                                return $item->categoria === $categoria
                                    && $mesRegistro === $mes;
                            });

                $datosAreasMensuales[$categoria][] = $registro
                    ? round($registro->promedio, 2)
                    : null;
            }
        }

        // Respuestas NPS
        $respuestasNps = Respuesta::query()
                            ->join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                            ->join('envio_encuesta','envio_encuesta.id_envio_encuesta','=','respuesta.id_envio_encuesta')
                            ->where('pregunta.id_encuesta', $idEncuesta)
                            ->where('pregunta.id_tipo_pregunta', 1)
                            ->where('respuesta.id_empresa', $idEmpresa)
                            ->where('envio_encuesta.id_estado', 2)
                            ->pluck('respuesta.respuesta');

        $totalNps = $respuestasNps->count();//Conteo

        $promotores = $respuestasNps
                    ->filter(function ($valor) {
                        return (int) $valor >= 9;
                    })
                    ->count();

        $neutros = $respuestasNps
                    ->filter(function ($valor) {
                        return (int) $valor >= 7 && (int) $valor <= 8;
                    })
                    ->count();

        $detractores = $respuestasNps
                    ->filter(function ($valor) {
                        return (int) $valor <= 6;
                    })
                    ->count();

        $porcentajePromotores = $totalNps > 0 ? round(($promotores / $totalNps) * 100, 1) : 0;
        $porcentajeNeutros = $totalNps > 0 ? round(($neutros / $totalNps) * 100, 1) : 0;
        $porcentajeDetractores = $totalNps > 0 ? round(($detractores / $totalNps) * 100, 1) : 0;
        $npsValor = $totalNps > 0 ? round($porcentajePromotores - $porcentajeDetractores) : null;

        // Respueta a comentarios
        $comentarios = Respuesta::query()
                            ->join('pregunta','pregunta.id_pregunta','=','respuesta.id_pregunta')
                            ->join('envio_encuesta','envio_encuesta.id_envio_encuesta','=','respuesta.id_envio_encuesta')
                            ->where('pregunta.id_encuesta', $idEncuesta)
                            ->where('pregunta.id_tipo_pregunta', 3)
                            ->where('respuesta.id_empresa', $idEmpresa)
                            ->where('envio_encuesta.id_estado', 2)
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
