@extends('layouts.template_admin')

@section('logo')
     
  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('procedimientos-admin-controller')

@endsection
