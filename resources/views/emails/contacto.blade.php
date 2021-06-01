<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Document</title>
</head>
<body>
    <h1>Correo Electrónico</h1>
    <p>Correo enviado desde Laravel</p>
    <p><strong>Nombre:</strong>{{$contacto['name']}}</p>
    <p><strong>Correo:</strong>{{$contacto['correo']}}</p>
    <p><strong>Mensaje:</strong>{{$contacto['mensaje']}}</p>
</body>
</html>