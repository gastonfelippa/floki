@extends('layouts.template')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('ventas-por-fechas-controller')

@endsection