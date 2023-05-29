@extends('layouts.template_con_sessions')

@section('logo')     
  @livewire('logo-controller')
@endsection

@section('content')

<div class="row layout-top-spacing">
    <div class="col-12 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one"> 
            @include('common.alerts')               
                @if(Auth::user()->sexo == 1)
                    <h1 class="text-center">Bienvenida <strong>{{Auth::user()->name}}!!!</strong></h1>
                @else
                    <h1 class="text-center">Bienvenido <strong>{{Auth::user()->name}}!!!</strong></h1>
                @endif             
            </div>
            <!-- <div class="visible-print text-center">
                {!! QrCode::size(100)->generate("www.floki.ar/productos") !!}
                <p>Escanéame para hacer tu pedido.</p>
            </div> -->
            <!-- <form action="{{ route('importar.productos.excel') }}" method="post" enctype="multipart/form-data">
                @csrf 
                @if(Session::has('message'))
                <p>{{ Session::get('message') }}</p>
                @endif
                <input type="file" name="file">
                <button>Importar Productos</button>
            </form> -->
        </div>
    </div>
</div>
@endsection

@if(session('modComandas') == "1")
<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        // if(tecla==13) window.location.href="{{ url('abrir-mesa') }}";
        if(tecla==13) window.location.href="{{ url('reservas-estado-mesas') }}";
    }
    
    /////código para prolongar la session
        var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 120000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
</script>
@endif