<?php

namespace App\Http\Controllers\ControladorEncuesta;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControladorMail\MailEncuestaController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
//Usar BDD directamente
use Illuminate\Support\Facades\DB;

use App\Models\EnvioEncuesta;
use App\Models\Encuesta;
use App\Models\Respuesta;
use App\Models\Pregunta;

class EncuestaController extends Controller
{
    private array $google_reviews = [
        // Alcántara
        '1' => 'https://search.google.com/local/writereview?placeid=ChIJI-AAHhfPYpYRZi2A-BzN86g',
        // Burgos
        '2' => 'https://search.google.com/local/writereview?placeid=ChIJM1wvQhfPYpYRYdWvykM4OOw',
        // Tenderini
        '3' => 'https://search.google.com/local/writereview?placeid=ChIJu08IuKHFYpYRjEy5puXG5VY',
        // Alameda
        '4' => 'https://search.google.com/local/writereview?placeid=ChIJM-hQy-jFYpYR0R3_bDRxFSg',
        // Providencia
        '5' => 'https://search.google.com/local/writereview?placeid=ChIJH9zuWHXPYpYRcyXZKpPIojw',

    ];

    public function visualizar_encuesta(Request $request, $id_encuesta, $id_envio, $id_empresa){
        $empresa     = base64_decode(base64_decode(base64_decode($id_empresa)));
        $id_encuesta = base64_decode(base64_decode(base64_decode($id_encuesta)));
        $id_envio    = base64_decode(base64_decode(base64_decode($id_envio)));


        $estado_encuesta = EnvioEncuesta::where(
            'id_envio_encuesta',
            $id_envio
        )->first();

        if(!$estado_encuesta){
            return redirect()->route('encuesta_bloqueada');
        }

        // dd($id_envio);

        if($estado_encuesta->id_estado == 1){
            //Se Guarda respuesta
            if($request->isMethod('post')){
                // dd('Pasó al POST');

                $bandera_nps        = false;
                $bandera_comentario = false;
                $nps_valor          = null;

                
                if($estado_encuesta->id_estado == 1){

                    foreach($request->request as $key => $respuesta){

                        //Campos para contruir encuesta
                        if(!str_contains($key, 'pregunta-')){
                            continue;
                        }

                        $partes = explode("-", $key);
                        $id_pregunta = $partes[1];

                        // Se obtiene el ti po de pregunta
                        $tipo_pregunta = Pregunta::where(
                            'id_pregunta',
                            $id_pregunta
                        )
                        ->select('id_tipo_pregunta')
                        ->first();

                        // dd($tipo_pregunta);

                        if(!$tipo_pregunta){
                            continue;
                        }

                        // Respuestas múltiples
                        if (is_array($respuesta)) {

                            //Si seleccionó 'Otro', agregamos el texto ingresado
                            if (in_array('Otro', $respuesta)) {

                                $textoOtro = $request->input(
                                    'otro-' . $id_pregunta
                                );

                                if(!empty($textoOtro)){
                                    $indiceOtro = array_search(
                                        'Otro',
                                        $respuesta
                                    );

                                    if($indiceOtro !== false){
                                        $respuesta[$indiceOtro] =
                                            'Otro: ' . $textoOtro;
                                    }
                                }
                            }

                            //Covertir respiestas múltiples que se speraran po '|'
                            $respuesta = implode('|', $respuesta);
                        }

                        // Registrar respuesta
                        $this->registrar_respuesta(
                            $id_envio,
                            $empresa,
                            $id_pregunta,
                            $respuesta
                        );

                        //Detectar NPS 
                        if($tipo_pregunta->id_tipo_pregunta == 1){

                            $nps_valor = (int) $respuesta;

                            if($nps_valor <= 6){
                                $bandera_nps = true;
                            }
                        }
                        //Detectar cualesson los comentarios 
                        elseif(
                            $tipo_pregunta->id_tipo_pregunta == 3 &&
                            strlen(trim($respuesta)) > 0
                        ){
                            $bandera_comentario = true;
                        }
                    }

                    // dd('LLEGÓ AL FINAL', $id_envio);
                    // No hay que contar las respuestas, ya en esta parte sabemos si encuesta se ereposndió
                    EnvioEncuesta::where('id_envio_encuesta', $id_envio)->update(['id_estado' => 2,'fecha_respuesta' => now()]);

                    //Pasa al estado 2 si está respondido
                    EnvioEncuesta::where('id_agenda',$estado_encuesta->id_agenda)->update(['id_estado' => 2]);

                    //Notificación
                    if($bandera_nps && $bandera_comentario){
                        $controlador_email = new MailEncuestaController;
                        $controlador_email->notificar_encuesta_encargado($id_envio);
                    }

                    if($nps_valor !== null && $nps_valor >= 9){

                        $id_sucursal = (string) $estado_encuesta->id_sucursal;
                        $link_google = $this->google_reviews[$id_sucursal] ?? null;

                        return redirect()->route(
                            'encuesta_contestada',
                            [
                                'promotor' => 1,
                                'link_google' => $link_google
                                    ? urlencode($link_google)
                                    : null, 
                            ]
                        );
                    }

                    //Encuestas contestada
                    return redirect()->route(
                        'encuesta_contestada'
                    );

                }else{
                    return redirect()->route(
                        'encuesta_bloqueada'
                    );
                }
            }

            //Trae la encuesta
            $encuesta = Encuesta::where('id_encuesta',$id_encuesta)->first();

            //Servicios que se utilizaron
            $id_servicios = [];

            if(!empty($estado_encuesta->id_reserva)){
                $id_servicios = DB::table('reserva_servicio')
                    ->where('id_reserva',$estado_encuesta->id_reserva)
                    ->pluck('id_servicio')
                    ->toArray();
            }

            //Preguntas para crear formulario dinámico según preguntas 
            $preguntas = Pregunta::query()
                ->join('categoria', 'categoria.id_categoria', '=', 'pregunta.id_categoria')
                ->where(
                    'pregunta.id_encuesta',
                    $id_encuesta
                )
                ->where(function ($query) use ($id_servicios){

                    //Preguntas generales
                    $query->whereNull(
                        'categoria.id_servicio'
                    );

                    // Preguntas correspondientes a los servicios utilizados
                    if(!empty($id_servicios)){

                        $query->orWhereIn(
                            'categoria.id_servicio',
                            $id_servicios
                        );
                    }
                })
                ->select(
                    'pregunta.*',
                    'categoria.categoria as nombre_categoria'
                )
                ->with('tipo_pregunta')
                ->orderBy('pregunta.id_pregunta')
                ->get();

            //Mostrar encuesta completa según corresponda 
            return view('encuesta.encuesta', [
                'encuesta'     => $encuesta,
                'preguntas'    => $preguntas,
                'empresa'      => $empresa,
                'id_servicios' => $id_servicios,
            ]);

        }else{

            return redirect()->route(
                'encuesta_bloqueada'
            );
        }
    }

