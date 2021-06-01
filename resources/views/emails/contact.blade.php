
@component('mail::message')
# Hola, {{$name}} bienvenida a FlokI!

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
<!-- <!DOCTYPE html>
<html lang="en">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nuevo contacto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
 
<body>
    <div class="container">
    <h1>Bienvenido</h1>
    <h4>Muchas gracias por unirte a nuestro servicio!!!<h4>
    <p>Estos son los datos para que puedas ingresar al sistema:</p>

    <p><strong>Usuario:</strong>{{$user}}</p>
    <p><strong>Contraseña:</strong>{{$contraseña}}</p>  

    <button class="btn btn-dark">INGRESAR</button><br>

    <p>Luego de ingresar podrás modificar tu contraseña.</p>
    <p>¡Esperamos que lo disfrutes tanto como nosotros!</p>
    <h4>El equipo de SisGNF</h4> 
        </div>
</body>
 
</html> -->