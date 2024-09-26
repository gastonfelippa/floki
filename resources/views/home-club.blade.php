
@extends('layouts.template_club')

@section('logo')     
  @livewire('logo-controller')
@endsection

@section('content')

<div class="row layout-top-spacing">
    <div class="col-12 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one"> 
            @include('common.alerts')               
                @if(Auth::user()->sexo == 1)
                    <h1 class="text-center">Bienvenida <strong>{{Auth::user()->name}}!!!</strong></h1>
                @else
                    <h1 class="text-center">Bienvenido <strong>{{Auth::user()->name}}!!!</strong></h1>
                @endif             
            </div>
            <!-- <div class="visible-print text-center">
                {!! QrCode::size(100)->generate("www.floki.ar") !!}
                <p>Escanéame para hacer tu pedido.</p>
            </div> -->
        </div>
    </div>
</div>
@endsection
