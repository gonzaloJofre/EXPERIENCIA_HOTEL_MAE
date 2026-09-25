function agregar_preguntas() {
    let puntuacion = document.getElementsByClassName('puntuacion');
    for(let i = 0; i < puntuacion.length; i++) {
        puntuacion[i].innerHTML = `<i style="color: #FF0000" class="mdi mdi-emoticon-sad-outline"></i>
                        <i style="color: #CC0000" class="mdi mdi-emoticon-sad-outline"></i>
                        <i style="color:rgb(223, 190, 2)" class="mdi mdi-emoticon-neutral-outline"></i>
                        <i style="color: #80FF00 " class="mdi mdi-emoticon-neutral-outline"></i>
                        <i style="color: #00FF00" class="mdi mdi-emoticon-happy-outline"></i>`;
    }
}


agregar_preguntas();