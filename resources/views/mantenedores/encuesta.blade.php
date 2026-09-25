@extends('base.base')
@section('titulo', $titulo = 'Encuestas')
@section('contenido')

@include('navegacion.navbar')

<div class="container-fluid page-body-wrapper">
    
    @include('navegacion.sidebar')

    <div class="main-panel">
      <div class="content-wrapper">           
        <div class="card card-rounded">
            <div class="card-body">
                <h4 class="card-title">Encuestas</h4>
                <div class="d-flex justify-content-between">
                    <p class="card-description">
                        Encuestas - <code> {{ $empresa_seleccionada }}</code>
                    </p>
                    <button class="btn btn-primary" data-bs-target="#modal_crear_encuesta" data-bs-toggle="modal"><i class="mdi mdi-plus"></i> Crear Encuesta</button>
                </div>
                <div class="table-responsive mt-5">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                            <th>Encuesta</th>
                            <th>Creador</th>
                            <!-- <th>Tipo</th> -->
                            <th>Fecha Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($encuestas as $encuesta)
                           <tr>
                            <td>{{ $encuesta->encuesta }}</td>
                            <td>{{ $encuesta->usuario->nombres }} {{ $encuesta->usuario->ape_paterno }}</td>
                            <td>{{ $encuesta->fecha_creacion }}</td>
                            <!-- <td>{{ $encuesta->encuesta }}</td> -->
                           </tr>
                           @endforeach
                        </tbody>
                    </table>
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

<script src="{{ asset('assets/js/graficos.js') }}"></script>
<script src="{{ asset('assets/js/encuesta.js') }}"></script>

@endsection

@section('modal')
@include('modal.encuesta.crear_encuesta')
@endsection