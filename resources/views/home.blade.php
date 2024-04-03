@extends('layouts.template_con_sessions')

@section('logo')
  @livewire('logo-controller')
@endsection

@if(session('modComandas') == "1")
  @section('content')
    @livewire('cards-home-controller')
  @endsection
@endif

{{-- @if(session('modComandas') == "1")
  <script type="text/javascript">
      window.onkeydown=PulsarTecla;
      function PulsarTecla(e)
      {
        if (e.ctrlKey || e.metaKey) {
              if(String.fromCharCode(e.which).toLowerCase() === 'm') {
                window.location.href="{{ url('reservas-estado-mesas') }}";
              }
          }
          tecla = event.keyCode;
          if(tecla==13) window.location.href="{{ url('reservas-estado-mesas') }}";
      }
  </script>
@endif --}}