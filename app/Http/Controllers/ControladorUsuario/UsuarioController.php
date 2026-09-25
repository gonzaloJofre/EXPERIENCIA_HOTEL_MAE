<?php

namespace App\Http\Controllers\ControladorUsuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Usuario;
use App\Models\UsuarioSucursal;

class UsuarioController extends Controller
{
    public function iniciar_sesion($request) {
        if($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email',
                'contrasena' => 'required'
            ], [
                'email.required' => 'El campo email es obligatorio',
                'email.email' => 'El formato del email no es válido',
                'contrasena.required' => 'El campo contraseña es obligatorio'
            ]);

            $credentials = [
                'email' => $request->email,
                'contrasena' => $request->contrasena
            ];

            $usuario = Usuario::where('email', $request->email)->first();
        
            if ($usuario && Hash::check($request->contrasena, $usuario->contrasena)) {
                Auth::login($usuario, false);
                return ['estado' => true, 'codigo' => 1];
            }

            return ['estado' => false, 'codigo' => 0];
        }
    }

    public function cerrar_sesion() {
        Auth::logout();
        session()->forget('empresa_seleccionada_id');
        return redirect()->route('login');
    }

    public function validar_accesos($usuario, $empresa) {
        $empresas = $usuario->empresas;
        if($empresa) {
            $empresa_seleccionada = $usuario->empresas->where('id_empresa', $empresa)->first();
        } else {
            $empresa_seleccionada = $empresas->first();
            session(['empresa_seleccionada_id' => $empresa_seleccionada->id_empresa]);
        }
        $sucursales = UsuarioSucursal::sucursalesUsuario($usuario->id_usuario, $empresa_seleccionada->id_empresa);
        return ['empresas' => $empresas, 'sucursales' => $sucursales, 'empresa_seleccionada' => $empresa_seleccionada];
    }
}
