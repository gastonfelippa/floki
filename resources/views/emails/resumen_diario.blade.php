<h1><strong> Reporte diario de Ventas </strong> </h1>

<p>Hoy, {{ now()->format('d-m-Y') }} se han registrado ventas por un total de $ {{ $total }}.</p>
@if ($resumen->count() > 0)
<p>Las ventas han sido:</p>
<div>
    <table>
        <tr>
            <th class="text-center">Fact. N° </th>
            <th class="text-center">Importe</th>
        </tr>
        <tbody>
            @foreach ($resumen as $i)
                <tr>
                    <td class="text-center"> {{ $i->numero }} </td>
                    <td class="text-center"> {{ $i->importe }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>  
@else
<p>No se registraron ventas...</p>  
@endif



