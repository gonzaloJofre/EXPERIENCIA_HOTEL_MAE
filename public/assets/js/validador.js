// Validar cada parte de la encuesta 
function validarSeccion(seccion) {

    let valido = true;
    let mensajes = [];

    $(seccion).find('.nps-form').removeClass('error-pregunta');

    $(seccion).find('.nps-form').each(function (){

        let contenedor = $(this);

        // Obtener el número visible de la pregunta
        let tituloPregunta = contenedor.find('.pregunta-titulo').text().trim();
        let numeroPreguntaMatch = tituloPregunta.match(/^\d+/);
        let numeroPregunta = numeroPreguntaMatch ? numeroPreguntaMatch[0] : '?';

        // Selección múltiple
        let seleccionMultiple = contenedor.find('.seleccion-multiple');

        if (seleccionMultiple.length) {

            let seleccionadas = seleccionMultiple.find('input[type="checkbox"][name^="pregunta-"]:checked');
            let cantidad = seleccionadas.length;
            let primerInput = seleccionMultiple.find('input[type="checkbox"][name^="pregunta-"]').first();
            
            if(!primerInput.length){
                return;
            }

            // Mínimo una opción
            if(cantidad === 0){

                valido = false;

                mensajes.push({pregunta: numeroPregunta,texto: 'Debes seleccionar al menos una opción.'});
                contenedor.addClass('error-pregunta');
            }

            // Máximo 3 opciones
            if (cantidad > 3) {

                valido = false;
                mensajes.push({pregunta: numeroPregunta,texto: 'Puedes seleccionar un máximo de 3 opciones.'});
                contenedor.addClass('error-pregunta');
            }

            // Si selecciona "Otro", debe escribir el detalle
            let checkboxOtro = seleccionMultiple.find('input[type="checkbox"][value="Otro"]');

            if (checkboxOtro.is(':checked')) {

                let campoOtro = seleccionMultiple.find('.opcion-otro');
                let textoOtro = campoOtro.length ? campoOtro.val().trim() : '';

                if(textoOtro === ''){

                    valido = false;

                    mensajes.push({
                        pregunta: numeroPregunta,
                        texto: 'Indica cuál es la opción "Otro".'
                    });
                    contenedor.addClass('error-pregunta');
                }
            }
            return;
        }

        // Otros tipos de preguntas
        let inputs = contenedor.find('[name^="pregunta-"]');

        if (!inputs.length) {
            return;
        }

        // Se mantiene el nombre real del input para validar
        let nombre = inputs.first().attr('name');


        // Radio
        if (inputs.first().attr('type') === 'radio') {

            if(!$(seccion).find('[name="' + nombre + '"]:checked').length){

                valido = false;
                mensajes.push({pregunta: numeroPregunta,texto: '-> Selecciona una opción.'});
                contenedor.addClass('error-pregunta');
            }
        }

        // Textarea
        if (inputs.is('textarea')) {

            let valor = inputs.first().val().trim();

            if(valor === ''){

                valido = false;
                mensajes.push({pregunta: numeroPregunta,texto: 'Completa este campo.'});
                contenedor.addClass('error-pregunta');
            }
        }

    });

    // Mostrar alerta si hay errores
    if(!valido){

        mostrarAlertaValidacion(mensajes);

        let primerError = $(seccion).find('.error-pregunta').first();

        if (primerError.length) {

            setTimeout(function () {

                $('html, body').animate({
                    scrollTop: primerError.offset().top - 120
                }, 400);
            }, 300);
        }
    }
    return valido;
}


//Mostrar ALertas según corresponda
function mostrarAlertaValidacion(mensajes) {

    let listaMensajes = '';

    mensajes.forEach(function (mensaje) {

        listaMensajes += `
            <div class="alerta-mae-item">
                <div class="alerta-mae-item-icono">
                    <i class="mdi mdi-alert-outline"></i>
                </div>
                <div class="alerta-mae-item-contenido">
                    <strong>
                        Pregunta ${mensaje.pregunta}
                    </strong>
                    <span>
                        ${mensaje.texto}
                    </span>
                </div>
            </div>
        `;  
    });


    Swal.fire({

        icon: 'warning',
        width: '540px',
        background: '#FFFFFF',
        showConfirmButton: true,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#1E4F63',
        buttonsStyling: true,
        customClass: {
            popup: 'alerta-validacion-mae',
            icon: 'alerta-mae-warning',
            title: 'alerta-mae-titulo',
            htmlContainer: 'alerta-mae-contenido',
            confirmButton: 'alerta-mae-boton'
        },
        title: 'Completa esta sección',
        html: `
            <div class="alerta-mae-descripcion">
                Antes de continuar, revisa las preguntas que aún están pendientes.
            </div>
            <div class="alerta-mae-lista">
                ${listaMensajes}
            </div>
        `
    });
}

//Validar Errores
$(document).on('change input','[name^="pregunta-"], .opcion-otro', function (){
        $(this).closest('.nps-form').removeClass('error-pregunta');
    }
);

//Form Encuesta
$('#form-encuesta').on('submit', function (e) {

    e.preventDefault();

    const formulario = this;
    const $boton = $('#btn-enviar');
    const $spinner = $('#spinner');
    const seccionActual = $('.seccion-encuesta:visible').first();

    if(seccionActual.length){

        if(!validarSeccion(seccionActual[0])){
            return false;
        }

    }

    $boton.prop('disabled', true);
    $spinner.removeClass('d-none');

    $boton.contents().filter(function (){    
        return this.nodeType === 3;}).first().replaceWith(' Enviando...');

    formulario.submit();

});