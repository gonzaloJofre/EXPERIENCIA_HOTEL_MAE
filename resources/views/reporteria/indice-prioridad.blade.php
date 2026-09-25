@extends('base.base')
@section('titulo', $titulo = 'Indice Prioridad')
@section('contenido')


@include('navegacion.navbar')

<div class="container-fluid page-body-wrapper">
    
    @include('navegacion.sidebar')

    <div class="main-panel">
      <div class="content-wrapper">           
        <div class="card card-rounded">
            <div class="card-body">
                <div>
                    <h4 class="card-title card-title-dash mb-4">Indice Prioridad</h4>
                </div>
                <div class="text-start">
                    <label style="font-size:10px;">Sucursal</label>
                    <select name="" onchange="cargarPrioridades($(this).val());" id="select_sucursal" class="form-select">
                        <option value="0">Todas</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->sucursal }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-3">
                    <!-- Resumen Ejecutivo (Cards) -->
                    <div id="resumenEjecutivo">
                        <!-- Se llena con JavaScript -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <!-- <span class="sr-only">Cargando...</span> -->
                            </div>
                            <p class="mt-2 text-muted">Cargando datos...</p>
                        </div>
                    </div>
                    
                    
                    
                    <!-- Tabla de Prioridades -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Detalle de Áreas Prioritarias</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div id="tablaPrioridades">
                                        <!-- Se llena con JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de Barras -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div style="height: 500px;">
                                        <canvas id="prioridadChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
      </div>
      <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Equipo de Innovación · Sycar Digital</span>
        </div>
      </footer>
    </div>
  </div>
</div>
@endsection

@section('script')
<!-- higcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>

<script src="{{ asset('assets/js/indice-prioridad.js') }}"></script>
@endsection