<?php

namespace App\Http\Controllers\ControladorVistas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControladorUsuario\UsuarioController;
use App\Http\Controllers\ControladorReportes\ReportesController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\EnvioEncuesta;
use App\Models\Respuesta;

class VistaController extends Controller
{
    public function indexAnalisis(Request $request) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id'); 
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            $controlador_reportes = new ReportesController();
            // $series = $controlador_reportes->nps_empresa_anual(1);
            
            return view('reporteria.dashboard-sentimientos', [
                // 'series' => $series,
                'empresas' => $permisos['empresas'], 
                'sucursales' => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa
            ]);
        }
        return redirect()->route('login');
        // return view('reporteria.dashboard-sentimientos');
    }
    public function login(Request $request) {
        if($request->isMethod('post')) {
            $usuario = new UsuarioController();
            $validacion = $usuario->iniciar_sesion($request);
            // if($validacion["estado"]) {
            //     return redirect()->route('panel');
            // }
            if($validacion["estado"]) {
                return redirect('/dashboard');
            }
            return back()->with([
                'message' => 'Correo electrónico o contraseña incorrectos. Intente nuevamente.'
            ]);
        }
        return view('login');
    }

    public function panel(Request $request) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id'); 
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            $controlador_reportes = new ReportesController();
            // $series = $controlador_reportes->nps_empresa_anual(1);
            
            return view('panel', [
                // 'series' => $series,
                'empresas' => $permisos['empresas'], 
                'sucursales' => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa
            ]);
        }
        return redirect()->route('login');
    }

    public function detalle_encuesta(Request $request, $id_envio_encuesta) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id'); 
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            
            $datos_encuesta = EnvioEncuesta::where('id_envio_encuesta', $id_envio_encuesta)->first();
            $respuestas = Respuesta::where('id_envio_encuesta', $id_envio_encuesta)->get();
            // dd($respuestas);
            
            return view('encuesta.detalle', [
                'empresas'             => $permisos['empresas'], 
                'sucursales'           => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa,
                'datos_encuesta'       => $datos_encuesta,
                'respuestas'           => $respuestas
            ]);
        }
        return redirect()->route('login');
    }

    public function resumen_general(Request $request) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id');
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            return view('reporteria.resumen-general', [
                'empresas' => $permisos['empresas'], 
                'sucursales' => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa
            ]);
        }
        return redirect()->route('login');
    }

    public function indice_prioridad(Request $request) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id');
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            return view('reporteria.indice-prioridad', [
                'empresas' => $permisos['empresas'], 
                'sucursales' => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa
            ]);
        }
        return redirect()->route('login');
    }

    public function resultado_profesional(Request $request) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id');
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            return view('reporteria.resultado-profesional', [
                'empresas' => $permisos['empresas'], 
                'sucursales' => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa
            ]);
        }
        return redirect()->route('login');
    }


    public function encuestas(Request $request) {
        if(Auth::check()) {
            $empresa_seleccionada = Session::get('empresa_seleccionada_id');
            if($request->isMethod("post")) {
                $empresa_seleccionada = base64_decode($request->empresa_seleccionada);
                session(['empresa_seleccionada_id' => $empresa_seleccionada]);
            }
            $usuario = Auth::user();
            $controlador_usuario = new UsuarioController();
            $permisos = $controlador_usuario->validar_accesos($usuario, $empresa_seleccionada);
            return view('mantenedores.encuesta',  [
                'empresas' => $permisos['empresas'], 
                'sucursales' => $permisos['sucursales'], 
                'empresa_seleccionada' => $permisos['empresa_seleccionada']->empresa,
                'encuestas' => $permisos['empresa_seleccionada']->encuestas,
            ]);
        }
        return redirect()->route('login');
    }
}
