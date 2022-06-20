@extends('layouts.template',[
  'modComandas'       => session('modComandas'),
  'modConsignaciones' => session('modConsignaciones'),
  'modViandas'        => session('modViandas'),
  'modDelivery'       => session('modDelivery'),
  'modClubes'         => session('modClubes')
])

@section('logo')     
  @livewire('logo-controller')
@endsection



@if(session('modComandas') == "1")
<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        if(tecla==13) window.location.href="{{ url('mesas') }}";
    }
</script>
@endif