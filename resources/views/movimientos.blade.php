@extends('layouts.template')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('caja-controller')

@endsection