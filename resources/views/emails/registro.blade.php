<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Document</title>
</head>
<body>
    <h1>Bienvenido</h1>
    <h4>Muchas gracias por unirte a nuestro servicio!!!<h4><br>
    <p>Estos son los datos para que puedas ingresar al sistema:</p><br>
    <p><strong>Usuario:</strong>VER</p>
foreach($data as $d){

    <p><strong>Contraseña:</strong>{{$d->contraseña}}</p>
}

    <button class="btn btn-dark">INGRESAR</button><br>
    <p>Luego de ingresar podrás modificar tu contraseña.</p><br><br>
    <p>¡Esperamos que lo disfrutes tanto como nosotros!</p><br>
    <h4>El equipo de SisGNF</h4> 

</body>
</html>