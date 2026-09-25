@extends('base.base')
@section('titulo', 'Hotel MAE - Encuesta de Satisfacción')
@section('link')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
@endsection
@section('contenido')
<div class="login-mae">
    <div class="background-mae">
        <img src="{{ asset('assets/images/mae/exterior_hdmae.jpg') }}" alt="Hotel MAE">
        <div class="principal-mae"></div>
    </div>
    <div class="form-mae">
        <img src="{{ asset('assets/images/mae/logo_mae_azul.png') }}" class="logo-mae" alt="Logo Hotel MAE">
        @if(session('message'))
            <div class="alert alert-warning">
                {{ session('message') }}
            </div>
        @endif
        <h2>Bienvenido</h2>
        <h4>Encuesta Satisfacción</h4>
        <p class="subtitulo-mae">
            Ingresa para comenzar tu experiencia MAE.
        </p>
        <form method="POST">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="correo@ejemplo.cl">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="contrasena" class="form-control @error('contrasena') is-invalid @enderror" placeholder="********">
                @error('contrasena')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button class="btn-login">
                Ingresar
            </button>
        </form>
        <div class="footer-mae">
            <strong>Hotel MAE</strong>
        </div>
    </div>
</div>

@endsection