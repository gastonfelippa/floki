<div class="modal fade" id="modalHistorialStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row col-12">
                    <h5 class="modal-title">Cliente: {{$nombreCliente}}</br>Producto: {{$producto}}</h5>
                </div>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-left">FECHA</th>
                                        <th class="text-left">COMPROBANTE</th>
                                        <th class="text-center">CANTIDAD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockHistorial as $r)
                                    <tr>
                                        <td class="text-left">{{\Carbon\Carbon::parse(strtotime($r->created_at))->format('d-m-Y')}}</td>
                                        <td class="text-left">{{$r->tipo_comprobante}}-{{str_pad($r->num_comprobante, 6, '0', STR_PAD_LEFT)}}</td>
                                        <td class="text-center">{{number_format($r->cantidad,0)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" wire:click="recargarPagina()" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Volver</button>
            </div>
        </div>
    </div>
</div>