<!-- mensaje que se envía al empleado agregado -->
{{-- @component('mail::message')    --}}
<x-mail::message>   
# Hola {{$user->name}}!!<br>
# {{$comercio}} te da la bienvenida a su equipo de trabajo!

Estas son tus credenciales para que puedas ingresar al sistema:<br><br>
**USUARIO:** {{$user->username}}<br>
**CONTRASEÑA:** {{$user->pass}}<br><br>
Luego de ingresar podrás modificar tu contraseña.<br>
¡Esperamos que lo disfrutes tanto como nosotros!

PD: si no creaste ninguna cuenta, podés ignorar o eliminar este email.

Saludos,<br>
{{ $admin }}
</x-mail::message>   
{{-- @endcomponent --}}
