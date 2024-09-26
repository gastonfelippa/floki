<h1><strong> Reporte diario con la visita del blog </strong> </h1>

<p>Hoy, {{now()->format('d-m-Y')}} se han registrado un total de {{count($visits)}} visitas en la web.</p>

<p>Las páginas vistas han sido:</p>
<div>
    <table>
        <tr class="bg-indigo-400 bg-opacity-100 text-white">
                <th class="text-left">Página</th>
                <th class="text-left">Fecha</th>

        </tr>
        <tbody>
            @foreach($visits as $visit)
                <tr class="border-b-2">
                    <td>  {{$visit->name}}   </td>
                    <td>  {{$visit->created_at}}   </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
