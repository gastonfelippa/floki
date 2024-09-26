@extends('layouts.pdf')

@section('content')
<div >
        <!-- calcula el total de viandas  -->
        @php
            $suma=0;
        @endphp
        @foreach ($info as $r)
            @php
                $suma+=$r->cantidad;
            @endphp             
        @endforeach
        <!--  -->
	<h5 class="text-center">Viandas {{\Carbon\Carbon::now()->format('d-m-Y')}} ({{$suma}})</h5>
    <table class="table table-hover table-checkable table-sm">
        <thead style="font-size:12px">
            <tr>
                <th class="text-center">HORA</th>
                <th class="text-center">CLIENTE</th>
                <th class="text-center">CANT</th>
                <th class="text-center">COMENTARIO</th>
            </tr>
        </thead>
        <tbody style="font-size:12px">
            @foreach($info as $r)
            <tr> 
                <td class="text-center">{{\Carbon\Carbon::parse($r->hora)->format('H:i')}}</td>
                <td class="text-left">{{$r->apellido}}, {{$r->nombre}}</td>
                <td class="text-center">{{$r->cantidad}}</td>
                <td class="text-center">{{$r->comentarios}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>                 
</div>
@endsection