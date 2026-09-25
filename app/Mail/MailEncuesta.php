<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailEncuesta extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($datos_envio, $link_encuesta)
    {
        //
        $this->datos_envio = $datos_envio;
        $this->link_encuesta = $link_encuesta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Encuesta Experiencia - Centro Odontológico Padre Mariano',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.encuesta',
            with: [
                'nombre_paciente'   => $this->datos_envio['nombre_paciente'],
                'nombre_dentista'   => $this->datos_envio['nombre_dentista'],
                'fecha_cita'        => $this->datos_envio['fecha_cita'],
                'hora_cita'         => $this->datos_envio['hora_cita'],
                'sucursal'          => $this->datos_envio['sucursal'],
                'link_encuesta'     => $this->link_encuesta
            ]
        );
    }
}
