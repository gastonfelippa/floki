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

@section('content')

  @livewire('factura-bar-controller')

@endsection
