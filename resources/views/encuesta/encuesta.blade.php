@extends('base.base')

@section('titulo', $titulo = 'Encuesta')

@section('link')
    <link rel="stylesheet" href="{{ asset('assets/css/encuesta.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nps.css') }}">
@endsection

@section('contenido')

<div class="main-panel w-100">
    <div class="d-flex justify-content-center w-100 bg-light">
        <div class="card card-rounded bg-white mb-5 encuesta-container">
            <div class="container px-0">
                {{-- Header logo Hotel MAE --}}
                <div class="encuesta-header-logo">
                    <div class="encuesta-header-linea"></div>
                    <img src="{{ asset('assets/images/mae/logo_mae_azul.png') }}" alt="Hotel MAE" class="encuesta-logo">
                    <div class="encuesta-header-linea"></div>
                </div>
                <form id="form-encuesta" method="POST">
                    @csrf
                    <div class="card-body encuesta-body">
                        {{-- Bienvenida y elección de secciones --}}
                        @php
                            $descripcionesCategorias = [
                                'Reserva y llegada'          => 'Esta sección evalúa la facilidad para gestionar su hospedaje y las primeras impresiones al llegar.',
                                'Habitación'                 => 'Esta sección evalúa la comodidad, limpieza y calidad de su habitación durante su estadía.',  
                                'Instalaciones'              => 'Esta sección evalúa la calidad de las instalaciones de Hotel MAE.',
                                'Restaurante, Bar & Quincho' => 'Esta sección evalúa la calidad de la atención, el servicio y su experiencia en nuestros espacios gastronómicos.',
                                'Atención del equipo'        => 'Evalúe la amabilidad, disposición y calidad de la atención de nuestro equipo.',
                                'Spa / Masajes'              => 'Esta sección evalúa la calidad del servicio, atención y experiencia en Spa y Masajes.',
                                'Eventos'                    => 'Evalúe la atención, organización y experiencia durante eventos realizados en Hotel MAE.',
                                'Recomendación NPS'          => 'Cuéntenos qué tan probable es que recomiende Hotel MAE a familiares, amigos o conocidos.',
                                'Oportunidades de mejora'    => 'Cuéntenos qué podríamos mejorar para ofrecerle una mejor experiencia.',
                                'Comentarios'                => 'Comparta cualquier comentario o sugerencia que quiera dejarnos sobre su estadía.',
                                'Información general'        => 'Ayúdenos a conocer algunos detalles sobre su estadía y visita a Hotel MAE.',
                                'Evaluación general'         => 'Evalúe de manera general el servicio en su estadía en el Hotel',
                                'Fidelización'               => 'Cuéntenos si volvería a elegir Hotel MAE en una próxima estadía.',
                                'Lo que más disfrutaste'     => 'Cuéntenos qué fue lo que más disfrutó de su experiencia en Hotel MAE.',
                            ];
                        @endphp 
                        <div id="pantalla-seleccion">
                            <div class="encuesta-header">
                                <div class="encuesta-bienvenida">
                                    <div class="encuesta-icono">
                                        <i class="mdi mdi-home-modern"></i>
                                    </div>
                                    <span class="encuesta-subtitulo">
                                        <b>HOTEL MAE</b>
                                    </span>
                                    <h2 class="encuesta-titulo">
                                        Encuesta de satisfacción
                                    </h2>
                                    <p class="encuesta-descripcion">
                                        Queremos conocer cómo fue tu experiencia para seguir ofreciendo un servicio de excelencia.
                                    </p>
                                </div>
                            </div>
                            <div class="seleccion-secciones">
                                <h4 class="seleccion-titulo">
                                    ¿Qué deseas responder?
                                </h4>
                                <p class="seleccion-descripcion">
                                    Puedes responder toda la encuesta o únicamente las áreas que utilizaste durante tu estadía.
                                </p>
                                {{-- Encuesta completa --}}
                                <div class="seleccion-opciones">
                                    <label class="opcion-seccion opcion-principal seleccionada">
                                        <input type="radio" name="modo_encuesta" value="todas" checked>                                       
                                        <span class="opcion-contenido">
                                            <span class="opcion-icono">
                                                <i class="mdi mdi-check-all"></i>
                                            </span>
                                            <span class="opcion-texto">
                                                <strong>
                                                    Experiencia completa
                                                </strong>
                                                <small>
                                                    Evaluar todos los aspectos de mi estadía
                                                </small>
                                            </span>
                                        </span>
                                    </label>

                                    {{-- Categorías --}}
                                    @foreach($preguntas->pluck('nombre_categoria')->unique() as $categoria)
                                        <label class="opcion-seccion">
                                            <input type="checkbox" class="categoria-check" value="{{ $categoria }}">
                                            <span class="opcion-contenido">
                                                <span class="opcion-icono">
                                                    <i class="mdi mdi-star-outline"></i>
                                                </span>
                                                <span class="opcion-texto">
                                                    <strong>
                                                        {{ $categoria }}
                                                    </strong>
                                                    <small>
                                                        {{ $descripcionesCategorias[$categoria] ?? 'Cuéntenos sobre su experiencia en Hotel MAE.' }}
                                                    </small>
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="text-center mt-4">
                                    <button type="button" id="btn-comenzar" class="btn btn-primary px-5 py-2">
                                        Comenzar encuesta
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Encuesta - oculta --}}
                        <div id="contenedor-encuesta" style="display:none;">
                            {{-- Barra de progreso --}}
                            <div class="encuesta-progreso">
                                <div class="encuesta-progreso-fondo">
                                    <div id="progreso-barra" class="encuesta-progreso-barra" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                    </div>
                                </div>
                            </div>
                            {{-- Secciones --}}
                            @php
                                $categoriaActual = null;
                                $numeroPregunta = 0;
                            @endphp

                            @foreach($preguntas as $pregunta)
                                @php
                                    $nombreCategoria = $pregunta->nombre_categoria;
                                @endphp

                                @if($nombreCategoria !== $categoriaActual)
                                    @if($categoriaActual !== null)
                                        </div>
                                        </div>
                                    @endif

                                    @php
                                        $categoriaActual = $nombreCategoria;
                                        $descripcionCategoria = $descripcionesCategorias[$nombreCategoria]
                                            ?? 'Cuéntenos sobre su experiencia en Hotel MAE.';
                                    @endphp

                                    <div class="seccion-encuesta" data-categoria="{{ $nombreCategoria }}" style="display:none;">
                                        <div class="seccion-header">
                                            <h3 class="seccion-titulo">
                                                {{ $nombreCategoria }}
                                            </h3>
                                            <p class="seccion-descripcion">
                                                {{ $descripcionCategoria }}
                                            </p>
                                        </div>
                                        <div class="seccion-contenido">
                                @endif

                                @php
                                    $numeroPregunta++;
                                @endphp
                                
                                {{-- Preguntas --}}
                                <div class="nps-form pregunta-encuesta" style="text-align: justify;">
                                    <div class="pregunta-titulo">
                                        {{ $numeroPregunta }}. - {{ $pregunta->pregunta }}
                                    </div>
                                    <div class="pregunta-respuesta">
                                        {!! str_replace(['__INDEX__', '__ID__'], [$numeroPregunta, $pregunta->id_pregunta], $pregunta->tipo_pregunta->etiqueta) !!}
                                    </div>
                                </div>
                            @endforeach

                            {{-- Cerrar la sección --}}
                            @if($categoriaActual !== null)
                                </div>
                                </div>
                            @endif

                            {{-- navegación --}}
                            <div class="encuesta-navegacion">
                                <button type="button" id="btn-anterior" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i>
                                    Anterior
                                </button>
                                <button type="button" id="btn-continuar" class="btn btn-mae-continuar">
                                    Continuar
                                    <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                                <button id="btn-enviar" type="submit" class="btn btn-mae-enviar" style="display:none;">
                                    <i class="mdi mdi-creation ms-1"></i>&nbsp;&nbsp;
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="spinner"></span>
                                    Enviar Encuesta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Footr --}}
