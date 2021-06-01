@extends('layouts.template')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('title', 'Contáctanos')

@section('content')
<div class="row layout-top-spacing justify-content-center">
    <div class="col-sm-12 col-md-6 text-center"> 		
		<div class="widget-content-area">
			<div class="widget-one">
                <h3>Déjanos un mensaje</h3>
                <form action="{{route('contactanos.store')}}" method="POST">
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
                    <input type="text" name="correo" class="col-sm-6">                    
                    <br>
                    
                    @error('correo')
                        <p><strong>{{$message}}</strong></p>
                    @enderror

                    <label>Mensaje:</label>
                    <br>
                    <textarea name="mensaje" rows="2" class="col-sm-6"></textarea>                    
                    <br>

                    @error('mensaje')
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
@endsection 