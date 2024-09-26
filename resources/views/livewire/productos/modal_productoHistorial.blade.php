<div class="modal fade" id="modal_productoHistorial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Historial de compras de: <b>{{$producto}}</b></h5>
        </div>       
        <div class="modal-body">
            <div class="widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="table-responsive scroll-small">
                            <table class="table table-hover table-checkable table-sm">
                                <thead class="encabezado">
                                    <tr>
                                        <th class="text-left">FECHA</th>
                                        <th class="text-left">CANT</th>
                                        <th class="text-left">PROVEEDOR</th>
                                        @can('Categorias_create')
                                        <th class="text-center">COSTO</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody class="contenido">
                                    @foreach($infoHistorial as $r)
                                    <tr>
                                    <td class="text-left">{{\Carbon\Carbon::parse(strtotime($r->fecha_fact))->format('d-m-Y')}}</td>

                                        <td class="text-center">{{$r->cantidad}}</td>
                                        <td class="text-left">{{$r->nombre_empresa}}</td>
                                        @can('Categorias_create')
                                        <td class="text-center">{{$r->precio}}</td>
                                        @endcan
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-dark" data-dismiss="modal">Volver</button>
            <!-- <button class="btn btn-primary" type="button" onclick="saveProductoProveedor()">Guardar</button> -->
        </div>
    </div>
</div>
</div>