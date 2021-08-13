@extends('layouts.template_comandas')

@section('styles')
    <style type="text/css">
        body {font-family: sans-serif; text-align:left; }
        /* #detalle {border-style:solid;} */
        #div {border-style:solid;padding:5px;}
        /* #detalle {border-style:solid;margin: 30px; padding:25px; display:inline-block;} */
        #detalle {background-color: #F2F3F4;}
        /* #detalle {background-color: yellow;} */
        td {
          font-size: 40px;
          color:green;
        }
    </style>
@endsection

@section('content')
     
  @livewire('comanda-controller')

@endsection