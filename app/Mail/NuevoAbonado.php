<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

class NuevoAbonado extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $comercio;
    /**
     * Create a new message instance.
     *
     * @return void
     */


    public function __construct($user, $comercio)
    {
        $this->user = $user;
        $this->comercio = $comercio;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.nuevo_abonado')->subject('Nuevo Abonado!!!');
    }
}
