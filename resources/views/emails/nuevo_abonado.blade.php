<!-- mensaje enviado al Admin Floki -->
@component('mail::message')
# Grande Mirlooo!!<br>
# Tenés un nuevo abonado!!!

Estos son sus datos:<br><br>
**NOMBRE:** {{$user->apellido}} {{$user->name}}<br>
**COMERCIO:** {{$comercio}}<br><br>
Felicitaciones...<br>
¡Vamos por muchos mas!

@endcomponent
