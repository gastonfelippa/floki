@extends('layouts.template')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('permisos-controller')

@endsection