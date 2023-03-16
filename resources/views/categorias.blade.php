@extends('layouts.template_con_sessions')
@extends('layouts.bootstrap5')

@section('logo')

  @livewire('logo-controller')

@endsection

@section('content')
     
  @livewire('categoria-controller')

@endsection
