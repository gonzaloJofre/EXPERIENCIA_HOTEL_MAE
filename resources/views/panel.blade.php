@extends('base.base')
@section('titulo', $titulo = 'Panel')
@section('contenido')

@include('navegacion.navbar')

<div class="container-fluid page-body-wrapper">
    @include('navegacion.sidebar')

    <div class="main-panel">
      <div class="content-wrapper">           
        <div class="card card-rounded">
            <div class="card-body">
                <div>
                    <h4 class="card-title card-title-dash">Panel General</h4>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="card mt-3 h-100">
                                <div class="card-header">
                                    <h4 class="card-title mb-0 py-3">Centros Disponibles:</h4>
                                </div>
                                <div class="card-body">
                                    @foreach($sucursales as $sucursal)
                                    <a href="" class="btn btn-success w-100 mb-2 text-start">{{ $sucursal->sucursal }} - {{ $empresa_seleccionada }}</a>
                                    <!-- <a href="" class="btn btn-success w-100 mb-2 text-start">Alcántara - Centro Odontológico Padre Mariano</a> -->
                                    <!-- <a href="" class="btn btn-success w-100 mb-2 text-start">Burgos - Centro Odontológico Padre Mariano</a>
                                    <a href="" class="btn btn-success w-100 mb-2 text-start">Tenderini - Centro Odontológico Padre Mariano</a>
                                    <a href="" class="btn btn-success w-100 mb-2 text-start">Alameda - Centro Odontológico Padre Mariano</a>
                                    <a href="" class="btn btn-success w-100 mb-2 text-start">Providencia - Centro Odontológico Padre Mariano</a> -->
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @foreach($sucursales as $sucursal)
                            <div class="col-md-4 mb-4">
                                <div class="card mt-3">
                                    <div class="card-body p-1">
                                        <div id="grafico-{{ $sucursal->id_sucursal }}" style="width: 100%; height: 400px;"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!-- <div class="col-lg-4">
                            <div class="card mt-3">
                                <div class="card-body p-1">
                                    <div id="grafico"></div>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="col-lg-4">
                            <div class="card mt-3">
                                <div class="card-body p-1">
                                    <div id="grafico-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mt-3">
                                <div class="card-body p-1">
                                    <div id="grafico-3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mt-3">
                                <div class="card-body p-1">
                                    <div id="grafico-4"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card mt-3">
                                <div class="card-body p-1">
                                    <div id="grafico-5"></div>
                                </div>
                            </div>
                        </div> -->
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
<script src="{{ asset('assets/js/graficos.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const sucursales = @json($sucursales);

        for(let i = 0; i < sucursales.length; i++) {
            console.log(sucursales[i].id_sucursal);  
            cargarSeries(sucursales[i].id_sucursal,sucursales[i].sucursal)
        }


        async function cargarSeries(id_sucursal, sucursal) {
            try {
                const response = await fetch(`/reporteria/ajax/nps-anual?id_sucursal=${id_sucursal}`);
                if (!response.ok) throw new Error("Error en la petición");
                
                let series = await response.json();

                // asegurar que los datos sean numéricos
                series = series.map(s => ({
                    ...s,
                    data: s.data.map(v => Number(v))
                }));

                console.log("Series cargadas:", series);

                generar_grafico(series, id_sucursal, sucursal);
            } catch (error) {
                console.error("Error cargando series:", error);
            }
        }
    });
</script>
@endsection