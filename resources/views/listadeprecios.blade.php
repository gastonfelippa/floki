@extends('layouts.template')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')

  @livewire('lista-de-precios-controller')

@endsection
