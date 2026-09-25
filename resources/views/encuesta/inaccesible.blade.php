@extends('base.base')
@section('titulo', $titulo = 'Encuesta')
@section('contenido')
<div class="w-100" style="height: 85vh;">
    <div class="h-100 card card-rounded bg-white mb-5 d-flex justify-content-center align-items-center" style="border 1px solid #b2b2b2 !important;">
        <div class="card shadow-lg text-center p-4" style="max-width: 500px; border-radius: 1rem;">
            <div class="card-body">
                <div class="mb-3">
                    <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 4rem;"></i>
                </div>
                <h3 class="card-title mb-3">Encuesta no disponible</h3>
                <p class="card-text text-muted">
                    Esta encuesta ya fue respondida anteriormente o no se encuentra disponible en este momento.  
                    Si tienes dudas, por favor comunícate con nosotros.
                </p>
                <a href="https://www.padremariano.com/" class="btn btn-secondary mt-3">Volver al Inicio</a>
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