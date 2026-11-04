@extends('base.base')

@section('titulo', 'Dashboard')
@section('link')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reporte-hotelmae.css') }}">
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
    <div class="dashboard-filtros">
        <form action="{{ url()->current() }}" method="GET" class="filtros-periodo">
            <label for="periodo" class="filtro-label">Período:</label>

            <select name="periodo" id="periodo" class="filtro-select">
                <option value="todo" {{ $periodo === 'todo' ? 'selected' : '' }}>
                    Todos los Periodos
                </option>
                <option value="semanal" {{ $periodo === 'semanal' ? 'selected' : '' }}>
                    Semanal
                </option>
                <option value="mensual" {{ $periodo === 'mensual' ? 'selected' : '' }}>
                    Mensual
                </option>
                <option value="anual" {{ $periodo === 'anual' ? 'selected' : '' }}>
                    Anual
                </option>
            </select>

            <button type="submit" class="btn btn-filtrar">
                Filtrar
            </button>
        </form>

        {{-- <a href="{{ route('reportes.exportar.excel', ['periodo' => request('periodo', 'mensual')]) }}" class="btn btn-excel">
            <i class="bi bi-file-earmark-excel"></i>
            &nbsp;
            Descarga Excel
        </a> --}}
        <a href="{{ route('reportes.exportar.excel', ['periodo' => $periodo]) }}" class="btn btn-excel">
            <i class="bi bi-file-earmark-excel"></i>
            &nbsp;
            Descarga Excel
        </a>
    </div>

    <div class="row g-4 mb-4">
        {{-- Encuestas enviadas --}}
        <div class="col-xl col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="indicador-titulo">
                        <h5><b>ENCUESTAS ENVIADAS</b></h5>
                    </div>
                    <div class="indicador-valor">
                        {{ $totalEnviadas }}
                    </div>
                    <div class="indicador-descripcion">
                        Total enviadas
                    </div>
                </div>
            </div>
        </div>

        {{-- Encuestas respondidas --}}
        <div class="col-xl col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="indicador-titulo">
                        <h5><b>ENCUESTAS RESPONDIDAS</b></h5>
                    </div>
                    <div class="indicador-valor">
                        {{ $totalRespondidas }}
                    </div>
                    <div class="indicador-descripcion">
                        Total respondidas
                    </div>
                </div>
            </div>
        </div>

        {{-- Encuestas pendientes --}}
        <div class="col-xl col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="indicador-titulo">
                        <h5><b>ENCUESTAS PENDIENTES</b></h5>
                    </div>
                    <div class="indicador-valor">
                        {{ $totalPendientes }}
                    </div>

                    <div class="indicador-descripcion">
                        Por responder
                    </div>
                </div>
            </div>
        </div>

        {{-- Tasa de respuesta --}}
        <div class="col-xl col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="indicador-titulo">
                        <h5><b>TASA DE RESPUESTA</b></h5>
                    </div>
                    <div class="indicador-valor">
                        {{ $porcentajeRespuesta }}%
                    </div>
                    <div class="indicador-descripcion">
                        Encuestas respondidas
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Recomendación NPS --}}
        <div class="col-xl col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="indicador-titulo">
                        <h5><b>NPS</b></h5>
                    </div>
                    <div class="indicador-valor">
                        {{ $npsValor !== null ? number_format($npsValor, 1, '.', '') : '-' }}%
                    </div>
                    <div class="indicador-descripcion">
                        Promedio sobre 10
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
                    {{-- Gráfico de resultados por área - utilianzado Highcharts --}}
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
                <div id="graficoAreas"></div>
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
                                                            {{ number_format($area['mejorPreguntaPromedio'], 1, ',', '.') }} / 5
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

                                                            {{ number_format($area['peorPreguntaPromedio'], 1,
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
                            {{-- <div class="section-description">
                                Qué tan probable es que los huéspedes recomienden su experiencia en Hotel MAE.
                            </div> --}}

                            <div class="recomendacion-etiqueta">
                                Resultado de recomendación
                            </div>
                            <div class="recomendacion-valor">
                                <span id="npsValor">
                                    -
                                </span>
                            </div>
                            <div class="recomendacion-ayuda">
                                Escala NPS: 0 a 10 puntos.
                            </div>

                            {{-- Separador antes del gráfico --}}
                            <div class="recomendacion-separador"></div>
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
                                            Promotores
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
                                            Neutros
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
                                            Detractores
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
<script src="https://code.highcharts.com/highcharts.js"></script>

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

    
    /* -------- Gráfico Resultados por área - Highcharts -------- */
    // Datos preparados desde DashboardController
    const labelsAreasMensuales = @json($labelsAreasMensuales);
    const datosAreasMensuales = @json($datosAreasMensuales);

    // Colores institucionales y complementarios de Hotel MAE
    const coloresAreas = [
        '#1E4F63',
        '#D8A84E',
        '#5B8C5A',
        '#8A5A83',
        '#B65C5C',
        '#6C757D'
    ];

    // Transformar los datos PHP al formato de series de Highcharts
    const seriesAreas = Object.entries(datosAreasMensuales).map(
        ([categoria, valores], index) => {

            return {
                name: categoria,
                data: valores.map(valor =>
                    valor === null ? null : Number(valor)
                ),
                color: coloresAreas[index % coloresAreas.length]
            };
        }
    );

    // Crear gráfico
    Highcharts.chart('graficoAreas', {

        chart: {
            type: 'column',
            backgroundColor: 'transparent',
            height: 420,
            style: {
                fontFamily: 'Arial, sans-serif'
            },
            spacingBottom: 20
        },
        title: {
            text: null
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: labelsAreasMensuales,
            title: {
                text: 'Período',
                style: {
                    color: '#1E4F63',
                    fontWeight: '700'
                }
            },
            labels: {
                style: {
                    color: '#2F2F2F',
                    fontSize: '12px',
                    fontWeight: '600'
                }
            },
            lineColor: '#C9D4D8',
            tickColor: '#C9D4D8'
        },
        yAxis: {
            min: 1,
            max: 5,
            tickInterval: 1,
            title: {
                text: 'Promedio de satisfacción (1 a 5)',
                style: {
                    color: '#1E4F63',
                    fontWeight: '700'
                }
            },
            labels: {
                style: {
                    color: '#1E4F63',
                    fontWeight: '600'
                }
            },
            gridLineColor: '#E1E7E9'
        },
        legend: {
            enabled: true,
            align: 'center',
            verticalAlign: 'bottom',
            itemStyle: {
                color: '#2F2F2F',
                fontWeight: '600',
                fontSize: '12px'
            },
            itemHoverStyle: {
                color: '#1E4F63'
            }
        },
        tooltip: {
            shared: false,
            backgroundColor: '#1E4F63',
            borderColor: '#1E4F63',
            borderRadius: 8,
            style: {
                color: '#FFFFFF',
                fontSize: '13px'
            },
            formatter: function () {
                return `
                    <b>${this.series.name}</b><br>
                    Período: ${this.x}<br>
                    Satisfacción: <b>${Highcharts.numberFormat(this.y, 2, ',', '.')} / 5</b>
                `;
            }
        },
        plotOptions: {
            column: {
                grouping: true,
                borderRadius: 3,
                borderWidth: 0,
                pointPadding: 0.08,
                groupPadding: 0.12,
                minPointLength: 2
            },
            series: {
                animation: {
                    duration: 1000
                }
            }
        },
        series: seriesAreas,

        lang: {
            noData: 'No hay resultados disponibles para este período'
        }
    });

    console.log('PERÍODOS DEL GRÁFICO:', labelsAreasMensuales);
    console.log('SERIES POR CATEGORÍA:', seriesAreas);


    // REcomendación
    const npsTexto = npsValor > 0 ? `${npsValor}` : `${npsValor}`;
    document.getElementById('npsValor').innerText = `${npsTexto} %`;

    // Evaluación de huéspedes
    const ctxNps = document.getElementById('graficoNps').getContext('2d');
    const totalNps = {{ $promotores + $neutros + $detractores }};

    new Chart(ctxNps, {
        type: 'pie',
        data: {
            labels: [
                'Promotores',
                'Neutros',
                'Detractores'
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