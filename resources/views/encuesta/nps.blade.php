@extends('base.base')
@section('titulo', $titulo = 'Encuesta')
@section('contenido')
<div class="w-100" style="height: 85vh;">
    <div class="h-100 card card-rounded bg-white mb-5 d-flex justify-content-center align-items-center" style="border 1px solid #b2b2b2 !important;">
        <div class="card shadow-lg text-center p-4" style="max-width: 500px; border-radius: 1rem;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-3">Lamentamos su mala experiencia</h4>
                <p class="card-text text-muted text-justify">
                    Agradecemos sinceramente que se haya tomado el tiempo para responder nuestra encuesta.  
                    Lamentamos que su experiencia en la clínica no haya sido satisfactoria.  
                    Queremos informarle que sus comentarios fueron enviados al <strong>Director Clínico</strong>,  
                    quien estará a cargo de revisar su caso y tomar las medidas correspondientes.
                </p>
                <p class="card-text text-muted">
                    Valoramos mucho su opinión, ya que nos ayuda a mejorar la calidad de nuestra atención.
                </p>
                <a href="https://www.padremariano.com/" class="btn btn-primary mt-3">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
<div class="">
    <div class="bg-primary py-5 text-center">
        <img width="150" src="{{ asset('assets/images/logo-experiencia.png') }}" alt="Logo +Experiencia"> 
    </div>
</div>
@endsection