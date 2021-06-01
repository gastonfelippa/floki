@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <h3 class="mt-5 text-center col-sm-12 col-md-8">ATENCIÓN!!!<br>TU PLAN ESTÁ EN MORA...</h3>
        <h2 class="mt-2 text-center col-sm-12 col-md-8">Debes regularizar la situación porque en los 
            próximos días se precederá a la suspensión de la suscripción del plan.<br>¡Muchas gracias!
        </h2>
    </div>
    <div class="mt-2 text-center">
        <a href="{{ url('notify') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> 
            Continuar
        </a>
    </div> 
@endsection