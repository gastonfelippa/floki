@extends('layouts.template_con_sessions')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')

  @livewire('factura-controller')

@endsection
