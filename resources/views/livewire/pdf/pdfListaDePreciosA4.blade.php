@extends('layouts.pdf')

@section('content') 
<div>
	<h5 class="text-center">Lista de Precios - L{{$listaNumero}}</h5>
    <div><span class="text-right" style="font-size:12px;">Fecha: {{\Carbon\Carbon::now()->format('d-m-Y')}}</span></div>
    <br>
    <table class="table table-hover table-checkable table-sm">
        <thead style="font-size:12px">
            <tr>
				<th class="text-center">CODIGO</th>
            	<th class="text-left">DESCRIPCION</th>
            	<th class="text-right mr-3">PRECIO</th>
            </tr>
        </thead>
        <tbody style="font-size:12px">
            @foreach($info as $r)
            <tr> 
				<td class="text-center">{{$r->codigo}}</td>
				<td class="text-left">{{$r->descripcion}}</td>
				<td class="text-right mr-3">{{$r->precio}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>   
</div>
@endsection


            