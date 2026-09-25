@extends('base.base')
@section('titulo', $titulo = 'Detalle Encuesta')
@section('contenido')

@section('link')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js">
<style>
    .detalle-encuesta {
        background: #02b3b1;
        min-height: 100px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .detalle-encuesta::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,128C1248,117,1344,107,1392,101.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
    }
    
    .header-content {
        position: relative;
        z-index: 1;
        padding: 2rem;
        color: white;
    }
    
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .info-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .info-card.cita-card {
        border-left-color: #02b3b1;
    }
    
    .info-card.contacto-card {
        border-left-color: #02b3b1;
    }
    
    .info-card.respuesta-card {
        border-left-color: #02b3b1;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
    
    .info-item {
        margin-bottom: 1.25rem;
    }
    
    .info-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.4rem;
    }
    
    .info-value {
        font-size: 1rem;
        color: #2d3748;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        gap: 0.5rem;
    }
    
    .status-badge.enviado {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .status-badge.pendiente {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .respuesta-item {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .respuesta-item::before {
        content: '"';
        position: absolute;
        top: -10px;
        left: 10px;
        font-size: 4rem;
        color: rgba(102, 126, 234, 0.15);
        font-family: Georgia, serif;
    }
    
    .pregunta-text {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        position: relative;
        z-index: 1;
    }
    
    .respuesta-text {
        color: #4a5568;
        font-size: 1rem;
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }
    
    .divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        margin: 2rem 0;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .detalle-encuesta {
            min-height: 150px;
        }
        
        .header-content {
            padding: 1.5rem;
        }
        
        .info-card {
            padding: 1rem;
        }
    }
</style>
@endsection

@include('navegacion.navbar')

<div class="container-fluid page-body-wrapper">
    @include('navegacion.sidebar')
    <div class="main-panel">
      
        <div class="content-wrapper">           
            <div class="card card-rounded border-0 shadow-sm">
                <div class="detalle-encuesta">
                    <div class="header-content">
                        <h4 class="mb-2" style="font-weight: 700;">Detalle de Encuesta</h4>
                        <p class="mb-0" style="opacity: 0.9;">Información completa de la encuesta y respuestas del paciente</p>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    
                    <!-- Datos de la Cita -->
                    <div class="info-card cita-card">
                        <h6 class="section-title">Datos de la Cita</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Rut Paciente</div>
                                    <div class="info-value">{{ $datos_encuesta->rut }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Nombre Paciente</div>
                                    <div class="info-value">{{ $datos_encuesta->nombre_paciente }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Clínica</div>
                                    <div class="info-value">{{ $datos_encuesta->sucursal }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">Empresa</div>
                                    <div class="info-value"></div>
                                </div>
                            </div> -->
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Nombre Dentista</div>
                                    <div class="info-value">{{ $datos_encuesta->nombre_dentista }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Especialidad</div>
                                    <div class="info-value">{{ $datos_encuesta->especialidad }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Fecha y Hora de Cita</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($datos_encuesta->fecha_cita)->format('d-m-Y') }} 
                                        {{ \Carbon\Carbon::parse($datos_encuesta->hora_cita)->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <div class="info-label">Fecha de Envío</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($datos_encuesta->fecha_envio)->format('d-m-Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <!-- Datos de Contactabilidad -->
                    <div class="info-card contacto-card">
                        <h6 class="section-title">Datos de Contactabilidad</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">Correo Electrónico</div>
                                    <div class="info-value">{{ $datos_encuesta->email }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">Teléfono Celular</div>
                                    <div class="info-value">+569{{ $datos_encuesta->celular }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <!-- Detalle de la Encuesta -->
                    <div class="info-card respuesta-card">
                        <h6 class="section-title">Respuestas de la Encuesta</h6>
                        
                        @if(isset($respuestas) && !empty($respuestas))
                            @foreach($respuestas as $index => $respuesta)
                                <div class="respuesta-item">
                                    <div class="pregunta-text">
                                        {{ $index + 1 }}. {{ $respuesta->preguntas->pregunta }}
                                    </div>
                                    <div class="respuesta-text">
                                        {{ $respuesta->respuesta }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <h6 style="color: #4a5568; font-weight: 600;">No hay respuestas registradas</h6>
                                <p style="color: #718096; margin-bottom: 0;">Esta encuesta aún no ha sido completada por el paciente.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>
<script>

</script>
@endsection