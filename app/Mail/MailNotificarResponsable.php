<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailNotificarResponsable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($nombre_paciente, $nombre_dentista, $fecha_cita, $hora_cita, $sucursal, $pregunta_nps, $respuesta_nps, $pregunta_comentario, $respuesta_comentario, $id_envio_encuesta)
    {
        $this->nombre_paciente      = $nombre_paciente;
        $this->nombre_dentista      = $nombre_dentista;
        $this->fecha_cita           = $fecha_cita;
        $this->hora_cita            = $hora_cita;
        $this->sucursal             = $sucursal;
        $this->pregunta_nps         = $pregunta_nps;
        $this->respuesta_nps        = $respuesta_nps;
        $this->pregunta_comentario  = $pregunta_comentario;
        $this->respuesta_comentario = $respuesta_comentario;
        $this->id_envio_encuesta    = $id_envio_encuesta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Encuesta Experiencia - Centro Odontológico Padre Mariano - NPS Negativo',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.notificar',
            with: [
                'nombre_paciente'           => $this->nombre_paciente,
                'nombre_dentista'           => $this->nombre_dentista,
                'fecha_cita'                => $this->fecha_cita,
                'hora_cita'                 => $this->hora_cita,
                'sucursal'                  => $this->sucursal,
                'pregunta_nps'              => $this->pregunta_nps,
                'respuesta_nps'             => $this->respuesta_nps,
                'pregunta_comentario'       => $this->pregunta_comentario,
                'respuesta_comentario'      => $this->respuesta_comentario,
                'id_envio_encuesta'         => $this->id_envio_encuesta,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
