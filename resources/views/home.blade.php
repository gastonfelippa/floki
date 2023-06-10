@extends('layouts.template_con_sessions')

@section('logo')     
  @livewire('logo-controller')
@endsection

@if(session('modComandas') == "1")
  @section('content')
    @livewire('cards-home-controller')
  @endsection
@endif

@if(session('modComandas') == "1")
<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        if(tecla==13) window.location.href="{{ url('reservas-estado-mesas') }}";
    }
    
    /////código para prolongar la session
    //     var keep_alive = false;
    // $(document).bind("click keydown keyup mousemove", function() {
    //     keep_alive = true;
    // });
    // setInterval(function() {
    //     if ( keep_alive ) {
    //         pingServer();
    //         keep_alive = false;
    //     }
    // }, 120000 );
    // function pingServer() {
    //     $.ajax('/keepAlive');
    // }
    /////
</script>
@endif