<!-- Hola <i>{{ $demo->receiver }}</i>,
 
<p><u>Estos son los datos para que puedas ingresar al sistema:</u></p>
 
<div>
<p><b>USUARIO:</b>&nbsp;{{ $demo->demo_one }}</p>
<p><b>CONTRASEÑA:</b>&nbsp;{{ $demo->demo_two }}</p>
</div>
<p>Luego de ingresar podrás modificar tu contraseña.</p>
<p>¡Esperamos que lo disfrutes tanto como nosotros!</p>
 
<p>Si no has creado ninguna cuenta, puedes ignorar o eliminar este e-mail.</p>
 
<div>
<p><b>testVarOne:</b>&nbsp;{{ $testVarOne }}</p>
<p><b>testVarTwo:</b>&nbsp;{{ $testVarTwo }}</p>
</div>
 
Saludos,
<br/>
<i>{{ $demo->sender }}</i> -->

@component('mail::message')
# Hola, {{$demo->receiver}} bienvenida a FlokI!

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent