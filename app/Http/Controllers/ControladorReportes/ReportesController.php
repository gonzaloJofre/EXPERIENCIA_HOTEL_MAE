<?php

namespace App\Http\Controllers\ControladorReportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use App\Models\EnvioEncuesta;
use App\Models\UsuarioSucursal;


class ReportesController extends Controller
{
    public function nps_empresa_anual(Request $request) {
        $id_sucursal = $request->id_sucursal;
        $resultados = DB::table('envio_encuesta as e')
                        ->join('respuesta as r', 'r.id_envio_encuesta', '=', 'e.id_envio_encuesta')
                        ->selectRaw("
                            MONTH(r.fecha_respuesta) AS mes,
                            SUM(CASE WHEN CAST(r.respuesta AS INT) <= 6 THEN 1 ELSE 0 END) AS Detractores,
                            SUM(CASE WHEN CAST(r.respuesta AS INT) >= 9 THEN 1 ELSE 0 END) AS Promotores,
                            SUM(CASE WHEN CAST(r.respuesta AS INT) BETWEEN 7 AND 8 THEN 1 ELSE 0 END) AS Neutros
                        ")
                        ->where('r.id_pregunta', 1)
                        ->where('e.id_sucursal', $id_sucursal)
                        ->whereYear('r.fecha_respuesta', now()->year)
                        ->groupBy(DB::raw('MONTH(r.fecha_respuesta)'))
                        ->orderBy(DB::raw('MONTH(r.fecha_respuesta)'))
                        ->get()
                        ->keyBy('mes')
                        ->toArray();

                    $meses = range(1, 12);
                    $promotores = [];
                    $neutros = [];
                    $detractores = [];

                    foreach ($meses as $mes) {
                        $promotores[]  = isset($resultados[$mes]) ? (int) $resultados[$mes]->Promotores : 0;
                        $neutros[]     = isset($resultados[$mes]) ? (int) $resultados[$mes]->Neutros : 0;
                        $detractores[] = isset($resultados[$mes]) ? (int) $resultados[$mes]->Detractores : 0;
                    }


                    $series = [
                        ['name' => 'Promotores',  'data' => $promotores,  'color' => '#23f788'],
                        ['name' => 'Neutros',     'data' => $neutros,     'color' => '#f5ca23'],
                        ['name' => 'Detractores', 'data' => $detractores, 'color' => '#f52324'],
                    ];

        return $series;
    } 

    public function resumen_general_generales(Request $request) {
        $user = Auth::user();
        $year = now()->year;
        $month = $request->mes;
        $idEmpresa = Session::get('empresa_seleccionada_id');
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        // Obtener sucursales del usuario dentro de su empresa
        if($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal');
        } else {
            $sucursales_ids = [$request->sucursal];
        }
        

        // dd($sucursales_ids);

        // Cantidad de encuestas respondidas
        $total_encuestas_respondidas = DB::table('respuesta')
            ->join('envio_encuesta', 'respuesta.id_envio_encuesta', '=', 'envio_encuesta.id_envio_encuesta')
            ->whereYear('fecha_respuesta', $year)
            ->whereMonth('fecha_respuesta', $month)
            ->whereIn('envio_encuesta.id_sucursal', $sucursales_ids)
            ->distinct('respuesta.id_envio_encuesta')
            ->count('respuesta.id_envio_encuesta');

        // Cantidad de comentarios (id_pregunta = 12)
        $total_cantidad_comentarios = DB::table('respuesta')
            ->join('envio_encuesta', 'respuesta.id_envio_encuesta', '=', 'envio_encuesta.id_envio_encuesta')
            ->where('id_pregunta', 14)
            ->whereYear('fecha_respuesta', $year)
            ->whereMonth('fecha_respuesta', $month)
            ->whereIn('envio_encuesta.id_sucursal', $sucursales_ids)
            ->count('id_pregunta');

        // IBB / NPS
        $nps_query = DB::table('respuesta')
            ->join('envio_encuesta', 'respuesta.id_envio_encuesta', '=', 'envio_encuesta.id_envio_encuesta')
            ->selectRaw("
                CAST(
                    (CAST(SUM(CASE WHEN CAST(respuesta AS INT) BETWEEN 9 AND 10 THEN 1 ELSE 0 END) AS FLOAT)
                    - CAST(SUM(CASE WHEN CAST(respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END) AS FLOAT)
                    ) / COUNT(*) * 100 AS DECIMAL(5,2)
                ) AS NPS
            ")
            ->where('id_pregunta', 1)
            ->whereYear('fecha_respuesta', $year)
            ->whereMonth('fecha_respuesta', $month)
            ->whereIn('envio_encuesta.id_sucursal', $sucursales_ids)
            ->first();

        $nps = $nps_query->NPS ?? 0;

        // Porcentaje de detractores
        $porcentaje_detractores_query = DB::table('respuesta')
            ->join('envio_encuesta', 'respuesta.id_envio_encuesta', '=', 'envio_encuesta.id_envio_encuesta')
            ->selectRaw("
                CAST(
                    SUM(CASE WHEN CAST(respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END) * 100.0 
                    / COUNT(*) AS DECIMAL(5,2)
                ) AS PorcentajeDetractores
            ")
            ->where('id_pregunta', 1)
            ->whereYear('fecha_respuesta', $year)
            ->whereMonth('fecha_respuesta', $month)
            ->whereIn('envio_encuesta.id_sucursal', $sucursales_ids)
            ->first();

        $porcentaje_detractores = $porcentaje_detractores_query->PorcentajeDetractores ?? 0;

        // Devolver todo en JSON
        return response()->json([
            'total_encuestas_respondidas' => $total_encuestas_respondidas,
            'total_cantidad_comentarios' => $total_cantidad_comentarios,
            'NPS' => $nps,
            'porcentaje_detractores' => $porcentaje_detractores,
        ]);
    }


    public function evolucion_detractores_promotores(Request $request) {
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');

        // Obtener las sucursales del usuario dentro de la empresa seleccionada
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal');
        } else {
            $sucursales_ids = [$request->sucursal];
        }

        // Consultar evolución de detractores y promotores filtrando por sucursales
        $resultados = DB::table('respuesta as r')
            ->join('envio_encuesta as e', 'r.id_envio_encuesta', '=', 'e.id_envio_encuesta')
            ->selectRaw("
                FORMAT(r.fecha_respuesta, 'MM/yy') as mes,
                SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 9 AND 10 THEN 1 ELSE 0 END) as Promotores,
                -SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END) as Detractores,
                CAST( (
                    (CAST(SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 9 AND 10 THEN 1 ELSE 0 END) AS FLOAT)
                    - CAST(SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END) AS FLOAT))
                    / CAST(COUNT(*) AS FLOAT) * 100
                ) AS DECIMAL(6,2)) as NPS
            ")
            ->where('r.id_pregunta', 1)
            ->whereYear('r.fecha_respuesta', date('Y'))
            ->whereIn('e.id_sucursal', $sucursales_ids)
            ->groupByRaw("FORMAT(r.fecha_respuesta, 'MM/yy')")
            ->orderByRaw("MIN(r.fecha_respuesta)")
            ->get();

        // Convertir a enteros/float para el gráfico
        $categories  = $resultados->pluck('mes');
        $promotores  = $resultados->pluck('Promotores')->map(fn($v) => (int) $v);
        $detractores = $resultados->pluck('Detractores')->map(fn($v) => (int) $v);
        $nps         = $resultados->pluck('NPS')->map(fn($v) => (float) $v);

        return response()->json([
            'categories'  => $categories,
            'promotores'  => $promotores,
            'detractores' => $detractores,
            'nps'         => $nps,
        ]);
    }

    public function distribucion_respuestas_nps(Request $request) {
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');
        $mes = $request->mes;

        // Obtener las sucursales del usuario dentro de la empresa seleccionada
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal');
        } else {
            $sucursales_ids = [$request->sucursal];
        }

        // Query con filtro de sucursales
        $resultados = DB::table('respuesta as r')
            ->join('envio_encuesta as e', 'r.id_envio_encuesta', '=', 'e.id_envio_encuesta')
            ->selectRaw("
                TRY_CAST(r.respuesta AS INT) as valor,
                COUNT(*) as cantidad,
                CAST(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER() AS DECIMAL(5,2)) as porcentaje
            ")
            ->where('r.id_pregunta', 1)
            ->whereYear('r.fecha_respuesta', now()->year)
            ->whereMonth('r.fecha_respuesta', $mes)
            ->whereRaw('ISNUMERIC(r.respuesta) = 1')
            ->whereIn('e.id_sucursal', $sucursales_ids)
            ->groupBy(DB::raw('TRY_CAST(r.respuesta AS INT)'))
            ->orderBy(DB::raw('TRY_CAST(r.respuesta AS INT)'), 'desc')
            ->get();

        return response()->json([
            'resultados' => $resultados
        ]);
    }

    public function evolucion_ibb_sucursales(Request $request) {
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');

        // Obtener sucursales del usuario dentro de la empresa seleccionada
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal')->toArray();
        } else {
            $sucursales_ids = [(int) $request->sucursal];
        }

        // Si el usuario no tiene sucursales, devolver array vacío (evita error SQL)
        if (empty($sucursales_ids)) {
            return response()->json([]);
        }

        $data = DB::table('respuesta as r')
            ->join('envio_encuesta as e', 'r.id_envio_encuesta', '=', 'e.id_envio_encuesta')
            ->join('sucursal as s', 'e.id_sucursal', '=', 's.id_sucursal')
            ->selectRaw("
                FORMAT(r.fecha_respuesta, 'MM/yy') as periodo,
                s.sucursal,
                CAST(SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 9 AND 10 THEN 1 ELSE 0 END) AS INT) as promotores,
                CAST(SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END) AS INT) as detractores,
                CAST(
                    (SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 9 AND 10 THEN 1 ELSE 0 END) 
                    - SUM(CASE WHEN TRY_CAST(r.respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END)) 
                AS INT) as IBB
            ")
            ->where('r.id_pregunta', 1)
            ->whereIn('e.id_sucursal', $sucursales_ids)
            ->groupBy(DB::raw("FORMAT(r.fecha_respuesta, 'MM/yy'), s.sucursal"))
            ->orderBy(DB::raw("MIN(r.fecha_respuesta)"))
            ->orderBy('s.sucursal')
            ->get()
            ->map(function ($item) {
                $item->promotores = (int) $item->promotores;
                $item->detractores = (int) $item->detractores;
                $item->IBB = (int) $item->IBB;
                return $item;
            });

