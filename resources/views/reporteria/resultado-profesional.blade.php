@extends('base.base')
@section('titulo', $titulo = 'Resultados Profesional')
@section('contenido')

@include('navegacion.navbar')

<div class="container-fluid page-body-wrapper">
    
    @include('navegacion.sidebar')

    <div class="main-panel">
      <div class="content-wrapper">           
        <div class="card card-rounded">
            <div class="card-body">
                <div>
                    <h4 class="card-title card-title-dash">Resultados por Profesional</h4>
                </div>
                <div class=" mt-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <label style="font-size:10px;">Sucursal</label>
                            <select name="" onchange="evolucion_ibb_sucursales($(this).val());" id="select_sucursal" class="form-select">
                                <option value="0">Todas</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->sucursal }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                  <div class="card mt-3 shadow-sm">
                    <div class="card-body my-4">
                        <h6 class="card-title">Leyenda de Evaluación</h6>
                        <div class="row g-2">
                            <div class="col-auto">
                                <span class="badge bg-success">4.5-5.0</span> Excelente
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-success bg-opacity-75">4.0-4.4</span> Muy Bueno
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-success bg-opacity-50 text-dark">3.5-3.9</span> Bueno
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-warning text-dark">3.0-3.4</span> Aceptable
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-warning bg-opacity-75 text-dark">2.5-2.9</span> Regular
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-danger bg-opacity-50">2.0-2.4</span> Malo
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-danger">1.0-1.9</span> Muy Malo
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                          <table class="table align-middle mb-0">
                              <thead class="table-dark">
                                  <tr>
                                      <th style="width: 60px;">ID</th>
                                      <th style="min-width: 150px;">Doctor</th>
                                      <th style="min-width: 120px;">Clínica</th>
                                      <th style="min-width: 120px;">Especialidad</th>
                                      <th class="text-center" style="width: 90px;">
                                          <small>Respuestas</small>
                                      </th>
                                      <th class="text-center" style="width: 100px;">
                                          <small>Comentarios</small>
                                      </th>
                                      <th class="text-center" style="width: 100px;">
                                          <small>Tiempo<br>Espera</small>
                                      </th>
                                      <th class="text-center" style="width: 100px;">
                                          <small>Amabilidad <br> y Trato</small>
                                      </th>
                                      <th class="text-center" style="width: 100px;">
                                          <small>Claridad <br> Explicación </small>
                                      </th>
                                      <th class="text-center" style="width: 90px;">
                                          <small>NPS</small>
                                      </th>
                                  </tr>
                              </thead>
                              <tbody id="dentistas">
                                  <!-- Los datos se cargarán aquí -->
                              </tbody>
                          </table>
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

<div class="modal fade" id="modal_comentario_periodo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Comentarios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-striped datatable">
                <thead>
                    <tr>
                        <th>Comentario</th>
                        <th>Dentista</th>
                        <th>Sucursal</th>
                        <th>Fecha Envío</th>
                    </tr>
                </thead>
                <tbody id="tbody_comentarios_periodo"></tbody>
            </table>
        </div>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
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

<script src="{{ asset('assets/js/reporte-dentistas.js') }}"></script>
@endsection