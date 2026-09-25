<!-- Modal -->
<div class="modal fade" id="modal_crear_encuesta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Nueva Encuesta</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="">
          <div class="row">
            <div class="col-lg-6">
              <h4>Crear encuesta</h4>
              <div class="form-group mb-3">
                <label>Nombre Encuesta</label>
                <input id="nombre_encuesta" type="text" class="form-control">
              </div>

              <div class="form-group mb-3">
                <label>Empresa de Encuesta</label>
                <select name="empresas" id="empresas" class="form-control">
                  <option value="">Seleccionar tipo de Encuesta</option>
                  @foreach($empresas as $empresa)
                  <option value="{{ $empresa->id_empresa }}">{{ $empresa->empresa }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group mb-3">
                <label>Tipo de Encuesta</label>
                <select name="tipo_encuesta" id="tipo_encuesta" class="form-control">
                  <option value="">Seleccionar Empresa</option>
                </select>
              </div>

              <h4>Enunciados o Preguntas</h4>
              <div class="form-group mb-3">
                <label>Escribe enunciado o pregunta:</label>
                <div class="d-flex justify-content-between">
                  <input id="input_preguntas" type="text" class="form-control">
                  <button type="button" class="btn btn-sm btn-primary ms-1" onclick="agregar_pregunta();"><i class="mdi mdi-plus"></i></button>
                </div>
              </div>

            </div>
            <div class="col-lg-6">
              <!-- <h4>Enunciados o Preguntas</h4> -->
              <!-- <div class="form-group mb-3">
                <label>Formato de Respuesta:</label>
                <select name="" id="" class="form-select">
                  <option value="">1 - 5</option>
                  <option value="">1 - 7</option>
                  <option value="">1 - 10</option>
                </select>
              </div> -->
              <div class="form-group mb-3">
                <h4>Previsualización:</h4>
                <div class="card">
                  <div class="card-body" style="font-size: 12px;">
                    <div id="preguntas">
                      <div class="mb-2">
                        <p>
                          <b class="text-danger" style="font-weight: bolder;">*</b> 
                          En relación a tu última experiencia en nuestro centro, ¿Qué tan probable es que recomiendes la clínica a un familiar o amigo?
                        </p>
                        <div class="puntuacion text-center d-flex justify-content-around"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="validar_encuesta();">Crear Encuesta</button>
      </div>
    </div>
  </div>
</div>


<script>

  function borrar_pregunta(elemento) {
    elemento.closest('.pregunta-nueva').remove();
  }

  function agregar_pregunta() {
    let preguntas = document.getElementById("preguntas");
    let input = document.getElementById("input_preguntas");
    preguntas.innerHTML += `<div class="pregunta-nueva">
                              <div style="font-size: 15px;" class="text-start text-danger"><i class="mdi mdi-delete-empty" style="cursor:pointer;" onclick="borrar_pregunta($(this));"></i></div>
                              <p>
                                <b class="text-danger" style="font-weight: bolder;">*</b>
                                ${input.value}
                              </p>
                              <div class="puntuacion text-center d-flex justify-content-around"></div>
                            </div>`;
    agregar_puntuacion();
    input.value = "";
  }

  function agregar_puntuacion() {
      let puntuacion = document.getElementsByClassName('puntuacion');
      for(let i = 0; i < puntuacion.length; i++) {
          puntuacion[i].innerHTML = `<i style="color: #FF0000" class="mdi mdi-emoticon-sad-outline"></i>
                          <i style="color: #CC0000" class="mdi mdi-emoticon-sad-outline"></i>
                          <i style="color:rgb(223, 190, 2)" class="mdi mdi-emoticon-neutral-outline"></i>
                          <i style="color: #80FF00 " class="mdi mdi-emoticon-neutral-outline"></i>
                          <i style="color: #00FF00" class="mdi mdi-emoticon-happy-outline"></i>`;
      }
  }

  function validar_encuesta() {
    let nombre_encuesta = $('#nombre_encuesta');
    let empresas = $('#empresas');
    let tipo_encuesta = $('#tipo_encuesta');
    let preguntas = $('#preguntas');
    let formulario = $('#formulario_encuesta');

    if(nombre_encuesta.val() == '') {
      nombre_encuesta.css('border','1px solid red');
    } else if(empresas.val() == '') {
      nombre_encuesta.css('border','1px solid #ced4da');
      empresas.css('border','1px solid red');
    } else if(tipo_encuesta.val() == '') {
      nombre_encuesta.css('border','1px solid #ced4da');
      empresas.css('border','1px solid #ced4da');
      tipo_encuesta.css('border','1px solid red');
    } else {
      nombre_encuesta.css('border','1px solid #ced4da');
      empresas.css('border','1px solid #ced4da');
      tipo_encuesta.css('border','1px solid #ced4da');
      formulario.submit()
    }
  }

  agregar_puntuacion();
</script>