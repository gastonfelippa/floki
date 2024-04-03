<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmail;
use App\Events\UserRegistered;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //quitar 'email_verified_at' una vez que funcione la autenticacion en la nube 
    protected $fillable = [
        'name', 'apellido', 'username', 'email', 'password', 'pass', 'documento',
        'calle', 'numero', 'localidad_id', 'telefono1', 'telefono2', 'sexo', 'abonado',
        'fecha_nac', 'fecha_ingreso', 'categoria_id', 'estado', 'comercio_id', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

        /**
         * Send the password reset notification.
         * 
         * @param string token
         * @return void
         */

    public function sendPasswordResetNotification($token)
    {   
        $this->notify(new ResetPasswordNotification($token));
    }

        /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    protected static function booted(){
        static::updated(function($user){
        });
    }
}
