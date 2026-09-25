<div class="modal fade" id="modalAnalisisSentimientos" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h3 class="modal-title">
                    <i class="bi bi-brain me-2"></i>
                    Análisis de Sentimientos - Inteligencia Artificial
                </h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0" style="overflow-y: auto;">
                <!-- Hero Section -->
                <div class="bg-gradient-primary text-white p-5">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-4 fw-bold mb-3">Análisis Inteligente de Experiencia del Paciente</h1>
                                <p class="lead">{{ $datos['total_comentarios'] }} comentarios analizados | Periodo: {{ $datos['periodo'] }}</p>
                                <p class="small opacity-75">Generado: {{ $fecha_generacion }}</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="display-1">
                                    @if($analisis['resumen_ejecutivo']['sentimiento_general'] == 'positivo')
                                        <i class="bi bi-emoji-smile text-success"></i>
                                    @elseif($analisis['resumen_ejecutivo']['sentimiento_general'] == 'negativo')
                                        <i class="bi bi-emoji-frown text-danger"></i>
                                    @else
                                        <i class="bi bi-emoji-neutral text-warning"></i>
                                    @endif
                                </div>
                                <h2 class="mt-3">{{ $analisis['resumen_ejecutivo']['score_sentimiento'] }}/100</h2>
                                <p>Score de Sentimiento</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container my-5">
                    <!-- Distribución de Sentimientos -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-white py-3">
                                    <h4 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribución de Sentimientos</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="p-4 rounded bg-success bg-opacity-10">
                                                <i class="bi bi-emoji-smile display-4 text-success"></i>
                                                <h2 class="mt-3 text-success">{{ number_format($analisis['resumen_ejecutivo']['distribucion']['positivos'], 1) }}%</h2>
                                                <p class="text-muted">Positivos</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-4 rounded bg-info bg-opacity-10">
                                                <i class="bi bi-emoji-neutral display-4 text-info"></i>
                                                <h2 class="mt-3 text-info">{{ number_format($analisis['resumen_ejecutivo']['distribucion']['neutrales'], 1) }}%</h2>
                                                <p class="text-muted">Neutrales</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-4 rounded bg-danger bg-opacity-10">
                                                <i class="bi bi-emoji-frown display-4 text-danger"></i>
                                                <h2 class="mt-3 text-danger">{{ number_format($analisis['resumen_ejecutivo']['distribucion']['negativos'], 1) }}%</h2>
                                                <p class="text-muted">Negativos</p>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <div class="p-4 rounded bg-warning bg-opacity-10">
                                                <i class="bi bi-emoji-expressionless display-4 text-warning"></i>
                                                <h2 class="mt-3 text-warning">{{ number_format($analisis['resumen_ejecutivo']['distribucion']['mixtos'], 1) }}%</h2>
                                                <p class="text-muted">Mixtos</p>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Insights Críticos -->
                    <div class="row mb-5">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-lg border-0 h-100 border-start border-danger border-4">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Problemas Urgentes</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($analisis['insights_criticos']['problemas_urgentes'] as $index => $problema)
                                    <div class="alert alert-danger mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-danger rounded-circle me-3" style="width: 30px; height: 30px; line-height: 20px;">{{ $index + 1 }}</span>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2">{{ $problema['problema'] }}</h6>
                                                <span class="badge bg-{{ $problema['impacto'] == 'alto' ? 'danger' : ($problema['impacto'] == 'medio' ? 'warning' : 'info') }}">
                                                    Impacto: {{ ucfirst($problema['impacto']) }}
                                                </span>
                                                <p class="small mt-2 mb-0"><strong>Recomendación:</strong> {{ $problema['recomendacion'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-lg border-0 h-100 border-start border-success border-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>Fortalezas a Potenciar</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($analisis['insights_criticos']['fortalezas'] as $index => $fortaleza)
                                    <div class="alert alert-success mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-success rounded-circle me-3" style="width: 30px; height: 30px; line-height: 20px;">{{ $index + 1 }}</span>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2">{{ $fortaleza['fortaleza'] }}</h6>
                                                <p class="small mb-0"><strong>Cómo potenciar:</strong> {{ $fortaleza['como_potenciar'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Temas Principales -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="bi bi-tags me-2"></i>Temas Más Mencionados</h4>
                                </div>
                                <div class="card-body">
                                    @foreach($analisis['temas_principales'] as $tema)
                                    <div class="mb-4 pb-4 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0">{{ $tema['tema'] }}</h5>
                                            <span class="badge bg-{{ $tema['sentimiento'] == 'positivo' ? 'success' : ($tema['sentimiento'] == 'negativo' ? 'danger' : 'warning') }} fs-6">
                                                {{ ucfirst($tema['sentimiento']) }}
                                            </span>
                                        </div>
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($tema['frecuencia'] / $datos['total_comentarios']) * 100 }}%">
                                                {{ $tema['frecuencia'] }} menciones
                                            </div>
                                        </div>
                                        <div class="small">
                                            <strong>Ejemplos:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach($tema['ejemplos'] as $ejemplo)
                                                <li class="text-muted">"{{ $ejemplo }}"</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Análisis por Dentista -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="bi bi-people me-2"></i>Análisis por Doctor</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Doctor</th>
                                                    <th class="text-center">Menciones</th>
                                                    <th class="text-center">Score</th>
                                                    <th class="text-center">Positivos</th>
                                                    <th class="text-center">Negativos</th>
                                                    <th>Comentario Destacado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($analisis['analisis_dentistas'] as $dentista)
                                                <tr>
                                                    <td class="fw-bold">{{ $dentista['nombre'] }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary">{{ $dentista['total_menciones'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $dentista['sentimiento_promedio'] >= 70 ? 'success' : ($dentista['sentimiento_promedio'] >= 50 ? 'warning' : 'danger') }} fs-6">
                                                            {{ $dentista['sentimiento_promedio'] }}/100
                                                        </span>
                                                    </td>
                                                    <td class="text-center text-success fw-bold">{{ $dentista['positivos'] }}</td>
                                                    <td class="text-center text-danger fw-bold">{{ $dentista['negativos'] }}</td>
                                                    <td class="small text-muted">"{{ $dentista['comentarios_destacados'][0] ?? 'N/A' }}"</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Análisis por Sucursal -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="bi bi-building me-2"></i>Análisis por clínica</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($analisis['analisis_sucursales'] as $sucursal)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border-2 border-{{ $sucursal['sentimiento_promedio'] >= 70 ? 'success' : ($sucursal['sentimiento_promedio'] >= 50 ? 'warning' : 'danger') }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0">{{ $sucursal['nombre'] }}</h5>
                                                        <span class="badge bg-{{ $sucursal['sentimiento_promedio'] >= 70 ? 'success' : ($sucursal['sentimiento_promedio'] >= 50 ? 'warning' : 'danger') }} fs-5">
                                                            {{ $sucursal['sentimiento_promedio'] }}/100
                                                        </span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong class="text-danger">Temas Críticos:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            @foreach($sucursal['temas_criticos'] as $tema)
                                                            <li>{{ $tema }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <strong class="text-success">Fortalezas:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            @foreach($sucursal['fortalezas'] as $fortaleza)
                                                            <li>{{ $fortaleza }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Citas Representativas -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <div class="card shadow-lg border-0 h-100 bg-success bg-opacity-10">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-star-fill me-2"></i>Mejores Comentarios</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($analisis['citas_representativas']['mejores'] as $cita)
                                    <div class="alert alert-success mb-3">
                                        <i class="bi bi-quote text-success me-2"></i>
                                        <em>{{ $cita }}</em>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-lg border-0 h-100 bg-info bg-opacity-10">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="bi bi-lightbulb-fill me-2"></i>Comentarios Constructivos</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($analisis['citas_representativas']['constructivos'] as $cita)
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-quote text-info me-2"></i>
                                        <em>{{ $cita }}</em>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-lg border-0 h-100 bg-danger bg-opacity-10">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0"><i class="bi bi-emoji-frown me-2"></i>Comentarios Críticos</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($analisis['citas_representativas']['peores'] as $cita)
                                    <div class="alert alert-danger mb-3">
                                        <i class="bi bi-quote text-danger me-2"></i>
                                        <em>{{ $cita }}</em>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Correlación NPS -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="bi bi-graph-up me-2"></i>Correlación NPS vs Sentimiento</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <p class="mb-0">{{ $analisis['correlacion_nps']['insight'] }}</p>
                                    </div>
                                    <div class="row text-center mt-4">
                                        <div class="col-md-4">
                                            <div class="p-4 rounded bg-success bg-opacity-10">
                                                <h6 class="text-success">Promotores (9-10)</h6>
                                                <h2 class="text-success">{{ $analisis['correlacion_nps']['datos']['promotores_sentimiento'] }}/100</h2>
                                                <p class="small text-muted">Score de Sentimiento</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-4 rounded bg-warning bg-opacity-10">
                                                <h6 class="text-warning">Pasivos (7-8)</h6>
                                                <h2 class="text-warning">{{ $analisis['correlacion_nps']['datos']['pasivos_sentimiento'] }}/100</h2>
                                                <p class="small text-muted">Score de Sentimiento</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-4 rounded bg-danger bg-opacity-10">
                                                <h6 class="text-danger">Detractores (0-6)</h6>
                                                <h2 class="text-danger">{{ $analisis['correlacion_nps']['datos']['detractores_sentimiento'] }}/100</h2>
                                                <p class="small text-muted">Score de Sentimiento</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Imprimir Reporte
                </button>
                <!-- <button type="button" class="btn btn-success" onclick="descargarPDF()">
                    <i class="bi bi-file-pdf me-2"></i>Descargar PDF
                </button> -->
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

@media print {
    .modal-header, .modal-footer {
        display: none;
    }
}
</style>

<script>
    // Continuación del script en la vista Blade

    // Gráfico de distribución
    const ctx = document.getElementById('chartDistribucion').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Positivos', 'Neutrales', 'Negativos', 'Mixtos'],
            datasets: [{
                label: 'Distribución de Sentimientos (%)',
                data: [
                    {{ $analisis['resumen_ejecutivo']['distribucion']['positivos'] }},
                    {{ $analisis['resumen_ejecutivo']['distribucion']['neutrales'] }},
                    {{ $analisis['resumen_ejecutivo']['distribucion']['negativos'] }},
                    {{ $analisis['resumen_ejecutivo']['distribucion']['mixtos'] }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ],
                borderColor: [
                    'rgb(40, 167, 69)',
                    'rgb(23, 162, 184)',
                    'rgb(220, 53, 69)',
                    'rgb(255, 193, 7)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    function descargarPDF() {
        window.print();
    }





    // En tu archivo principal JS
async function generarAnalisisSentimientos() {
    // Mostrar loading
    Swal.fire({
        title: 'Analizando Sentimientos...',
        html: '<div class="spinner-border text-primary" role="status"></div><p class="mt-3">Claude AI está procesando los comentarios. Esto puede tomar 1-2 minutos...</p>',
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    try {
        const response = await fetch('/reporteria/ajax/analizar-sentimientos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.close();
            
            // Insertar el HTML en el DOM
            if (!document.getElementById('modalAnalisisSentimientos')) {
                document.body.insertAdjacentHTML('beforeend', data.html);
            }
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalAnalisisSentimientos'));
            modal.show();
            
            // Toast de éxito
            Swal.fire({
                icon: 'success',
                title: 'Análisis Completado',
                text: 'El análisis de sentimientos ha sido generado exitosamente',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al generar el análisis. Por favor intente nuevamente.'
        });
    }
}
</script>