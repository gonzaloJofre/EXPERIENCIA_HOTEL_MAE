// async function cargarCuadrantePreguntas() {
//     try {
//         const response = await fetch('/reporteria/ajax/cuadrante-preguntas');
//         const data = await response.json();
        
//         // UMBRALES FIJOS - Ajusta estos valores según tus necesidades
//         const UMBRAL_DESEMPENO = 3.5;  // Punto medio entre 1-5
//         const UMBRAL_IMPORTANCIA = 50; // Punto medio de tu escala de importancia
        
//         // Asegurar que los datos son números
//         const dataParsed = data.map(item => ({
//             ...item,
//             desempeno: parseFloat(item.desempeno),
//             importancia: parseFloat(item.importancia),
//             porcentaje_criticos: parseFloat(item.porcentaje_criticos)
//         }));
        
//         console.log('Primer dato:', dataParsed[0]); // Debug
        
//         // Clasificar en cuadrantes
//         const porCuadrante = {
//             'CRÍTICO - Mejorar': { data: [], color: '#dc3545' },
//             'Fortalecer': { data: [], color: '#28a745' },
//             'Baja Prioridad': { data: [], color: '#ffc107' },
//             'Mantener': { data: [], color: '#17a2b8' }
//         };
        
//         dataParsed.forEach(item => {
//             const punto = {
//                 x: item.desempeno,
//                 y: item.importancia,
//                 label: item.pregunta,
//                 categoria: item.categoria,
//                 criticos: item.porcentaje_criticos,
//                 respuestas: item.total_respuestas
//             };
            
//             // Clasificación basada en umbrales fijos
//             if (punto.x < UMBRAL_DESEMPENO && punto.y > UMBRAL_IMPORTANCIA) {
//                 porCuadrante['CRÍTICO - Mejorar'].data.push(punto);
//             } else if (punto.x >= UMBRAL_DESEMPENO && punto.y > UMBRAL_IMPORTANCIA) {
//                 porCuadrante['Fortalecer'].data.push(punto);
//             } else if (punto.x < UMBRAL_DESEMPENO && punto.y <= UMBRAL_IMPORTANCIA) {
//                 porCuadrante['Baja Prioridad'].data.push(punto);
//             } else {
//                 porCuadrante['Mantener'].data.push(punto);
//             }
//         });
        
//         // Crear datasets
//         const datasets = Object.keys(porCuadrante).map(cuadrante => ({
//             label: cuadrante,
//             data: porCuadrante[cuadrante].data,
//             backgroundColor: porCuadrante[cuadrante].color,
//             borderColor: porCuadrante[cuadrante].color,
//             borderWidth: 2,
//             pointRadius: 8,
//             pointHoverRadius: 12
//         }));
        
//         // Destruir gráfico anterior si existe
//         if (window.cuadranteChartInstance) {
//             window.cuadranteChartInstance.destroy();
//         }
        