<footer class="encuesta-footer">
    <div class="encuesta-footer-linea"></div>
    <div class="encuesta-footer-contenido">
        <img src="{{ asset('assets/images/mae/logo_mae_azul.png') }}" alt="Hotel MAE" class="encuesta-footer-logo">
        <div class="encuesta-footer-texto">
            Copyright © 2026 Hotel MAE
        </div>
    </div>
</footer>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>

<script src="{{ asset('assets/js/validador.js') }}"></script>


<script>

document.addEventListener('DOMContentLoaded', function(){

    const pantallaSeleccion = document.getElementById('pantalla-seleccion');
    const btnComenzar = document.getElementById('btn-comenzar');
    const contenedorEncuesta =  document.getElementById('contenedor-encuesta');
    const seccionesEncuesta = document.querySelectorAll('.seccion-encuesta');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnContinuar = document.getElementById('btn-continuar');
    const btnEnviar = document.getElementById('btn-enviar');
    const progresoBarra = document.getElementById('progreso-barra');

    // Selección de áreas
    const opcionesSeccion = document.querySelectorAll('.opcion-seccion');
    const opcionTodas = document.querySelector('.opcion-principal');
    const inputTodas = opcionTodas.querySelector('input');
    const opcionesCategorias = document.querySelectorAll('.categoria-check');


    opcionesSeccion.forEach(function(opcion){

        const input = opcion.querySelector('input');

        opcion.addEventListener('click', function(e){

            // Encuesta completa de experiencia 
            if(input === inputTodas){

                inputTodas.checked = true;
                opcionTodas.classList.add('seleccionada');

                opcionesCategorias.forEach(function(checkbox){
                    checkbox.checked = false;
                    checkbox.closest('.opcion-seccion').classList.remove('seleccionada');
                });
                return;
            }

            // Categ individuales 
            if(input.type === 'checkbox'){

                input.checked = !input.checked;

                if(input.checked){
                    opcion.classList.add('seleccionada');

                    //Se crea una condicional.. si se slecciona otra categ. pierde el foco el contestar toda la encuesta
                    inputTodas.checked = false;
                    opcionTodas.classList.remove('seleccionada');
                }else{
                    opcion.classList.remove('seleccionada');
                }

                // Si no se selecciona otra sección pasará a contestar todas las secciones
                let algunaSeleccionada = false;

                opcionesCategorias.forEach(function(checkbox){
                    if(checkbox.checked){
                        algunaSeleccionada = true;
                    }
                });

                if(!algunaSeleccionada){
                    inputTodas.checked = true;
                    opcionTodas.classList.add('seleccionada');
                }
            }
        });
    });


    //Variables para la navegación 
    let seccionesSeleccionadas = [];
    let seccionActual = 0;

    // Comenzar encuesta
    btnComenzar.addEventListener('click', function(){

        seccionesSeleccionadas = [];

        if(inputTodas.checked){
            seccionesEncuesta.forEach(function(seccion){
                seccionesSeleccionadas.push(seccion);
            });
        }
        else{
            seccionesEncuesta.forEach(function(seccion){
                const categoria = seccion.dataset.categoria;
                const checkbox = document.querySelector('.categoria-check[value="' + CSS.escape(categoria) + '"]');

                if(checkbox && checkbox.checked){
                    seccionesSeleccionadas.push(seccion);
                }
            });
        }

        // validación 
        if(seccionesSeleccionadas.length === 0){

            Swal.fire({
                icon:'warning',
                title:'Selecciona una sección',
                text:'Debes seleccionar al menos un área para comenzar.'
            });
            return;
        }

        // Ocultar selección
        pantallaSeleccion.style.display = 'none';

        //Mostrar encuesta
        contenedorEncuesta.style.display = 'block';

        seccionActual = 0;
        mostrarSeccion();

    });

    
    // Mostrar sección escogida
    function mostrarSeccion(){

        // Ocultamos todas las secciones.
        seccionesEncuesta.forEach(function(seccion){
            seccion.style.display = 'none';
        });

        // Obtenemos la sección actual.
        const seccion = seccionesSeleccionadas[seccionActual];

        if(!seccion){
            return;
        }

        seccion.style.display = 'block';

        //Progreso
        const total = seccionesSeleccionadas.length;
        const progreso = ((seccionActual + 1) / total) * 100;

        progresoBarra.style.width = progreso + '%';
        progresoBarra.setAttribute('aria-valuenow', progreso);

        //Btn anterior
        if(seccionActual === 0){
            btnAnterior.style.display = 'none';
        }else{
            btnAnterior.style.display = 'inline-block';
        }

        // última seccion
        if(seccionActual === total - 1){

            btnContinuar.style.display = 'none';
            btnEnviar.style.display = 'inline-block';

        }else{

            btnContinuar.style.display = 'inline-block';
            btnEnviar.style.display = 'none';

        }

        //Scroll volver arriba 
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

    }

    btnContinuar.addEventListener('click', function(){

        const seccion = seccionesSeleccionadas[seccionActual];

        /*Validar la sección actual*/
        if(!validarSeccion(seccion)){
            return;
        }

        /*Si está completo... continuar*/
        if(seccionActual < seccionesSeleccionadas.length - 1){
            seccionActual++;
            mostrarSeccion();
        }
    });

    //Anterior a la sección actual
    btnAnterior.addEventListener('click', function(){
        if(seccionActual > 0){
            seccionActual--;
            mostrarSeccion();
        }
    });
});

</script>

@endsection