    private function registrar_respuesta($id_envio, $id_empresa, $id_pregunta, $respuesta): void {
        Respuesta::create([
            'respuesta'         => $respuesta,
            'id_pregunta'       => $id_pregunta,
            'id_envio_encuesta' => $id_envio,
            'id_empresa'        => $id_empresa
        ]);
    }

    public function encuesta_contestada(Request $request) {
        $promotor    = $request->query('promotor', 0);
        $link_google = $request->query('link_google') ? urldecode($request->query('link_google')) : null;
        return view('encuesta.contestada', compact('promotor', 'link_google'));
    }

    public function encuesta_bloqueada() {
        return view('encuesta.inaccesible');
    }

    public function encuesta_nps() {
        return view('encuesta.nps');
    }

    public function obtener_responsables() {
        $controlador_mail = new MailEncuestaController();
        $responsables = $controlador_mail->notificar_encuesta_encargado(13);

        return $responsables;
    }

    public function enviar_encuesta($id_clinica) {
        set_time_limit(120);
        $id_encuesta = 2;
        $response = Http::get('https://app.padremariano.com/experienciatestPM/api/encuesta.php?id_clinica='.$id_clinica);
        $datos = $response->json();

        $resultados = [];

        foreach($datos as $dato) {
            $cantidad_envios = EnvioEncuesta::where('id_agenda', $dato["id_agenda"])->where('id_tipo_envio', 1)->count();
            if($cantidad_envios == 0) {
                $encuesta = EnvioEncuesta::create([
                    "id_agenda"         => $dato["id_agenda"],
                    'nombre_paciente'   => $dato["nombre_paciente"],
                    'nombre_dentista'   => $dato["nombre_dentista"],
                    'sucursal'          => $dato["sucursal"],
                    'email'             => $dato["email"],
                    'celular'           => $dato["celular"],
                    'id_paciente'       => $dato["id_paciente"],
                    'id_dentista'       => $dato["id_dentista"],
                    'id_empresa'        => $dato["id_empresa"],
                    'id_sucursal'       => $dato["id_sucursal"],
                    'id_estado'         => 1,            // cambiar segun el estado, probablemente 1 sea enviado, por lo que podría estar bien
                    'id_encuesta'       => $id_encuesta, // cambiar con el id_encuesta real, no se si enviarlo por get u otro ***
                    'id_tipo_envio'     => 1,            // tipo_envio, wsp o correo, hay que hacerlo dinámico, probablemente get tambien ****
                    'fecha_cita'        => $dato["fecha_cita"],
                    'hora_cita'         => $dato["hora_cita"],
                    'id_especialidad'   => 1,
                    'especialidad'      => 'Diagnóstico',
                    'rut'               => $dato["rut"],
                ]);
                
                $id_envio_encuesta = $encuesta->id_envio_encuesta;

                $id_empresa_encriptado  = base64_encode(base64_encode(base64_encode($dato["id_empresa"])));
                $id_encuesta_encriptado = base64_encode(base64_encode(base64_encode($id_encuesta)));
                $id_envio_encriptado    = base64_encode(base64_encode(base64_encode($id_envio_encuesta)));
                

                $link_encuesta = 'http://localhost:9191/encuesta/'.$id_encuesta_encriptado.'/'.$id_envio_encriptado.'/'.$id_empresa_encriptado;

                $controlador_email = new MailEncuestaController;
                $envio_mail = $controlador_email->enviar_encuesta_mail($dato, $link_encuesta);
                // $respuesta = $envio_mail->getData();
                // if ($respuesta->success) {}

                // return $envio_mail;


                $resultados[] = [
                    'id_agenda' => $dato['id_agenda'],
                    'email' => $dato['email'],
                    'resultado' => $envio_mail
                ];
            }
        }

        return response()->json([
            'success' => true,
            'total_envios' => count($resultados),
            'detalle' => $resultados
        ]);
    }

