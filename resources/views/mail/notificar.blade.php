<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Experiencia - Centro Odontológico Padre Mariano</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-collapse: collapse;">
        <!-- Header -->
        <tr>
            <td style="background: #e63946; padding: 30px 20px; text-align: center;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: center;">
                            <div style="background-color: #ffffff; width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 15px auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                <img style="width: 100%;" src="https://padremariano.com/assets/img/LogoPM.png" alt="Logo Centro Odontológico Padre Mariano">
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Alerta de Experiencia</h1>
                            <h2 style="color: #ffffff; margin: 5px 0 0 0; font-size: 18px; font-weight: normal; opacity: 0.9;">Centro Odontológico Padre Mariano</h2>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Main Content -->
        <tr>
            <td style="padding: 40px 30px 20px 30px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: center; padding-bottom: 25px;">
                            <h3 style="color: #e63946; margin: 0; font-size: 22px; font-weight: bold;">Se ha registrado un Detractor</h3>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #333333; font-size: 16px; line-height: 1.6; padding-bottom: 25px;">
                            Estimado/a Encargado/a,
                            <br><br>
                            Le notificamos que el paciente <strong>{{ $nombre_paciente }}</strong> dejó un puntaje bajo en la encuesta de satisfacción. A continuación, encontrará los detalles de su atención y los comentarios asociados.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Appointment Details -->
        <tr>
            <td style="padding: 0 30px 30px 30px;">
                <table style="width: 100%; background-color: #f8f9fa; border-radius: 10px; border-collapse: collapse; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="padding: 25px;">
                            <h4 style="color: #00ad9c; margin: 0 0 20px 0; font-size: 18px; font-weight: bold; text-align: center; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">Detalles de la Atención</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 8px 0; width: 30%; vertical-align: top;">
                                        <span style="font-weight: bold; color: #495057; font-size: 14px;">Paciente:</span>
                                    </td>
                                    <td style="padding: 8px 0; color: #333333; font-size: 14px;">{{ $nombre_paciente }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; vertical-align: top;">
                                        <span style="font-weight: bold; color: #495057; font-size: 14px;">Especialista:</span>
                                    </td>
                                    <td style="padding: 8px 0; color: #333333; font-size: 14px;">Dr/a. {{ $nombre_dentista }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; vertical-align: top;">
                                        <span style="font-weight: bold; color: #495057; font-size: 14px;">Fecha:</span>
                                    </td>
                                    <td style="padding: 8px 0; color: #333333; font-size: 14px;">{{ $fecha_cita }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; vertical-align: top;">
                                        <span style="font-weight: bold; color: #495057; font-size: 14px;">Hora:</span>
                                    </td>
                                    <td style="padding: 8px 0; color: #333333; font-size: 14px;">{{ $hora_cita }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; vertical-align: top;">
                                        <span style="font-weight: bold; color: #495057; font-size: 14px;">Clínica:</span>
                                    </td>
                                    <td style="padding: 8px 0; color: #333333; font-size: 14px;">{{ $sucursal }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 25px 30px; text-align: center;">
                <a href="https://experiencia.sycardigital.cl/encuesta/detalle-encuesta/{{ $id_envio_encuesta }}" style="text-decoration:none; color:#fff; padding: 8px; background: #1bbab9;">Ver Detalle</a>
            </td>
        </tr>

        <!-- NPS & Comment Section -->
        <tr>
            <td style="padding: 0 30px 40px 30px;">
                <table style="width: 100%; background-color: #fff3f3; border-radius: 10px; border-collapse: collapse; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="padding: 25px;">
                            <h4 style="color: #e63946; margin: 0 0 20px 0; font-size: 18px; font-weight: bold; text-align: center; border-bottom: 2px solid #f5c2c7; padding-bottom: 15px;">Detalles de la Encuesta</h4>
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #495057;">
                                <strong>{{ $pregunta_nps }}</strong><br>
                                <span style="color: #e63946; font-size: 16px; font-weight: bold;">{{ $respuesta_nps }}</span>
                            </p>
                            <p style="margin: 15px 0 0 0; font-size: 14px; color: #495057;">
                                <strong>{{ $pregunta_comentario }}</strong><br>
                                <span style="color: #333333; font-style: italic;">"{{ $respuesta_comentario }}"</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #00ad9c; padding: 25px 30px; text-align: center;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: center; padding-bottom: 15px;">
                            <h4 style="color: #ffffff; margin: 0; font-size: 16px; font-weight: bold;">Recomendamos dar seguimiento a este caso</h4>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #ffffff; font-size: 14px; opacity: 0.9; line-height: 1.4;">
                            Centro Odontológico Padre Mariano<br>
                            La sonrisa nos une.
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">
                            <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 15px; color: #ffffff; font-size: 12px; opacity: 0.8;">
                                Este correo fue enviado automáticamente. Por favor, no responda a este mensaje.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
