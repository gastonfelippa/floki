<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Resumen_Diario extends Mailable
{
    use Queueable, SerializesModels;
    public $total, $resumen;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($total, $resumen)
    {
        $this->total = $total;
        $this->resumen = $resumen;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.resumen_diario');
    }
}
