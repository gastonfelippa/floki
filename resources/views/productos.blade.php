@extends('layouts.template',[
    'modComandas'       => session('modComandas'),
    'modConsignaciones' => session('modConsignaciones'),
    'modViandas'        => session('modViandas'),
    'modDelivery'       => session('modDelivery')
  ])

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('producto-controller')

@endsection