//         const ctx = document.getElementById('cuadranteChart').getContext('2d');
//         window.cuadranteChartInstance = new Chart(ctx, {
//             type: 'scatter',
//             data: { datasets },
//             options: {
//                 responsive: true,
//                 maintainAspectRatio: false,
//                 plugins: {
//                     title: {
//                         display: true,
//                         text: 'Matriz de Priorización - Importancia vs Desempeño',
//                         font: { size: 18, weight: 'bold' }
//                     },
//                     subtitle: {
//                         display: true,
//                         text: `Umbrales: Desempeño = ${UMBRAL_DESEMPENO} | Importancia = ${UMBRAL_IMPORTANCIA}`,
//                         font: { size: 12 },
//                         color: '#666'
//                     },
//                     tooltip: {
//                         callbacks: {
//                             label: function(context) {
//                                 const item = context.raw;
//                                 return [
//                                     `Pregunta: ${item.label.substring(0, 60)}...`,
//                                     `Categoría: ${item.categoria}`,
//                                     `Desempeño: ${item.x.toFixed(2)}`,
//                                     `Importancia: ${item.y.toFixed(2)}`,
//                                     `% Críticos: ${item.criticos.toFixed(1)}%`,
//                                     `N: ${item.respuestas}`
//                                 ];
//                             }
//                         }
//                     },
//                     legend: {
//                         position: 'bottom',
//                         labels: {
//                             font: { size: 12 },
//                             padding: 15,
//                             usePointStyle: true
//                         }
//                     }
//                 },
//                 scales: {
//                     x: {
//                         title: {
//                             display: true,
//                             text: 'Desempeño (Promedio 1-5)',
//                             font: { size: 14, weight: 'bold' }
//                         },
//                         min: 1,
//                         max: 5,
//                         ticks: {
//                             stepSize: 0.5
//                         },
//                         grid: {
//                             color: function(context) {
//                                 // Línea divisoria más gruesa
//                                 if (context.tick.value === UMBRAL_DESEMPENO) {
//                                     return '#000';
//                                 }
//                                 return 'rgba(0, 0, 0, 0.1)';
//                             },
//                             lineWidth: function(context) {
//                                 if (context.tick.value === UMBRAL_DESEMPENO) {
//                                     return 3;
//                                 }
//                                 return 1;
//                             }
//                         }
//                     },
//                     y: {
//                         title: {
//                             display: true,
//                             text: 'Importancia (Brecha + % Críticos)',
//                             font: { size: 14, weight: 'bold' }
//                         },
//                         min: 0,
//                         max: 100,
//                         ticks: {
//                             stepSize: 10
//                         },
//                         grid: {
//                             color: function(context) {
//                                 // Línea divisoria más gruesa
//                                 if (context.tick.value === UMBRAL_IMPORTANCIA) {
//                                     return '#000';
//                                 }
//                                 return 'rgba(0, 0, 0, 0.1)';
//                             },
//                             lineWidth: function(context) {
//                                 if (context.tick.value === UMBRAL_IMPORTANCIA) {
//                                     return 3;
//                                 }
//                                 return 1;
//                             }
//                         }
//                     }
//                 }
//             }
//         });
        
//         // Añadir etiquetas de cuadrantes en el canvas
//         agregarEtiquetasCuadrantes(ctx, UMBRAL_DESEMPENO, UMBRAL_IMPORTANCIA);
        
//         // Mostrar tabla de áreas críticas
//         mostrarPreguntasCriticas(porCuadrante['CRÍTICO - Mejorar'].data);
        
//     } catch (error) {
//         console.error('Error cargando cuadrante:', error);
//     }
// }

// function agregarEtiquetasCuadrantes(ctx, umbralX, umbralY) {
//     // Esta función se ejecuta después de renderizar el gráfico
//     const chart = window.cuadranteChartInstance;
    
//     Chart.register({
//         id: 'cuadranteLabels',
//         afterDraw: (chart) => {
//             const ctx = chart.ctx;
//             const xAxis = chart.scales.x;
//             const yAxis = chart.scales.y;
            
//             // Calcular posiciones de los cuadrantes
//             const centerX = xAxis.getPixelForValue(umbralX);
//             const centerY = yAxis.getPixelForValue(umbralY);
            
//             const leftX = (xAxis.left + centerX) / 2;
//             const rightX = (centerX + xAxis.right) / 2;
//             const topY = (yAxis.top + centerY) / 2;
//             const bottomY = (centerY + yAxis.bottom) / 2;
            
//             ctx.save();
//             ctx.font = 'bold 14px Arial';
//             ctx.textAlign = 'center';
//             ctx.textBaseline = 'middle';
            
//             // Cuadrante 1: Bajo desempeño, Alta importancia (CRÍTICO)
//             ctx.fillStyle = 'rgba(220, 53, 69, 0.3)';
//             ctx.fillText('CRÍTICO', leftX, topY - 10);
//             ctx.fillText('Mejorar', leftX, topY + 10);
            
//             // Cuadrante 2: Alto desempeño, Alta importancia (FORTALECER)
//             ctx.fillStyle = 'rgba(40, 167, 69, 0.3)';
//             ctx.fillText('FORTALECER', rightX, topY - 10);
//             ctx.fillText('Mantener', rightX, topY + 10);
            
//             // Cuadrante 3: Bajo desempeño, Baja importancia
//             ctx.fillStyle = 'rgba(255, 193, 7, 0.3)';
//             ctx.fillText('BAJA', leftX, bottomY - 10);
//             ctx.fillText('Prioridad', leftX, bottomY + 10);
            
//             // Cuadrante 4: Alto desempeño, Baja importancia
//             ctx.fillStyle = 'rgba(23, 162, 184, 0.3)';
//             ctx.fillText('MANTENER', rightX, bottomY);
            
//             ctx.restore();
//         }
//     });
// }

