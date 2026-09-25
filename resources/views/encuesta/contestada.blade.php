@extends('base.base')
@section('titulo', $titulo = 'Encuesta')
@section('contenido')
<div class="w-100" style="height: 85vh;">
    <div class="h-100 card card-rounded bg-white mb-5 d-flex justify-content-center align-items-center" style="border 1px solid #b2b2b2 !important;">
        <div class="card shadow-lg text-center p-4" style="max-width: 500px; border-radius: 1rem;">
            <div class="card-body">
                <div class="mb-3">
                    <i class="mdi mdi-check-circle-outline text-success" style="font-size: 4rem;"></i>
                </div>
                <h3 class="card-title mb-3">¡Encuesta contestada!</h3>
                <p class="card-text text-muted">
                    Gracias por tomarse el tiempo de responder nuestra encuesta.  
                    Ya hemos registrado su opinión y no es necesario volver a completarla.
                </p>

                {{-- @if(isset($promotor) && $promotor && isset($link_google) && $link_google)
                <div class="mt-4 pt-3 border-top">
                    <p class="fw-semibold mb-3" style="font-size: 15px;">
                        ¡Nos alegra que hayas tenido una gran experiencia!<br>
                        <span class="text-muted" style="font-size: 13px;">¿Te gustaría compartirla en Google? Solo toma un minuto.</span>
                    </p>
                    <a href="{{ $link_google }}" 
                       target="_blank" 
                       class="btn mt-1 d-inline-flex align-items-center gap-2 justify-content-center w-100"
                       style="background-color: #4285F4; color: white; border-radius: 8px; padding: 12px 20px; font-weight: 600; font-size: 15px; text-decoration: none;">
                        {{-- Logo Google -
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.6 20H24v8h11.3C33.6 33.1 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C34.1 6.5 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c11 0 20-8 20-20 0-1.3-.1-2.7-.4-4z"/>
                            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 15.1 18.9 12 24 12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C34.1 6.5 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
                            <path fill="#4CAF50" d="M24 44c5.2 0 9.9-1.9 13.5-5l-6.2-5.2C29.5 35.6 26.9 36 24 36c-5.2 0-9.6-2.9-11.3-7.1l-6.5 5C9.8 39.8 16.5 44 24 44z"/>
                            <path fill="#1976D2" d="M43.6 20H24v8h11.3c-.9 2.4-2.5 4.4-4.6 5.8l6.2 5.2C40.6 35.6 44 30.3 44 24c0-1.3-.1-2.7-.4-4z"/>
                        </svg>
                        Escribir una reseña en Google
                    </a>
                </div>
                @endif --}}
                
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