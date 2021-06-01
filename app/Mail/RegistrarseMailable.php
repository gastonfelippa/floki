<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrarseMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Información de registro"; 

    public $contacto, $contraseña;

    public $greeting = 'Hola!';
    public $line = 'Se solicitó un restablecimiento de contraseña para tu cuenta ';

    public $salutation='Saludos!';


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contraseña)
    {

        $this->$contraseña =$contraseña;

        dd($this->contraseña);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.registro');
    }
}