// function mostrarPreguntasCriticas(criticas) {
//     const tbody = document.getElementById('preguntasCriticas');
//     tbody.innerHTML = '';
    
//     if (criticas.length === 0) {
//         tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay áreas críticas</td></tr>';
//         return;
//     }
    
//     // Ordenar por importancia
//     criticas.sort((a, b) => b.y - a.y);
    
//     criticas.forEach(punto => {
//         const urgencia = punto.y > 60 ? 5 : punto.y > 55 ? 4 : 3;
//         const row = document.createElement('tr');
        
//         row.innerHTML = `
//             <td>
//                 <span class="badge bg-danger">Nivel ${urgencia}</span>
//             </td>
//             <td class="fw-medium">${punto.label}</td>
//             <td>${punto.categoria}</td>
//             <td class="text-center">
//                 <span class="badge bg-danger">${punto.x.toFixed(2)}</span>
//             </td>
//             <td class="text-center">${punto.y.toFixed(1)}</td>
//             <td class="text-center">${punto.criticos.toFixed(1)}%</td>
//             <td class="text-center">${punto.respuestas}</td>
//         `;
//         tbody.appendChild(row);
//     });
// }

// cargarCuadrantePreguntas();







async function cargarPrioridades(sucursal) {
    try {
        const response = await fetch(`/reporteria/ajax/cuadrante-preguntas?sucursal=${sucursal}`);
        const data = await response.json();
        
        console.log('Datos cargados:', data.length, 'preguntas');
        
        // Renderizar componentes
        renderizarResumenEjecutivo(data);
        renderizarTablaPrioridades(data);
        renderizarGraficoBarras(data);
        
    } catch (error) {
        console.error('Error cargando prioridades:', error);
        mostrarError();
    }
}

