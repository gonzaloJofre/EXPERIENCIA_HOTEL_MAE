<?php

namespace App\Http\Controllers\ControladorMail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Mail\MailEncuesta;
use App\Mail\MailNotificarResponsable;

use App\Models\EnvioEncuesta;
use App\Models\Respuesta;


class MailEncuestaController extends Controller
{
    public function enviar_encuesta_mail($datos_envio, $link_encuesta) {
        try {
            Mail::to($datos_envio["email"])->send(new MailEncuesta($datos_envio, $link_encuesta));
            return response()->json([
                'success' => true,
                'message' => 'Correo de experiencia enviado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
       
    }

    public function notificar_encuesta_encargado($id_envio_encuesta) {
        $usuario_encuestado = EnvioEncuesta::where('id_envio_encuesta', $id_envio_encuesta)
                                            ->select('nombre_paciente', 'nombre_dentista','sucursal','email', 'sucursal','fecha_cita','hora_cita')
                                            ->first();

        if ($usuario_encuestado) {
            $usuario_encuestado->fecha_cita = Carbon::parse($usuario_encuestado->fecha_cita)->format('d-m-Y');
            $usuario_encuestado->hora_cita = Carbon::parse($usuario_encuestado->hora_cita)->format('H:i');
        }

        $usuarios_relacionados = EnvioEncuesta::join('usuario_sucursal', 'usuario_sucursal.id_sucursal', '=', 'envio_encuesta.id_sucursal')
                                                ->join('usuario', 'usuario.id_usuario', '=', 'usuario_sucursal.id_usuario')
                                                ->where('envio_encuesta.id_envio_encuesta', $id_envio_encuesta)
                                                ->select('usuario_sucursal.id_usuario', 'usuario.nombres', 'usuario.email')
                                                ->get();
        $respuestas = Respuesta::join('pregunta', 'pregunta.id_pregunta', '=', 'respuesta.id_pregunta')
                                ->where('respuesta.id_envio_encuesta', $id_envio_encuesta)
                                ->whereIn('pregunta.id_tipo_pregunta', [1, 3])
                                ->select('pregunta.pregunta', 'respuesta.respuesta')
                                ->get(); 

        $pregunta_nps = $respuestas[0]->pregunta;
        $respuesta_nps = $respuestas[0]->respuesta;

        $pregunta_comentario = $respuestas[1]->pregunta;
        $respuesta_comentario = $respuestas[1]->respuesta;

        foreach($usuarios_relacionados as $usuario_relacionado) {
            try {
                Mail::to($usuario_relacionado->email)
                ->send(new MailNotificarResponsable($usuario_encuestado->nombre_paciente,
                                                    $usuario_encuestado->nombre_dentista, 
                                                    $usuario_encuestado->fecha_cita, 
                                                    $usuario_encuestado->hora_cita, 
                                                    $usuario_encuestado->sucursal, 
                                                    $pregunta_nps, 
                                                    $respuesta_nps, 
                                                    $pregunta_comentario, 
                                                    $respuesta_comentario,
                                                    $id_envio_encuesta));
                
                echo response()->json([
                    'success' => true,
                    'message' => 'Correo de notificacion enviado exitosamente'
                ], 200);

            } catch (\Exception $e) {
                echo response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el correo: ' . $e->getMessage()
                ], 500);
            }
        }
        // dd($usuarios_relacionados);
        
       
    }
}
