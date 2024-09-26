@extends('layouts.template_con_sessions')

@section('logo')     
  @livewire('logo-controller')
@endsection



@if(session('modComandas') == "1")
<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        if(tecla==13) window.location.href="{{ url('reservas-estado-mesas') }}";
    }
</script>
@endif