@extends('base.base')
@section('titulo', $titulo = 'Análisis de Sentimientos')
@section('contenido')

@section('link')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js">
@endsection

@include('navegacion.navbar')

<div class="container-fluid page-body-wrapper">
    @include('navegacion.sidebar')
    <div class="main-panel">
      <div class="content-wrapper">           
        <div class="card card-rounded">
            <div class="card-body">
                <div>
                    <h4 class="card-title card-title-dash mb-4">Análisis de Sentimientos</h4>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <label style="font-size:10px;">Sucursal</label>
                        <select id="select_sucursal" class="form-select">
                            <option value="0">Todas</option>
                            @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->sucursal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label style="font-size:10px;">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label style="font-size:10px;">Fecha Fin</label>
                        <input type="date" id="fecha_fin" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <div class="container-fluid py-4 px-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-lg">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-brain display-1 text-primary mb-4"></i>
                                        <h2 class="mb-3">Análisis de Sentimientos con IA</h2>
                                        <p class="text-muted mb-4">
                                            Utiliza Inteligencia Artificial para analizar automáticamente todos los comentarios<br>
                                            de pacientes y generar insights accionables
                                        </p>
                                        
                                        <button class="btn btn-primary btn-lg px-5" onclick="generarAnalisisSentimientos($('#select_sucursal').val(),$('#fecha_inicio').val(), $('#fecha_fin').val());">
                                            <i class="bi bi-stars me-2"></i>
                                            Generar Análisis con Inteligencia Artificial
                                        </button>
                                        
                                        <div class="mt-4">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                El análisis puede tomar 1-2 minutos dependiendo de la cantidad de comentarios
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>
<script>
async function generarAnalisisSentimientos(sucursal, fecha_inicio, fecha_fin) {
    // Mostrar loading
    Swal.fire({
        title: 'Analizando Sentimientos...',
        html: `
            <div class="d-flex flex-column align-items-center">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                <p class="mt-3">Claude AI está procesando los comentarios...</p>
                <small class="text-muted">Esto puede tomar 1-2 minutos</small>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    if(fecha_inicio != '' && fecha_fin != '') {
        var link = `/reporteria/ajax/analisis-sentimientos?sucursal=${sucursal}&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`;
    } else {
        var link = `/reporteria/ajax/analisis-sentimientos?sucursal=${sucursal}`;
    }
    

    try {

        const response = await fetch(link, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const html = await response.text();

        // Insertar el HTML
        document.body.insertAdjacentHTML('beforeend', html);

        // Mostrar modal
        Swal.close();
        const modal = new bootstrap.Modal(document.getElementById('modalAnalisisSentimientos'));
        modal.show();
        
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al comunicarse con el servidor. Por favor intente nuevamente.'
        });
    }
}
</script>
@endsection