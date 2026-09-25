<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorVistas\VistaController;
use App\Http\Controllers\ControladorUsuario\UsuarioController;
use App\Http\Controllers\ControladorEncuesta\EncuestaController;
use App\Http\Controllers\ControladorReportes\ReportesController;
use App\Http\Controllers\ControladorMail\MailEncuestaController;

use App\Http\Controllers\ControladorTraerDatos\TraerDatosController;
use App\Http\Controllers\ControladorDashboard\DashboardController;
use App\Reportes\ReporteHotelMae;
use Maatwebsite\Excel\Facades\Excel;


//*** RUTAS ***

Route::match(['GET','POST'], '/',                           [VistaController::class,   'login'])                        ->name('login');
Route::match(['GET','POST'], '/panel',                      [VistaController::class,   'panel'])                        ->name('panel');
Route::match(['GET','POST'], '/resumen-general',            [VistaController::class,   'resumen_general'])              ->name('resumen-general');
Route::match(['GET','POST'], '/indice-prioridad',           [VistaController::class,   'indice_prioridad'])             ->name('indice-prioridad');
Route::match(['GET','POST'], '/resultado-profesional',      [VistaController::class,   'resultado_profesional'])        ->name('resultado-profesional');
Route::match(['GET','POST'], '/mantenedores/encuestas',     [VistaController::class,   'encuestas'])                    ->name('encuestas');
Route::match(['GET','POST'], '/cerrar-sesion',              [UsuarioController::class, 'cerrar_sesion'])                ->name('cerrar-sesion');
Route::get('/analisis-sentimientos',                        [VistaController::class,   'indexAnalisis'])                ->name('analizar_sentimientos');

// Encuesta
Route::get('/encuesta/enviar_encuesta/{id_clinica}',                               [EncuestaController::class, 'enviar_encuesta']);
Route::get('/encuesta/enviar_encuesta_whatsapp/{id_clinica}',                      [EncuestaController::class, 'enviar_encuesta_whatsapp']);
Route::get('/encuesta/encuesta-contestada',                                        [EncuestaController::class, 'encuesta_contestada'])          ->name('encuesta_contestada');
Route::get('/encuesta/encuesta-bloqueada',                                         [EncuestaController::class, 'encuesta_bloqueada'])           ->name('encuesta_bloqueada');
Route::get('/encuesta/encuesta-nps',                                               [EncuestaController::class, 'encuesta_nps'])                 ->name('encuesta_nps');
Route::get('/encuesta/detalle-encuesta/{id_encuesta}',                             [VistaController::class,    'detalle_encuesta'])             ->name('detalle_encuesta');
Route::match(['GET', 'POST'], '/encuesta/{id_encuesta}/{id_envio}/{id_empresa}',   [EncuestaController::class, 'visualizar_encuesta']);

// Reportería
Route::get('/reporteria/ajax/nps-anual',                            [ReportesController::class, 'nps_empresa_anual']);
Route::get('/reporteria/ajax/datos-generales',                      [ReportesController::class, 'resumen_general_generales']);
Route::get('/reporteria/ajax/evolucion-detractores-promotores',     [ReportesController::class, 'evolucion_detractores_promotores']);
Route::get('/reporteria/ajax/distribucion-respuestas-nps',          [ReportesController::class, 'distribucion_respuestas_nps']);
Route::get('/reporteria/ajax/evolucion-ibb-sucursales',             [ReportesController::class, 'evolucion_ibb_sucursales']);
Route::get('/reporteria/ajax/ibb-dentistas',                        [ReportesController::class, 'ibb_dentistas']);
Route::get('/reporteria/ajax/cuadrante-preguntas',                  [ReportesController::class, 'cuadrante_preguntas']);
Route::get('/reporteria/ajax/traer-comentarios',                    [ReportesController::class, 'traer_comentarios']);
Route::get('/reporteria/datos-nps',                                 [ReportesController::class, 'datos-nps']);
Route::post('/reporteria/ajax/analisis-sentimientos',               [ReportesController::class, 'analizar_sentimientos']);


// correo prueba
Route::get('/encuesta/enviar_encuesta_test/',                       [EncuestaController::class, 'enviar_encuesta_test']);



// PRUEBA encuesta Hotel
Route::get('/test', function(){
    $idEncuesta = base64_encode(base64_encode(base64_encode(3)));
    $idEnvio    = base64_encode(base64_encode(base64_encode(5))); //1,2,3,4
    $idEmpresa  = base64_encode(base64_encode(base64_encode(1)));
    // dd($idEmpresa);
    return redirect("/encuesta/$idEncuesta/$idEnvio/$idEmpresa");
});

// Creación de reporte
// Route::get('/dashboard', [DashboardController::class,'index']);
Route::get('/dashboard', [DashboardController::class,'index'])->middleware('auth');

// Descargar reporte Excel
Route::get('/reportes/exportar', function () {
    return Excel::download(new ReporteHotelMae,'reporte-hotel-mae.xlsx');
})->name('reportes.exportar.excel');