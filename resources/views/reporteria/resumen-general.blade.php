@extends('base.base')
@section('titulo', $titulo = 'Resumen General')
@section('contenido')

@include('navegacion.navbar')

<style>
    .fondo-amarillo {
        background-color: #fbc44c !important;
    }
</style>

<div class="container-fluid page-body-wrapper">
    
    @include('navegacion.sidebar')

    <div class="main-panel">
      <div class="content-wrapper">           
        <div class="card card-rounded">
            <div class="card-body">
                <div>
                    <h4 class="card-title card-title-dash">Resumen General</h4>
                </div>
                <div class="row my-3">
                    <div class="col-lg-4">
                        <label style="font-size:10px;">Mes</label>
                        <select name="" onchange="actualizar_datos($(this).val(), $('#select_sucursal').val());" id="select_mes" class="form-select">
                            <!-- <option value="1">Enero - 2025</option>
                            <option value="2">Febrero - 2025</option>
                            <option value="3">Marzo - 2025</option>
                            <option value="4">Abril - 2025</option>
                            <option value="5">Mayo - 2025</option>
                            <option value="6">Junio - 2025</option>
                            <option value="7">Julio - 2025</option>
                            <option value="8">Agosto - 2025</option>
                            <option value="9" >Septiembre - 2025</option>
                            <option value="10">Octubre - 2025</option> -->
                            <option value="11">Noviembre - 2025</option>
                            <option value="12">Diciembre - 2025</option>
                            <option value="1">Enero - 2026</option>
                            <option value="2">Febrero - 2026</option>
                            <option value="3" >Marzo - 2026</option>
                            <option value="4" selected>Abril - 2026</option>
                           
                            
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label style="font-size:10px;">Sucursal</label>
                        <select name="" onchange="actualizar_datos($('#select_mes').val(), $(this).val());" id="select_sucursal" class="form-select">
                            <option value="0">Todas</option>
                            @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->sucursal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-lg-3 mb-2">
                            <div style="border-radius: 10px;" class="d-flex justify-content-around align-items-center bg-primary text-white p-1">
                                <div class="d-flex justify-content-center">
                                    <div style="font-size: 64px;" class="mdi mdi-forum"></div>
                                </div>
                                <div class="statistics-details d-flex align-items-center justify-content-center">
                                    <div>
                                        <p class="statistics-title mb-0"><b>Respuestas (Mes - Sucursal)</b></p>
                                        <!-- <p class="d-flex mb-0"><span>Mes Actual</span></p> -->
                                        <h3 id="cantidad_respuestas" class="rate-percentage">--</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-2">
                            <div style="border-radius: 10px;" class="d-flex justify-content-around align-items-center bg-info text-white p-1">
                                <div class="d-flex justify-content-center">
                                    <div style="font-size: 64px;" class="mdi mdi-gauge"></div>
                                </div>
                                <div class="statistics-details d-flex align-items-center justify-content-center">
                                    <div>
                                        <p class="statistics-title mb-0"><b>NPS (Mes - Sucursal)</b></p>
                                        <!-- <p class="d-flex mb-0"><span>Mes Actual</span></p> -->
                                        <h3 id="porcentaje_nps" class="rate-percentage">--</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-2">
                            <div style="border-radius: 10px;" class="d-flex justify-content-around align-items-center bg-success text-white p-1">
                                <div class="d-flex justify-content-center">
                                    <div style="font-size: 64px;" class="mdi mdi-comment"></div>
                                </div>
                                <div class="statistics-details d-flex align-items-center justify-content-center">
                                    <div>
                                        <p class="statistics-title mb-0"><b>Comentarios (Mes - Sucursal)</b></p>
                                        <!-- <p class="d-flex mb-0"><span>Mes Actual</span></p> -->
                                        <h3 id="cantidad_comentarios" style="cursor: pointer;" onclick="traer_comentarios($('#select_mes').val(), $('#select_sucursal').val());" class="rate-percentage">--</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-2">
                            <div style="border-radius: 10px;" class="d-flex justify-content-around align-items-center bg-danger text-white p-1">
                                <div class="d-flex justify-content-center">
                                    <div style="font-size: 64px;" class="mdi mdi-emoticon-sad"></div>
                                </div>
                                <div class="statistics-details d-flex align-items-center justify-content-center">
                                    <div>
                                        <p class="statistics-title mb-0"><b>Detractores (Mes - Sucursal)</b></p>
                                        <!-- <p class="d-flex mb-0"><span>Mes Actual</span></p> -->
                                        <h3 id="porcentaje_detractores" class="rate-percentage">--%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-5">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <!-- <div class="card mt-5"> -->
                                        <div id="grafico-promotores"></div>
                                    <!-- </div> -->
                                </div>
                                <div class="col-lg-4">
                                    <!-- <div class="card" > -->
                                        <!-- <div class=""> -->
                                            <h6 class="mb-0 text-center">Distribución de las respuestas NPS (Mes - Sucursal)</h6>
                                        <!-- </div> -->
                                        <!-- <div class="card-body pt-0"> -->
                                            <table class="table text-center">
                                                <thead>
                                                    <tr style="background-color: #b2b2b2 !important;">
                                                        <td></td>
                                                        <td><b>Cantidad</b></td>
                                                        <td><b>Porcentaje</b></td>
                                                    </tr>
                                                </thead>
                                                <tbody id="distribucion"></tbody>
                                            </table>
                                        <!-- </div> -->
                                    <!-- </div> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-5" id="evolutivo_sucursales">

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

<script src="{{ asset('assets/js/grafico-promotores.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        obtener_datos_generales($('#select_mes').val(), $('#select_sucursal').val());
        evolucion_detractores_promotores($('#select_sucursal').val());
        distribucion_respuestas_nps($('#select_mes').val(), $('#select_sucursal').val());
        evolucion_ibb_sucursales($('#select_sucursal').val());
    });
</script>
@endsection