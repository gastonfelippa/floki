<h1><strong> Reporte diario de Ventas </strong> </h1>

<p>Hoy, {{ now()->format('d-m-Y') }} se han registrado ventas por un total de {{ $total }}.</p>
@if ($resumen->count() > 0)
<p>Las ventas han sido:</p>
<div>
    <table>
        <tr class="bg-indigo-400 bg-opacity-100 text-white">
            <th class="text-left">Fact. N° </th>
            <th class="text-left">Importe</th>
        </tr>
        <tbody>
            @foreach ($resumen as $i)
                <tr class="border-b-2">
                    <td> {{ $i->numero }} </td>
                    <td> {{ $i->importe }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>  
@else
<p>No se registraron ventas...</p>  
@endif



