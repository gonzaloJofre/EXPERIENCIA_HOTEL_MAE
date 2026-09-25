@extends('base.base')

@section('titulo', 'Dashboard')
@section('link')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection
@section('contenido')

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="text-center flex-grow-1">
            <img src="{{ asset('assets/images/mae/logo_mae_azul.png') }}" alt="Hotel MAE" class="logo-dashboard-mae">
            <h2 class="fw-semibold mb-1 mt-3">
                Reporte de encuesta de satisfacción
            </h2>
            <h5 class="text-muted mb-0">
                Resultados de satisfacción de huéspedes
            </h5>
        </div>
        {{-- Logout --}}
        <a href="{{ route('cerrar-sesion') }}" class="btn btn-logout dashboard-logout">
            Cerrar sesión
        </a>
    </div>

    <br><br>
    {{-- Botón Excel --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('reportes.exportar.excel') }}" class="btn btn-excel">
            <i class="bi bi-file-earmark-excel"></i>
            Descarga Excel
        </a>
    </div>
    <div class="row g-4 mb-4">
        {{-- Encuestas enviadas --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="indicador-titulo">
                        <h4><b>ENCUESTAS ENVIADAS</b></h4>
                    </div>
                    <div class="indicador-valor">
                        {{ $totalEnviadas }}
                    </div>
                    <div class="indicador-descripcion">
                        Total de encuestas enviadas a huéspedes
                    </div>
                </div>
            </div>
        </div>

        {{-- Encuestas respondidas --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="indicador-titulo">
                        <h4><b>ENCUESTAS RESPONDIDAS</b></h4>
                    </div>
                    <div class="indicador-valor">
                        {{ $totalRespondidas }}
                    </div>
                    <div class="indicador-descripcion">
                        Total de encuestas respondidas
                    </div>
                </div>
            </div>
        </div>

        {{-- Encuestas pendientes --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="indicador-titulo">
                        <h4><b>ENCUESTAS PENDIENTES</b></h4>
                    </div>
                    <div class="indicador-valor">
                        {{ $totalPendientes }}
                    </div>
                    <div class="indicador-descripcion">
                        Total de encuestas pendientes por responder
                    </div>
                </div>
            </div>
        </div>

        {{-- Tasa de respuesta --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="indicador-titulo">
                        <h4><b>TASA DE RESPUESTA</b></h4>
                    </div>
                    <div class="indicador-valor">
                        {{ $porcentajeRespuesta }}%
                    </div>
                    <div class="indicador-descripcion">
                        Porcentaje de encuestas respondidas
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Particiáción gral --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-4 satisfaccion-card">
                    <div class="section-title text-center">
                        <h2>Participación en la encuesta</h2>
                    </div>
                    <div class="section-description">
                        Muestra cuántas encuestas fueron respondidas y cuántas están pendientes, considerando el total de encuestas enviadas.
                    </div>
                    <div class="grafico-respuestas">
                        <canvas id="graficoRespuestas"></canvas>
                    </div>

                    {{-- Resumen de participación --}}
                    <div class="participacion-resumen">
                        <div class="participacion-item">
                            <div class="participacion-indicador respondidas"></div>
                            <div class="participacion-info">
                                <div class="participacion-label">
                                    Respondidas
                                </div>
                                <div class="participacion-datos">
                                    <strong>{{ $totalRespondidas }}</strong>
                                    <span>
                                        {{ $totalEnviadas > 0 ? round(($totalRespondidas / $totalEnviadas) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="participacion-item">
                            <div class="participacion-indicador pendientes"></div>
                            <div class="participacion-info">
                                <div class="participacion-label">
                                    Pendientes
                                </div>
                                <div class="participacion-datos">
                                    <strong>{{ $totalPendientes }}</strong>
                                    <span>
                                        {{ $totalEnviadas > 0 ? round(($totalPendientes / $totalEnviadas) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Satisfacción general --}}
        <div class="col-xl-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-4 satisfaccion-card">
                    <div class="section-title text-center">
                        <h2>Satisfacción general</h2>
                    </div>
                    <div class="section-description">
                        Promedio general de las evaluaciones recibidas.
                    </div>
                    <div class="satisfaccion-valor">
                        <span id="satisfaccionGeneral">
                            -
                        </span>
                        <span class="satisfaccion-maximo">
                            / 5
                        </span>
                    </div>

                    {{-- Estrellas --}}
                    <div id="estrellasSatisfaccion" class="estrellas-satisfaccion"></div>
                    <div id="textoSatisfaccion" class="texto-satisfaccion">
                        -
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resultados por área --}}
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="resultados-area-header text-center">
                <div class="resultados-area-titulo justify-content-center">
                    <div class="resultados-area-icono">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="resultados-area-marca">HOTEL MAE</div>
                        <div class="section-title mb-0">
                            <h2>Resultados por área</h2>
                        </div>
                    </div>
                </div>
                <div class="section-description">
                    Promedio de satisfacción de los huéspedes en cada área.
                    <strong>Escala de evaluación: 1 a 5.</strong>
                </div>
            </div>

            {{-- Gráfico general --}}
            <div class="grafico-areas mb-4">
                <canvas id="graficoAreas"></canvas>
            </div>

            {{-- Resumen de mejor y peor área --}}
            <div class="row g-4 mb-4">
                <div class="col-xl-6">
                    <div class="card resumen-recomendacion-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            {{-- Resumen de mejor y peor área --}}
                            <div class="row g-3 mb-4">
                                {{-- Mejor evaluado --}}
                                <div class="col-6">
                                    <div class="card resumen-area-card border-0 shadow-sm h-100">
                                        <div class="card-body p-3">
                                            <div class="area-resumen-icon">
                                                <i class="bi bi-trophy"></i>
                                            </div>
                                            <div class="area-resumen-contenido">
                                                <div class="area-resumen-titulo">
                                                    Mejor evaluado
                                                </div>
                                                <div class="area-resumen-nombre">
                                                    @if(count($detalleAreas) > 0)
                                                        {{ collect($detalleAreas)
                                                            ->sortByDesc('promedio')
                                                            ->first()['categoria'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                                <div class="area-resumen-puntaje">
                                                    @if(count($detalleAreas) > 0)
                                                        {{ number_format(collect($detalleAreas)
                                                                ->sortByDesc('promedio')
                                                                ->first()['promedio'], 1, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                    <span>/ 5</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Oportunidad de mejorar --}}
                                <div class="col-6">
                                    <div class="card resumen-area-card border-0 shadow-sm h-100">
                                        <div class="card-body p-3">
                                            <div class="area-resumen-icon">
                                                <i class="bi bi-arrow-up-circle"></i>
                                            </div>
                                            <div class="area-resumen-contenido">
                                                <div class="area-resumen-titulo">
                                                    Oportunidad de mejorar
                                                </div>
                                                <div class="area-resumen-nombre">
                                                    @if(count($detalleAreas) > 0)

                                                        {{ collect($detalleAreas)
                                                            ->sortBy('promedio')
                                                            ->first()['categoria'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                                <div class="area-resumen-puntaje">
                                                    @if(count($detalleAreas) > 0)
                                                        {{ number_format(collect($detalleAreas)
                                                                ->sortBy('promedio')
                                                                ->first()['promedio'], 1, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                    <span>/ 5</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resultados-areas-detalle">
                                @forelse($detalleAreas as $indice => $area)
                                    <div class="resultado-area-item">
                                        {{-- Encabezado del área --}}
                                        <button class="resultado-area-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#detalleArea{{ $indice }}" aria-expanded="false" aria-controls="detalleArea{{ $indice }}">
                                            <div class="resultado-area-info">
                                                <div class="resultado-area-nombre">
                                                    {{ $area['categoria'] }}
                                                </div>
                                                <div class="resultado-area-promedio">
                                                    {{ number_format($area['promedio'], 1, ',', '.') }}
                                                    <span>/ 5</span>
                                                </div>
                                            </div>
                                            <div class="resultado-area-flecha">
                                                <span>⌄</span>
                                            </div>
                                        </button>

                                        {{-- Contenido desplegable --}}
                                        <div id="detalleArea{{ $indice }}" class="collapse">
                                            <div class="resultado-area-detalle-contenido">
                                                {{-- Mejor evaluada --}}
                                                <div class="resultado-pregunta resultado-pregunta-mejor">
                                                    <div class="resultado-pregunta-titulo">
                                                        Mejor evaluada
                                                    </div>
                                                    <div class="resultado-pregunta-texto">
                                                        {{ $area['mejorPregunta'] ?? 'Sin información disponible' }}
                                                    </div>
                                                    @if($area['mejorPreguntaPromedio'] !== null)
                                                        <div class="resultado-pregunta-puntaje">
                                                            {{ number_format($area['mejorPreguntaPromedio'], 1,
                                                                ',',
                                                                '.'
                                                            ) }} / 5
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Oportunidad de mejora --}}
                                                <div class="resultado-pregunta resultado-pregunta-peor">
                                                    <div class="resultado-pregunta-titulo">
                                                        Oportunidad de mejora
                                                    </div>
                                                    <div class="resultado-pregunta-texto">
                                                        {{ $area['peorPregunta'] ?? 'Sin información disponible' }}
                                                    </div>
                                                    @if($area['peorPreguntaPromedio'] !== null)

                                                        <div class="resultado-pregunta-puntaje">

                                                            {{ number_format(
                                                                $area['peorPreguntaPromedio'],
                                                                1,
                                                                ',',
                                                                '.'
                                                            ) }} / 5
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        No hay resultados disponibles para mostrar.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card dashboard-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center recomendacion-card">
                            <div class="section-title text-center">
                                <h2>
                                    Recomendación de Hotel MAE
                                </h2>
                            </div>
                            <div class="section-description">
                                Qué tan probable es que los huéspedes recomienden
                                su experiencia en Hotel MAE.
                            </div>

                            <div class="recomendacion-etiqueta">
                                Resultado de recomendación
                            </div>
                            <div class="recomendacion-valor">
                                <span id="npsValor">
                                    -
                                </span>
                            </div>
                            <div class="recomendacion-ayuda">
                                Escala NPS: -100 a +100 puntos.
                            </div>

                            {{-- Separador --}}
                            <div class="recomendacion-separador"></div>
                            <div class="section-title text-center">
                                <h2>
                                    Evaluación de los huéspedes
                                </h2>
                            </div>
                            <div class="section-description text-center mb-3">
                                Distribución de las respuestas según el nivel de recomendación de los huéspedes.
                            </div>

                            {{-- Gráfico NPS --}}
                            <div class="grafico-nps">
                                <canvas id="graficoNps"></canvas>
                            </div>

                            {{-- Resumen de evaluación --}}
                            <div class="evaluacion-resumen">
                                {{-- Muy satisfechos --}}
                                <div class="evaluacion-item">
                                    <div class="evaluacion-indicador muy-satisfechos"></div>
                                    <div class="evaluacion-info">
                                        <div class="evaluacion-label">
                                            Muy satisfechos
                                        </div>
                                        <div class="evaluacion-datos">
                                            <strong>
                                                {{ $promotores }}
                                            </strong>
                                            <span>
                                                {{ ($promotores + $neutros + $detractores) > 0 ? round(($promotores / ($promotores + $neutros + $detractores)) * 100, 1) : 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Satisfechos --}}
                                <div class="evaluacion-item">
                                    <div class="evaluacion-indicador satisfechos"></div>
                                    <div class="evaluacion-info">
                                        <div class="evaluacion-label">
                                            Satisfechos
                                        </div>
                                        <div class="evaluacion-datos">
                                            <strong>
                                                {{ $neutros }}
                                            </strong>
                                            <span>
                                                {{($promotores + $neutros + $detractores) > 0 ? round(($neutros / ($promotores + $neutros + $detractores)) * 100, 1) : 0
                                                }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Poco satisfechos --}}
                                <div class="evaluacion-item">
                                    <div class="evaluacion-indicador poco-satisfechos"></div>
                                    <div class="evaluacion-info">
                                        <div class="evaluacion-label">
                                            Poco satisfechos
                                        </div>
                                        <div class="evaluacion-datos">
                                            <strong>
                                                {{ $detractores }}
                                            </strong>
                                            <span>
                                                {{ ($promotores + $neutros + $detractores) > 0 ? round(($detractores / ($promotores + $neutros + $detractores)) * 100, 1) : 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    {{-- Comentarios de huéspedes --}}
    <div class="card dashboard-card comentarios-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            @php
                $comentariosMostrar = [];

                foreach($comentarios ?? [] as $comentario){

                    if(is_object($comentario)){
                        $comentario = (string) $comentario;
                    }

                    $datosComentario = json_decode($comentario, true);

                    if(json_last_error() === JSON_ERROR_NONE && is_array($datosComentario)){

                        $respuesta = trim($datosComentario['respuesta'] ?? '');
                        $pregunta = trim($datosComentario['pregunta'] ?? '');

                        if($respuesta !== '' && !in_array(mb_strtolower($respuesta), ['no', 'nada', 'ninguno', 'ninguna'])){
                            $comentariosMostrar[] = [
                                'pregunta' => $pregunta,
                                'respuesta' => $respuesta
                            ];
                        }

                    }else {

                        $comentarioTexto = trim($comentario);

                        if($comentarioTexto !== ''){
                            $comentariosMostrar[] = [
                                'pregunta' => '',
                                'respuesta' => $comentarioTexto
                            ];
                        }
                    }
                }

                $totalComentarios = count($comentariosMostrar);

                $comentariosVisibles = array_slice($comentariosMostrar, -5);

                $comentariosAnteriores = array_slice($comentariosMostrar, max(0, $totalComentarios - 10), 5);

                $hayMasComentarios = $totalComentarios > 10;
            @endphp

            {{-- Encabezado Comentarios--}}
            <div class="comentarios-header-mae">
                <div class="comentarios-titulo-bloque">
                    <div class="resultados-area-icono comentarios-icono-mae">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="comentarios-marca-mae">
                            HOTEL MAE
                        </div>
                        <div class="section-title mb-1">
                            <h2>Lo que dicen nuestros huéspedes</h2>
                        </div>
                        <div class="section-description mb-0">
                            Comentarios compartidos por huéspedes sobre su estadía, atención y experiencia en Hotel MAE.
                        </div>
                    </div>
                </div>
                <div class="comentarios-contador-mae">
                    <span>
                        {{ $totalComentarios == 1 ? 'comentario recibido' : 'comentarios recibidos' }}
                    </span>
                </div>
            </div>
            
            @if($totalComentarios > 0)
                <div class="comentarios-linea-mae"></div>

                {{-- Comentarios principales --}}
                <div class="comentarios-lista-mae">
                    @foreach($comentariosVisibles as $comentario)
                        <div class="comentario-mae">
                            <div class="comentario-icono-mae">
                                <i class="bi bi-chat-left-quote-fill"></i>
                            </div>
                            <div class="comentario-contenido-mae">
                                @if(!empty($comentario['pregunta']))
                                    <div class="comentario-pregunta-mae">
                                        {{ $comentario['pregunta'] }}
                                    </div>
                                @endif

                                <div class="comentario-respuesta-mae">
                                    “{{ $comentario['respuesta'] }}”
                                </div>
                                {{-- <div class="comentario-firma-mae">
                                    <i class="bi bi-water"></i>
                                    Experiencia Hotel MAE
                                </div> --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Comentarios anteriores --}}
                @if(count($comentariosAnteriores) > 0)
                    <div class="accordion comentarios-accordion-mae mt-4" id="accordionComentarios">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#comentariosAnteriores" aria-expanded="false" aria-controls="comentariosAnteriores">
                                    <span class="comentarios-anteriores-icono">
                                        <i class="bi bi-chat-left-text"></i>
                                    </span>
                                    <span class="comentarios-anteriores-texto">
                                        <strong>
                                            Ver 5 comentarios anteriores
                                        </strong>
                                        <small>
                                            Mostrar más opiniones recientes de huéspedes
                                        </small>
                                    </span>
                                    <span class="comentarios-flecha">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>
                                </button>
                            </h2>

                            <div id="comentariosAnteriores" class="accordion-collapse collapse" data-bs-parent="#accordionComentarios">
                                <div class="accordion-body">
                                    @foreach($comentariosAnteriores as $comentario)
                                        <div class="comentario-mae comentario-anterior-mae">
                                            <div class="comentario-icono-mae">
                                                <i class="bi bi-chat-left-text"></i>
                                            </div>
                                            <div class="comentario-contenido-mae">

                                                @if(!empty($comentario['pregunta']))
                                                    <div class="comentario-pregunta-mae">
                                                        {{ $comentario['pregunta'] }}
                                                    </div>
                                                @endif
                                                <div class="comentario-respuesta-mae">
                                                    “{{ $comentario['respuesta'] }}”
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                {{-- Sin comentarios --}}
                <div class="comentarios-vacio-mae">
                    <div class="comentarios-vacio-icono">
                        <i class="bi bi-chat-heart"></i>
                    </div>
                    <h4>Aún no hay comentarios</h4>
                    <p>
                        Cuando los huéspedes compartan sus opiniones sobre su experiencia en Hotel MAE, aparecerán aquí.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('link')

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    //Datos para los gráficos y reportes
    const totalRespondidas = {{ $totalRespondidas }};
    const totalPendientes = {{ $totalPendientes }};
    const satisfaccionGeneral = {{ $satisfaccionGeneral }};
    const npsValor = {{ $npsValor !== null ? $npsValor : 0 }};
    const labelsAreas = @json($labelsAreas);
    const datosAreas = @json($datosAreas);


    //Participación
    const ctxRespuestas = document.getElementById('graficoRespuestas').getContext('2d');
    const totalEncuestas = totalRespondidas + totalPendientes;

    new Chart(ctxRespuestas, {
        type: 'pie',
        data: {
            labels: [
                'Respondidas',
                'Pendientes'
            ],
            datasets: [{
                data: [
                    totalRespondidas,
                    totalPendientes
                ],
                backgroundColor: [
                    '#1E4F63',
                    '#FF6347'
                ],
                borderColor: '#FFFFFF',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });


    // Satisfacción gral
    document.getElementById('satisfaccionGeneral').innerText = satisfaccionGeneral.toFixed(1).replace('.', ',');

    const estrellas = document.getElementById('estrellasSatisfaccion');
    const satisfaccionRedondeada = Math.round(satisfaccionGeneral);

    let estrellasHTML = '';

    for(let i = 1; i <= 5; i++){

        if(i <= satisfaccionRedondeada){
            estrellasHTML += '★';
        }else{
            estrellasHTML += '☆';
        }

    }

    estrellas.innerHTML = estrellasHTML;

    let textoSatisfaccion = 'Sin evaluación';

    if(satisfaccionGeneral >= 4.5){

        textoSatisfaccion = 'Excelente';

    }else if(satisfaccionGeneral >= 4){

        textoSatisfaccion = 'Muy bueno';

    }else if(satisfaccionGeneral >= 3){

        textoSatisfaccion = 'Bueno';

    }else if(satisfaccionGeneral >= 2){

        textoSatisfaccion = 'Regular';

    }else if(satisfaccionGeneral > 0){

        textoSatisfaccion = 'Necesita mejorar';
    }

    document.getElementById('textoSatisfaccion').innerText = textoSatisfaccion;


    /* -------- Gráfico Resultados por área - comparación -------- */
    const ctxAreas = document.getElementById('graficoAreas').getContext('2d');

    const labelsAreasMensuales = @json($labelsAreasMensuales);
    const datosAreasMensuales = @json($datosAreasMensuales);

    // Colores de las áreas
    const coloresAreas = [
        '#1E4F63',
        '#D8A84E',
        '#5B8C5A',
        '#8A5A83',
        '#B65C5C',
        '#6C757D'
    ];

    // Crear una línea por cada área
    const datasetsAreas = Object.entries(datosAreasMensuales).map(
        ([categoria, valores], index) => {

            const color = coloresAreas[index % coloresAreas.length];

            return {
                label: categoria,
                data: valores,
                borderColor: color,
                backgroundColor: color,
                borderWidth: 3,
                tension: 0.35,
                fill: false,
                pointStyle: 'circle',
                pointBackgroundColor: color,
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                spanGaps: true
            };
        }
    );

    //Al pasar por bloque entre meses... aprezca información del primer mes
    const hoverPorMes = {

        id: 'hoverPorMes',
        beforeEvent(chart, args) {
            const event = args.event;
            if (!event || event.type !== 'mousemove') {
                return;
            }

            const escalaX = chart.scales.x;

            if (!escalaX) {
                return;
            }

            const x = event.x;
            const cantidadMeses = labelsAreasMensuales.length;

            // Posición real de cada mes en el gráfico
            const posicionesMeses = [];

            for (let i = 0; i < cantidadMeses; i++) {

                posicionesMeses.push(
                    escalaX.getPixelForValue(i)
                );
            }

            let indice = 0;

            for(let i = 0; i < cantidadMeses - 1; i++){

                const inicio = posicionesMeses[i];
                const siguiente = posicionesMeses[i + 1];

                if(x >= inicio && x < siguiente){

                    indice = i;

                    break;
                }
            }

            // Si está sobre o después del último mes
            if (x >= posicionesMeses[cantidadMeses - 1]) {

                indice = cantidadMeses - 1;
            }

            // Si está antes del primer mes
            if (x < posicionesMeses[0]) {

                indice = 0;
            }

            const elementosActivos = [];

            chart.data.datasets.forEach(
                (dataset, datasetIndex) => {

                    const valor = dataset.data[indice];

                    if(valor !== null && valor !== undefined){

                        elementosActivos.push({
                            datasetIndex: datasetIndex,
                            index: indice
                        });
                    }
                }
            );

            chart.setActiveElements(elementosActivos);

            chart.tooltip.setActiveElements(
                elementosActivos,
                {
                    x: posicionesMeses[indice],
                    y: event.y
                }
            );

            args.changed = true;

            return false;
        }
    };


    new Chart(ctxAreas, {

        type: 'line',
        plugins: [
            hoverPorMes
        ],
        data: {
            labels: labelsAreasMensuales,
            datasets: datasetsAreas
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },
            scales: {
                y: {
                    min: 1,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        color: '#1E4F63',
                        font: {
                            size: 13,
                            weight: '700'
                        },

                        padding: 8
                    },
                    title: {
                        display: true,
                        text: 'Satisfacción (1 a 5)',
                        color: '#1E4F63',
                        font: {
                            size: 14,
                            weight: '700'
                        },
                        padding: {
                            bottom: 10
                        }
                    },
                    grid: {
                        display: true,
                        color: '#C9D4D8',
                        lineWidth: 1
                    }
                },

                x: {
                    offset: false,
                    ticks: {
                        color: '#2F2F2F',
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        padding: 10,
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0
                    },

                    grid: {
                        display: true,
                        color: '#E1E7E9',
                        lineWidth: 1
                    }
                }
            },

            plugins: {

                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {

                        color: '#2F2F2F',
                        padding: 18,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 14,
                        boxHeight: 14,
                        font: {

                            size: 13,
                            weight: '600'
                        }
                    }
                },

                tooltip: {

                    enabled: true,
                    backgroundColor: '#1E4F63',
                    titleColor: '#FFFFFF',
                    bodyColor: '#FFFFFF',
                    titleFont: {
                        size: 14,
                        weight: '700'
                    },

                    bodyFont: {
                        size: 14,
                        weight: '600'
                    },

                    padding: 12,
                    displayColors: true,
                    callbacks: {

                        title: function(context) {

                            if (!context || context.length === 0) {

                                return '';
                            }

                            const indice = context[0].dataIndex;

                            return labelsAreasMensuales[indice];
                        },

                        label: function(context) {

                            const valor = context.parsed.y;

                            if (valor === null || valor === undefined) {

                                return context.dataset.label + ': Sin datos';
                            }

                            return context.dataset.label +
                                ': ' +
                                Number(valor)
                                    .toFixed(1)
                                    .replace('.', ',') +
                                ' / 5';
                        }
                    }
                }
            }
        }
    });

    console.log('MESES:', labelsAreasMensuales);
    console.log('DATOS:', datosAreasMensuales);

    // REcomendación
    const npsTexto = npsValor > 0 ? `+${npsValor}` : `${npsValor}`;
    document.getElementById('npsValor').innerText = `${npsTexto} puntos`;

    // Evaluación de huéspedes
    const ctxNps = document.getElementById('graficoNps').getContext('2d');
    const totalNps = {{ $promotores + $neutros + $detractores }};

    new Chart(ctxNps, {
        type: 'pie',
        data: {
            labels: [
                'Muy satisfechos',
                'Satisfechos',
                'Poco satisfechos'
            ],
            datasets: [{
                data: [
                    {{ $promotores }},
                    {{ $neutros }},
                    {{ $detractores }}
                ],
                backgroundColor: [
                    '#1E4F63',
                    '#D8C4A0',
                    '#FF6347'
                ],
                borderColor: '#FFFFFF',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins:{
                legend:{
                    display: false
                },
                tooltip:{
                    enabled: false
                }
            }
        }
    });

</script>

@endsection