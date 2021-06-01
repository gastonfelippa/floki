<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $contraseña;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($user, $contraseña)
    {
        $this->user = $user;
        $this->contraseña = $contraseña;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->markdown('emails.contact')
        ->from('sisgnf@gmail.com', config('app.name'))
        ->subject('Correo de confirmación')
        ->greeting('Prueba saludo');
    }
}
