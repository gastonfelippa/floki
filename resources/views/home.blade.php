@extends('layouts.template')

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
        </div>
    </div>
</div>
@endsection

<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        if(tecla==13) window.location.href="{{ url('mesas') }}";
    }
</script>