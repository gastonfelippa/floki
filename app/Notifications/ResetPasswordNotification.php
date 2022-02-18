<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     * @var string
     */

    public $token;

    /**
     * @param string $token
     * @return void
     */

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $count = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');
        return (new MailMessage)
            ->subject('Solicitud de restablecimiento de contraseña')
            ->greeting('Hola ' . $notifiable->name . '!')
            ->line('Se solicitó un restablecimiento de contraseña para tu cuenta ' . $notifiable->username . ', haz clic en el botón que aparece a continuación para cambiar tu contraseña.')
            ->action(('Cambiar contraseña'), url(config('app.url') . route('password.reset',['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->line('Si no realizaste la solicitud de cambio de contraseña, solo ignora este mensaje. ')
            ->line('Este enlace solo es válido dentro de los proximos : ' . $count . ' minutos.' )
            ->salutation('Saludos!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
