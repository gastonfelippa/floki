<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PruebaCron extends Mailable
{
    use Queueable, SerializesModels;
    public $visits;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */


    public function __construct($visits)
    {
        $this->visits = $visits;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.prueba_cron');
        // return $this->markdown('emails.prueba_cron')->subject('Prueba Cron!!!');
    }
}