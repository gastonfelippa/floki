@extends('layouts.template',[
  'modComandas'       => session('modComandas'),
  'modConsignaciones' => session('modConsignaciones'),
  'modViandas'        => session('modViandas'),
  'modDelivery'       => session('modDelivery'),
  'modClubes'         => session('modClubes'),
  'comercioTipo'      => session('tipoComercio')
])

@if(session('modComandas') == "1")
  <script type="text/javascript">
      window.onkeydown=PulsarTecla;
      function PulsarTecla(e)
      {
        if (e.ctrlKey || e.metaKey) {  //redirige con 'ctrl+m'
              if(String.fromCharCode(e.which).toLowerCase() === 'm') {
                window.location.href="{{ url('reservas-estado-mesas') }}";
              }
          }

        tecla = event.keyCode;  //redirige con 'enter'
        if(tecla==13) window.location.href="{{ url('reservas-estado-mesas') }}";

        if (event.key == 'F1') { // redirige con la tecla F1            
            event.preventDefault();
            window.location.href="{{ url('reservas-estado-mesas') }}";
        }
      }
  </script>
@endif