@extends('layouts.template_con_sessions')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('venta-diaria-controller')

@endsection
