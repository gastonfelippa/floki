<div class="modal fade" id="modal_detalleHistorialStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                    <h5 class="modal-title">Historial de {{$data}} de: <br>
                    <b style="color: coral">{{$productoStock}}</b>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div class="table-responsive scroll-small">
                            <table class="table table-hover table-checkable table-sm">
                                <thead class="encabezado">
                                    <tr>
                                        <th class="text-center">FECHA</th> 
                                        <th class="text-center">HORA</th>
                                        @if ($dataAccion == 4 || $dataAccion == 5)                                        
                                            <th class="text-center">ACCION</th>
                                        @else
                                            <th class="text-center">CANTIDAD</th>
                                        @endif
                                        @if ($dataAccion == 5)                                        
                                            <th class="text-center">==>></th>
                                            <th class="text-center">REACCION</th>
                                        @endif 
                                        <th class="text-center">USUARIO</th>
                                    </tr>
                                </thead>
                                <tbody class="contenido">
                                    @foreach($infoHistorialStock as $r)
                                    <tr>
                                        <td class="text-center">{{$r->created_at->format('d-m-Y')}}</td>
                                        <td class="text-center">{{$r->created_at->format('H:i')}}</td>
                                        {{-- accion --}}
                                        @if ($dataAccion == 5)
                                            <td class="text-center">{{$r->accion_prod_modif}} {{$r->cant_prod_modif}} {{$r->descProdModif}}</td>
                                            <td class="text-center">=></td>
                                            <td class="text-center">{{$r->accion}} {{$r->cantidad}} {{$productoStock}}</td>
                                        @endif
                                        {{-- reaccion --}}
                                        @if ($dataAccion == 4)
                                            <td class="text-center">{{$r->accion}} {{$r->cantidad}}</td>
                                        @endif
                                        @if ($dataAccion != 4 && $dataAccion != 5)
                                            <td class="text-center">{{$r->cantidad}}</td>
                                        @endif
                                        <td class="text-center">{{$r->user}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal">Volver</button>
            </div>
        </div>
    </div>
</div>