    public function enviar_encuesta_test() {
        $link_encuesta = 'https://www.youtube.com';
        $dato = [
                'nombre_paciente'   => 'Kevin Martinez',
                'nombre_dentista'   => 'Dan Mella',
                'fecha_cita'        => '15-12-2025',
                'hora_cita'         => '10:00',
                'sucursal'          => 'Alcantara',
                'email'             => 'd.danmella@gmail.com'
        ];
        $controlador_email = new MailEncuestaController;
        $envio_mail = $controlador_email->enviar_encuesta_mail($dato, $link_encuesta);

        dd($envio_mail);
    }


    public function enviar_encuesta_whatsapp($id_clinica) {

   
        set_time_limit(120);
        $id_encuesta = 2;
        //$response = Http::get('https://app.padremariano.com/experienciatestPM/api/encuesta-wsp.php?id_clinica='.$id_clinica);
        $datos = $response->json();
         
        $resultados = [];
        foreach($datos as $dato) {
            $cantidad_respondidas = EnvioEncuesta::where('id_agenda', $dato["id_agenda"])->where('id_estado', 2)->count();
            $cantidad_envios = EnvioEncuesta::where('id_agenda', $dato["id_agenda"])->where('id_tipo_envio', 2)->count();

                      
            // dd($cantidad_envios);
            if($cantidad_envios == 0 && $cantidad_respondidas == 0) {
                $encuesta = EnvioEncuesta::create([
                    "id_agenda"         => $dato["id_agenda"],
                    'nombre_paciente'   => $dato["nombre_paciente"],
                    'nombre_dentista'   => $dato["nombre_dentista"],
                    'sucursal'          => $dato["sucursal"],
                    'email'             => $dato["email"],
                    'celular'           => $dato["celular"],
                    'id_paciente'       => $dato["id_paciente"],
                    'id_dentista'       => $dato["id_dentista"],
                    'id_empresa'        => $dato["id_empresa"],
                    'id_sucursal'       => $dato["id_sucursal"],
                    'id_estado'         => 1,            // cambiar segun el estado, probablemente 1 sea enviado, por lo que podría estar bien
                    'id_encuesta'       => $id_encuesta, // cambiar con el id_encuesta real, no se si enviarlo por get u otro ***
                    'id_tipo_envio'     => 2,            // tipo_envio, wsp o correo, hay que hacerlo dinámico, probablemente get tambien ****
                    'fecha_cita'        => $dato["fecha_cita"],
                    'hora_cita'         => $dato["hora_cita"],
                    'id_especialidad'   => 1,
                    'especialidad'      => 'Diagnóstico',
                    'rut'               => $dato["rut"],
                ]);
                
                $id_envio_encuesta = $encuesta->id_envio_encuesta;

                $id_empresa_encriptado  = base64_encode(base64_encode(base64_encode($dato["id_empresa"])));
                $id_encuesta_encriptado = base64_encode(base64_encode(base64_encode($id_encuesta)));
                $id_envio_encriptado    = base64_encode(base64_encode(base64_encode($id_envio_encuesta)));
                

                $link_encuesta = 'http://localhost:9191/encuesta/'.$id_encuesta_encriptado.'/'.$id_envio_encriptado.'/'.$id_empresa_encriptado;

                $envio_wsp = Http::get('localhost:9191/pago_online/encuesta.php?nombre='.$dato["nombre_paciente"].'&nombre_dentista='.$dato["nombre_dentista"].'&numero=569'.$dato["celular"].'&fecha_cita='.$dato["fecha_cita"].'&clinica='.$dato["sucursal"].'&link='.$link_encuesta);

                // return $envio_wsp;

                
                $resultados[] = [
                    'id_agenda' => $dato['id_agenda'],
                    'email' => $dato['email'],
                    'resultado' => $envio_wsp
                ];

            }
        }

        return response()->json([
            'success' => true,
            'total_envios' => count($resultados),
            'detalle' => $resultados
        ]);
    }
}
