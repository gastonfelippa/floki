@extends('layouts.template_con_sessions')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('ventas-por-fechas-controller')

@endsection