        return response()->json($data);
    }


    public function ibb_dentistas(Request $request) {
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');

        // Obtener sucursales asociadas al usuario
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal')->toArray();
        } else {
            $sucursales_ids = [(int) $request->sucursal];
        }

        // Evitar errores si el usuario no tiene sucursales
        if (empty($sucursales_ids)) {
            return response()->json([]);
        }

        // Convertir a string para usar en SQL
        $ids_str = implode(',', $sucursales_ids);

        $sql = "
            WITH datos AS (
                SELECT 
                    e.id_dentista,
                    e.nombre_dentista,
                    e.id_especialidad,
                    e.especialidad,
                    e.sucursal AS centro,
                    e.id_envio_encuesta,
                    r.id_pregunta,
                    e.id_sucursal,
                    p.pregunta,
                    TRY_CAST(r.respuesta AS FLOAT) AS respuesta,
                    r.respuesta AS respuesta_texto,
                    CASE WHEN r.id_pregunta = 1 AND TRY_CAST(r.respuesta AS INT) BETWEEN 9 AND 10 THEN 1 ELSE 0 END AS promotor,
                    CASE WHEN r.id_pregunta = 1 AND TRY_CAST(r.respuesta AS INT) BETWEEN 0 AND 6 THEN 1 ELSE 0 END AS detractor
                FROM envio_encuesta e
                INNER JOIN respuesta r ON r.id_envio_encuesta = e.id_envio_encuesta
                INNER JOIN pregunta p ON p.id_pregunta = r.id_pregunta
                WHERE (p.id_categoria = 5 OR r.id_pregunta IN (1,14))
                AND e.id_sucursal IN ($ids_str)
            ),
            resumen AS (
                SELECT
                    id_dentista,
                    nombre_dentista,
                    id_especialidad,
                    especialidad,
                    centro,
                    id_sucursal,
                    COUNT(DISTINCT id_envio_encuesta) AS cantidad_respuesta,
                    COUNT(DISTINCT CASE WHEN id_pregunta = 14 AND respuesta_texto IS NOT NULL AND respuesta_texto <> '' THEN id_envio_encuesta END) AS cantidad_comentarios,
                    CAST(
                        (SUM(promotor) - SUM(detractor)) * 100.0 / NULLIF(SUM(CASE WHEN id_pregunta = 1 THEN 1 END), 0)
                        AS DECIMAL(6,2)
                    ) AS IBB,
                    id_pregunta,
                    pregunta,
                    AVG(respuesta) AS promedio_respuesta
                FROM datos
                GROUP BY
                    id_dentista,
                    nombre_dentista,
                    id_especialidad,
                    especialidad,
                    centro,
                    id_sucursal,
                    id_pregunta,
                    pregunta
            )
            SELECT
                r.id_dentista,
                r.nombre_dentista AS doctor,
                r.centro,
                r.especialidad,
                r.id_sucursal,
                MAX(r.cantidad_respuesta) AS cantidad_respuesta,
                MAX(r.cantidad_comentarios) AS cantidad_comentarios,
                MAX(CASE WHEN r.id_pregunta = 7  THEN r.promedio_respuesta END) AS satisfaccion_tiempo_espera,
                MAX(CASE WHEN r.id_pregunta = 8  THEN r.promedio_respuesta END) AS claridad_explicacion,
                MAX(CASE WHEN r.id_pregunta = 9 THEN r.promedio_respuesta END) AS delicadeza_procedimiento,
                MAX(r.IBB) AS IBB
            FROM resumen r
            GROUP BY
                r.id_dentista,
                r.nombre_dentista,
                r.centro,
                r.id_sucursal,
                r.especialidad
            ORDER BY r.centro, r.id_dentista, r.id_sucursal
        ";

        $resultados = DB::select($sql);

        return response()->json($resultados);
    }

    public function cuadrante_preguntas(Request $request) {
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');

        // Obtener sucursales asociadas al usuario
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal')->toArray();
        } else {
            $sucursales_ids = [(int) $request->sucursal];
        }

        // Evitar errores SQL si el usuario no tiene sucursales
        if (empty($sucursales_ids)) {
            return response()->json([]);
        }

        // Convertir array a string para usar en la query SQL
        $ids_str = implode(',', $sucursales_ids);

        $sql = "
            WITH datos_preguntas AS (
                SELECT 
                    r.id_pregunta,
                    p.pregunta,
                    p.id_categoria,
                    c.categoria,
                    AVG(TRY_CAST(r.respuesta AS FLOAT)) AS promedio,
                    COUNT(*) AS n_respuestas,
                    STDEV(TRY_CAST(r.respuesta AS FLOAT)) AS desviacion,
                    COUNT(CASE WHEN TRY_CAST(r.respuesta AS INT) <= 2 THEN 1 END) * 100.0 / COUNT(*) AS pct_criticos,
                    COUNT(CASE WHEN TRY_CAST(r.respuesta AS INT) <= 3 THEN 1 END) * 100.0 / COUNT(*) AS pct_insatisfechos
                FROM respuesta r
                INNER JOIN envio_encuesta e ON r.id_envio_encuesta = e.id_envio_encuesta
                INNER JOIN sucursal s ON e.id_sucursal = s.id_sucursal
                INNER JOIN pregunta p ON p.id_pregunta = r.id_pregunta
                INNER JOIN categoria c ON c.id_categoria = p.id_categoria
                WHERE r.id_pregunta NOT IN (1, 14)
                AND TRY_CAST(r.respuesta AS FLOAT) IS NOT NULL
                AND e.id_sucursal IN ($ids_str)
                GROUP BY r.id_pregunta, p.pregunta, p.id_categoria, c.categoria
                HAVING COUNT(*) >= 5
            ),
            con_indices AS (
                SELECT 
                    id_pregunta,
                    pregunta,
                    id_categoria,
                    categoria,
                    promedio,
                    n_respuestas,
                    ISNULL(desviacion, 0) AS desviacion,
                    pct_criticos,
                    pct_insatisfechos,
                    (5 - promedio) AS brecha,
                    ROUND(
                        ((5 - promedio) * 20) +      
                        (pct_insatisfechos * 0.35) +  
                        (pct_criticos * 0.25),
                    1) AS indice_prioridad
                FROM datos_preguntas
            )
            SELECT 
                id_pregunta,
                pregunta,
                categoria,
                CAST(promedio AS FLOAT) AS promedio_desempeno,
                CAST(brecha AS FLOAT) AS brecha,
                CAST(pct_criticos AS FLOAT) AS pct_criticos,
                CAST(pct_insatisfechos AS FLOAT) AS pct_insatisfechos,
                CAST(indice_prioridad AS FLOAT) AS indice_prioridad,
                n_respuestas AS total_respuestas,
                CAST(desviacion AS FLOAT) AS desviacion,
                CASE 
                    WHEN indice_prioridad >= 70 THEN 'URGENTE'
                    WHEN indice_prioridad >= 50 THEN 'ALTA'
                    WHEN indice_prioridad >= 30 THEN 'MEDIA'
                    ELSE 'BAJA'
                END AS nivel_prioridad,
                CASE 
                    WHEN indice_prioridad >= 70 THEN 1
                    WHEN indice_prioridad >= 50 THEN 2
                    WHEN indice_prioridad >= 30 THEN 3
                    ELSE 4
                END AS nivel_numerico,
                CASE 
                    WHEN promedio < 2.5 THEN 'Desempeño muy deficiente'
                    WHEN promedio < 3.5 THEN 'Desempeño bajo el estándar'
                    WHEN promedio < 4.0 THEN 'Desempeño mejorable'
                    WHEN promedio < 4.5 THEN 'Buen desempeño'
                    ELSE 'Excelente desempeño'
                END AS diagnostico
            FROM con_indices
            ORDER BY indice_prioridad DESC, pct_criticos DESC
        ";

        $resultados = DB::select($sql);

        $resultados = array_map(function ($item) {
            return [
                'id_pregunta' => (int) $item->id_pregunta,
                'pregunta' => $item->pregunta,
                'categoria' => $item->categoria,
                'promedio_desempeno' => round((float) $item->promedio_desempeno, 2),
                'brecha' => round((float) $item->brecha, 2),
                'indice_prioridad' => round((float) $item->indice_prioridad, 1),
                'pct_criticos' => round((float) $item->pct_criticos, 1),
                'pct_insatisfechos' => round((float) $item->pct_insatisfechos, 1),
                'total_respuestas' => (int) $item->total_respuestas,
                'desviacion' => round((float) $item->desviacion, 2),
                'nivel_prioridad' => $item->nivel_prioridad,
                'nivel_numerico' => (int) $item->nivel_numerico,
                'diagnostico' => $item->diagnostico
            ];
        }, $resultados);

        return response()->json($resultados);
    }


    public function traer_comentarios(Request $request) {
        // dd($request);
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');

        // Obtener sucursales asociadas al usuario
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal')->toArray();
        } else {
            $sucursales_ids = [(int) $request->sucursal];
        }

        if ($request->especialista === "null") {
            $query = "";
        } else {
            $query = "AND e.id_dentista = $request->especialista";
        }

        if ($request->mes === "null") {
            $querymes = "";
        } else {
            $querymes = "AND MONTH(e.fecha_envio) = '$request->mes'
                        AND YEAR(e.fecha_envio) = YEAR(GETDATE())";
        }


        $ids_str = implode(',', $sucursales_ids);

        $comentarios = DB::select("
            SELECT 
                r.respuesta AS comentario,
                e.nombre_dentista,
                e.sucursal,
                e.fecha_envio
            FROM respuesta r
            INNER JOIN envio_encuesta e ON e.id_envio_encuesta = r.id_envio_encuesta
            WHERE r.id_pregunta = 14 -- Comentarios
            AND r.respuesta IS NOT NULL
            AND r.respuesta <> ''
            AND e.id_sucursal IN ($ids_str)
            $querymes
            $query
            ORDER BY e.fecha_envio DESC
        ");

        return response()->json($comentarios);
        // dd($comentarios);
    }


    public function analizar_sentimientos(Request $request) {
        $user = Auth::user();
        $idEmpresa = Session::get('empresa_seleccionada_id');

        // Obtener sucursales asociadas al usuario
        $sucursales = UsuarioSucursal::sucursalesUsuario($user->id_usuario, $idEmpresa);

        if ($request->sucursal == 0) {
            $sucursales_ids = $sucursales->pluck('id_sucursal')->toArray();
        } else {
            $sucursales_ids = [(int) $request->sucursal];
        }

        if (empty($sucursales_ids)) {
            return response()->json([]);
        }

        $ids_str = implode(',', $sucursales_ids);

        // dd($ids_str);

        // Manejo de fechas del request (formato: Y-m-d)
        if ($request->filled(['fecha_inicio', 'fecha_fin'])) {
            $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay()->format('Ymd H:i');
            $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay()->format('Ymd H:i');
        } else {
            $fecha_inicio = now()->subMonths(3)->startOfDay()->format('Ymd H:i');
            $fecha_fin = now()->endOfDay()->format('Ymd H:i');
        }

        // dd($fecha_inicio);

        $comentarios = DB::select("
            SELECT 
                r.respuesta AS comentario,
                e.nombre_dentista,
                e.especialidad,
                e.sucursal,
                e.fecha_envio,
                -- Obtener el NPS asociado
                (SELECT TOP 1 TRY_CAST(r2.respuesta AS INT) 
                FROM respuesta r2 
                WHERE r2.id_envio_encuesta = e.id_envio_encuesta 
                AND r2.id_pregunta = 1) AS nps_score,
                -- Promedios de satisfacción
                (SELECT AVG(TRY_CAST(r3.respuesta AS FLOAT))
                FROM respuesta r3
                WHERE r3.id_envio_encuesta = e.id_envio_encuesta
                AND r3.id_pregunta IN (7, 8, 9)) AS promedio_satisfaccion
            FROM respuesta r
            INNER JOIN envio_encuesta e ON e.id_envio_encuesta = r.id_envio_encuesta
            WHERE r.id_pregunta = 14 -- Comentarios
            AND r.respuesta IS NOT NULL
            AND r.respuesta <> ''
            AND e.id_sucursal IN ($ids_str)
            AND e.fecha_envio BETWEEN '$fecha_inicio' AND '$fecha_fin'
            ORDER BY e.fecha_envio DESC
        ");

        // dd($comentarios);

        // Preparar datos para Claude
        $datosParaClaude = [
            'total_comentarios' => count($comentarios),
            'periodo' => "$fecha_inicio a $fecha_fin",
            'comentarios' => array_map(function($c) {
                return [
                    'texto' => $c->comentario,
                    'contexto' => [
                        'dentista' => $c->nombre_dentista,
                        'especialidad' => $c->especialidad,
                        'sucursal' => $c->sucursal,
                        'fecha' => $c->fecha_envio,
                        'nps' => $c->nps_score,
                        'satisfaccion' => round($c->promedio_satisfaccion ?? 0, 2)
                    ]
                ];
            }, $comentarios)
        ];

        $html = $this->enviarAClaude($datosParaClaude);

        return response($html, 200)
            ->header('Content-Type', 'text/html');
    }





    private function enviarAClaude($datos) {
        ini_set('max_execution_time', 300);
        $prompt = $this->construirPrompt($datos);


        $response = Http::withHeaders([
            'x-api-key' => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 8000,
            'temperature' => 0.3,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);


        if ($response->successful()) {
            // $analisis = $response->json()['completion'] ?? null;
            $analisisBruto = $response->json()['completion'] ?? $response->json()['content'][0]['text'] ?? null;

            if (!$analisisBruto) throw new \Exception('No se recibió completion del API');

            // Extraer el JSON desde la primera llave { hasta la última } 
            preg_match('/\{.*\}/s', $analisisBruto, $matches);
            if (!isset($matches[0])) {
                throw new \Exception('No se pudo extraer JSON válido del análisis');
            }

            $analisisArray = json_decode($matches[0], true);
            if (!$analisisArray) throw new \Exception('Error al decodificar JSON: ' . $matches[0]);

            return $this->generarHTML($analisisArray, $datos);

        }
    }


    private function construirPrompt($datos) {
        $comentariosJSON = json_encode($datos['comentarios'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return <<<PROMPT
                Eres un analista experto en experiencia del paciente para una clínica dental. Analiza los siguientes {$datos['total_comentarios']} comentarios de pacientes del periodo: {$datos['periodo']}.

                DATOS:
                $comentariosJSON

                INSTRUCCIONES DE ANÁLISIS:

                1. **ANÁLISIS DE SENTIMIENTOS**
                - Clasifica cada comentario: Positivo, Neutral, Negativo, Mixto
                - Calcula distribución porcentual
                - Identifica emociones específicas (frustración, gratitud, decepción, satisfacción)

                2. **TEMAS RECURRENTES**
                - Identifica los 10 temas más mencionados
                - Agrupa por: Atención del dentista, Tiempos de espera, Instalaciones, Precio, Resultados del tratamiento, Personal administrativo
                - Para cada tema: frecuencia, sentimiento predominante, ejemplos específicos

                3. **ANÁLISIS POR DENTISTA**
                - Ranking de dentistas por sentimiento positivo
                - Dentistas con más menciones negativas
                - Patrones específicos por profesional

                4. **ANÁLISIS POR CLÍNICA**
                - Comparación entre sucursales
                - Problemas específicos por ubicación
                - Mejores prácticas identificadas

                5. **CORRELACIÓN NPS-COMENTARIOS**
                - Relación entre score NPS y tipo de comentario
                - ¿Los promotores (9-10) comentan diferente que los detractores (0-6)?

                6. **INSIGHTS CRÍTICOS**
                - 5 problemas más urgentes a resolver
                - 5 fortalezas a potenciar
                - Sugerencias accionables específicas

                7. **CITAS TEXTUALES REPRESENTATIVAS**
                - 5 mejores comentarios (verbatim)
                - 5 peores comentarios (verbatim)
                - 3 comentarios constructivos

                8. **TENDENCIAS TEMPORALES**
                - ¿Hay cambios en el sentimiento a lo largo del periodo?
                - ¿Algún evento específico generó un patrón?

                FORMATO DE RESPUESTA:
                Devuelve ÚNICAMENTE un objeto JSON válido con esta estructura exacta:

                {
                "resumen_ejecutivo": {
                    "sentimiento_general": "positivo|neutral|negativo",
                    "score_sentimiento": 0-100,
                    "total_comentarios": número,
                    "distribucion": {
                    "positivos": porcentaje,
                    "neutrales": porcentaje,
                    "negativos": porcentaje,
                    "mixtos": porcentaje
                    }
                },
                "temas_principales": [
                    {
                    "tema": "string",
                    "frecuencia": número,
                    "sentimiento": "positivo|neutral|negativo",
                    "ejemplos": ["cita1", "cita2"]
                    }
                ],
                "analisis_dentistas": [
                    {
                    "nombre": "string",
                    "total_menciones": número,
                    "sentimiento_promedio": 0-100,
                    "positivos": número,
                    "negativos": número,
                    "comentarios_destacados": ["cita"]
                    }
                ],
                "analisis_sucursales": [
                    {
                    "nombre": "string",
                    "sentimiento_promedio": 0-100,
                    "temas_criticos": ["tema1", "tema2"],
                    "fortalezas": ["fortaleza1"]
                    }
                ],
                "insights_criticos": {
                    "problemas_urgentes": [
                    {
                        "problema": "string",
                        "impacto": "alto|medio|bajo",
                        "recomendacion": "string"
                    }
                    ],
                    "fortalezas": [
                    {
                        "fortaleza": "string",
                        "como_potenciar": "string"
                    }
                    ]
                },
                "citas_representativas": {
                    "mejores": ["cita1", "cita2", "cita3", "cita4", "cita5"],
                    "peores": ["cita1", "cita2", "cita3", "cita4", "cita5"],
                    "constructivos": ["cita1", "cita2", "cita3"]
                },
                "correlacion_nps": {
                    "insight": "string explicando la correlación",
                    "datos": {
                    "promotores_sentimiento": 0-100,
                    "pasivos_sentimiento": 0-100,
                    "detractores_sentimiento": 0-100
                    }
                }
                }

                IMPORTANTE: 
                - Devuelve SOLO el JSON, sin texto adicional
                - Usa comillas dobles
                - Escapa caracteres especiales
                - Sé específico y cuantitativo
                - Basa todo en los datos reales proporcionados
                PROMPT;
    }

    private function generarHTML($analisisArray, $datosOriginales) {
        return view('reporteria.analisis-sentimientos', [
            'analisis' => $analisisArray,
            'datos' => $datosOriginales,
            'fecha_generacion' => now()->format('d/m/Y H:i')
        ]);
    }

    public function datosNPS(Request $request) {
        $datos = DB::table('envio_encuesta')
                ->join('respuesta', 'respuesta.id_envio_encuesta', '=', 'envio_encuesta.id_envio_encuesta')
                ->where('respuesta.id_pregunta', 1)
                ->select([
                    'rut as RUT',
                    'respuesta.respuesta as NPS',
                    'id_agenda',
                    'nombre_paciente',
                    'nombre_dentista',
                    'sucursal as centro',
                    'fecha_cita',
                    DB::raw("CONVERT(VARCHAR(6), hora_cita, 108) as hora_cita"),
                    'fecha_envio'
                ])
                ->get();

        return $datos;
    }
}