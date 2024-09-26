@extends('layouts.template_club')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('movimientos-de-caja-controller')

@endsection