// ============================================
// 1. RESUMEN EJECUTIVO (Cards con métricas)
// ============================================
function renderizarResumenEjecutivo(data) {
    const urgentes = data.filter(d => d.nivel_prioridad === 'URGENTE').length;
    const altas = data.filter(d => d.nivel_prioridad === 'ALTA').length;
    const promedioGeneral = (data.reduce((sum, d) => sum + d.promedio_desempeno, 0) / data.length).toFixed(1);
    const promedioCriticos = (data.reduce((sum, d) => sum + d.pct_criticos, 0) / data.length).toFixed(1);
    
    const html = `
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title text-white">Urgentes</h5>
                        <h2 class="display-4">${urgentes}</h2>
                        <p class="mb-0">Requieren acción inmediata</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title text-white">Prioridad Alta</h5>
                        <h2 class="display-4">${altas}</h2>
                        <p class="mb-0">Planificar mejoras pronto</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title text-white">Promedio</h5>
                        <h2 class="display-4">${promedioGeneral}</h2>
                        <p class="mb-0">Promedio general</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h5 class="card-title text-white">% Críticos</h5>
                        <h2 class="display-4">${promedioCriticos}%</h2>
                        <p class="mb-0">Respuestas muy negativas</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('resumenEjecutivo').innerHTML = html;
}

// ============================================
// 2. TABLA DE PRIORIDADES (Top 15)
// ============================================
function renderizarTablaPrioridades(data) {
    const top15 = data.slice(0, 15);
    
    const rows = top15.map((item, index) => {
        const badgeClass = {
            'URGENTE': 'text-danger',
            'ALTA': 'text-warning',
            'MEDIA': 'text-info',
            'BAJA': 'text-success'
        }[item.nivel_prioridad] || 'badge-secondary';
        
        const iconoPrioridad = {
            'URGENTE': '🔴',
            'ALTA': '🟠',
            'MEDIA': '🟡',
            'BAJA': '🟢'
        }[item.nivel_prioridad] || '⚪';
        
        return `
            <tr>
                <td class="text-center"><strong>${index + 1}</strong></td>
                <td>
                    <strong>${item.pregunta}</strong>
                    <br>
                    <small class="text-muted">${item.categoria}</small>
                </td>
                <td class="text-center">
                    <span class="${badgeClass} px-3 py-2">
                         ${item.nivel_prioridad}
                    </span>
                </td>
                <td class="text-center">
                    <strong style="font-size: 1.2em; color: ${getColorIndice(item.indice_prioridad)}">
                        ${item.indice_prioridad}
                    </strong>
                </td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="mr-2">${item.promedio_desempeno}</span>&nbsp;
                        ${getBarraProgreso(item.promedio_desempeno)}
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge badge-${item.pct_criticos > 30 ? 'danger' : item.pct_criticos > 15 ? 'warning' : 'info'}">
                        ${item.pct_criticos}%
                    </span>
                </td>
                <td class="text-center text-muted">
                    <small>n=${item.total_respuestas}</small>
                </td>
            </tr>
        `;
    }).join('');
    
    const html = `
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>Pregunta / Categoría</th>
                        <th class="text-center" width="150">Prioridad</th>
                        <th class="text-center" width="100">Índice</th>
                        <th class="text-center" width="150">Promedio</th>
                        <th class="text-center" width="100">% Críticos</th>
                        <th class="text-center" width="80">N</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
        
        <div class="alert alert-info mt-3">
            <strong>Cómo interpretar:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Índice de Prioridad:</strong> 0-100 (más alto = más urgente)</li>
                <li><strong>Promedio:</strong> Promedio de satisfacción 1-5</li>
                <li><strong>% Críticos:</strong> Porcentaje de respuestas muy negativas (1-2)</li>
            </ul>
        </div>
    `;
    
    document.getElementById('tablaPrioridades').innerHTML = html;
}

// ============================================
// 3. GRÁFICO DE BARRAS HORIZONTALES
// ============================================
function renderizarGraficoBarras(data) {
    const top10 = data.slice(0, 10);
    
    // Destruir gráfico anterior si existe
    if (window.prioridadChartInstance) {
        window.prioridadChartInstance.destroy();
    }
    
    const colores = top10.map(d => {
        if (d.nivel_prioridad === 'URGENTE') return '#dc3545';
        if (d.nivel_prioridad === 'ALTA') return '#ffc107';
        if (d.nivel_prioridad === 'MEDIA') return '#17a2b8';
        return '#28a745';
    });
    
    const ctx = document.getElementById('prioridadChart').getContext('2d');
    window.prioridadChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top10.map(d => truncarTexto(d.pregunta, 50)),
            datasets: [{
                label: 'Índice de Prioridad',
                data: top10.map(d => d.indice_prioridad),
                backgroundColor: colores,
                borderColor: colores.map(c => c),
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Top 10 Áreas Prioritarias',
                    font: { size: 18, weight: 'bold' }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const item = top10[context.dataIndex];
                            return [
                                `Índice: ${item.indice_prioridad}`,
                                `Promedio: ${item.promedio_desempeno}/5.0`,
                                `% Críticos: ${item.pct_criticos}%`,
                                `Categoría: ${item.categoria}`
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: function(context) {
                            if (context.tick.value === 70 || context.tick.value === 50 || context.tick.value === 30) {
                                return 'rgba(0,0,0,0.3)';
                            }
                            return 'rgba(0,0,0,0.1)';
                        },
                        lineWidth: function(context) {
                            if (context.tick.value === 70 || context.tick.value === 50 || context.tick.value === 30) {
                                return 2;
                            }
                            return 1;
                        }
                    },
                    title: {
                        display: true,
                        text: 'Índice de Prioridad (0-100)',
                        font: { weight: 'bold' }
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================
function getColorIndice(indice) {
    if (indice >= 70) return '#dc3545';
    if (indice >= 50) return '#ffc107';
    if (indice >= 30) return '#17a2b8';
    return '#28a745';
}

function getBarraProgreso(valor) {
    const porcentaje = (valor / 5) * 100;
    let colorClass = 'bg-danger';
    if (valor >= 4) colorClass = 'bg-success';
    else if (valor >= 3.5) colorClass = 'bg-warning';
    
    return `
        <div class="progress" style="width: 80px; height: 20px;">
            <div class="progress-bar ${colorClass}" 
                 role="progressbar" 
                 style="width: ${porcentaje}%">
            </div>
        </div>
    `;
}

function truncarTexto(texto, max) {
    return texto.length > max ? texto.substring(0, max) + '...' : texto;
}

function mostrarError() {
    document.getElementById('resumenEjecutivo').innerHTML = `
        <div class="alert alert-danger">
            <strong>Error:</strong> No se pudieron cargar los datos. 
            Por favor, intenta de nuevo.
        </div>
    `;
}

// ============================================
// INICIALIZAR
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    cargarPrioridades($('#select_sucursal').val());
});