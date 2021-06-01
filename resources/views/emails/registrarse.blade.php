<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Document</title>
</head>

<body>



    <h1>Correo Electrónico</h1>
    <p>Registrarse en SisGNF</p>


    <div class="row layout-top-spacing justify-content-center">
    <div class="col-sm-12 col-md-6 text-center"> 		
		<div class="widget-content-area">
			<div class="widget-one">
                <h3>Déjanos un mensaje</h3>
                <form action="{{route('registrarse.store')}}" method="POST">
                    @csrf
                    <label>Nombre:</label>
                    <br>
                    <input type="text" name="name" class="col-sm-6">                    
                    <br>

                    @error('name')
                        <p><strong>{{$message}}</strong></p>
                    @enderror

                    <label>Correo:</label>
                    <br>
                    <input type="text" name="email" class="col-sm-6">                    
                    <br>
                    
                    @error('email')
                        <p><strong>{{$message}}</strong></p>
                    @enderror

                    <label>telefono:</label>
                    <br>
                    <input type="text" name="telefono" class="col-sm-6">                    
                    <br>

                    @error('telefono')
                        <p><strong>{{$message}}</strong></p>
                    @enderror

                    <button type="submit">Enviar mensaje</button>
                </form>

                @if (session('info'))
                    <script>
                        alert("{{session('info')}}");
                    </script>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>