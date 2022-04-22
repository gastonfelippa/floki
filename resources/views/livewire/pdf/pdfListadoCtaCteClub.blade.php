@extends('layouts.pdf')

@section('content')
<div>
    <h5 class="text-center">Listado de Cuentas Corrientes</h5>
    <div><span class="text-left" style="font-weight: bold;font-size:16px;">Total: $ {{number_format($suma,2,',','.')}}</span></div>
    <div><span class="text-right" style="font-size:12px;">Fecha: {{\Carbon\Carbon::now()->format('d-m-Y')}}</span></div>
    <br>
    <div class="col-6 offset-3">
    <table class="table table-hover table-checkable table-sm">
        <thead style="font-size:12px">
            <tr>
                <th class="text-left">SOCIO</th>
                <th class="text-right">IMPORTE</th>
            </tr>
        </thead>
        <tbody style="font-size:12px">
            @foreach($info as $r)
            <tr> 
                <td class="text-left">{{$r->apellido}}, {{$r->nombre}}</td>
                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>  
      </div>             
                 
</div>
@endsection