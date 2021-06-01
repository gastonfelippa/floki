@extends('layouts.template')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('gasto-controller')

@endsection
