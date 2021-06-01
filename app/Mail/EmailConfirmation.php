<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;
    public $demo;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($demo)
    {
        //$this->demo = $demo;
        $this->demo = $demo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // ->text('emails.demo_plain')
        return $this->from('floki@gmail.com', config('app.name'))
        ->subject('Bienvenido/a a FlokI!')
        ->view('emails.demo')
        ->with(
          [
                'testVarOne' => '1',
                'testVarTwo' => '2',
          ])
          ->attach(public_path('/images').'/coche.jpg', [
                  'as' => 'coche.jpg',
                  'mime' => 'image/jpeg',
          ]);
        //return $this->markdown('emails.users.confirmation')->subject('Por favor confirma tu correo');
    }
}
