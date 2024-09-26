<div class="modal fade" id="modalDetalleGastosOperativos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title2"></h5>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div class="row">
                            <div class="table-responsive scroll-small">
                                <table class="table table-hover table-checkable table-sm">
                                    <thead class="encabezado">
                                        <tr>
                                            <th>DESCRIPCION</th>
                                            <th class="text-right">IMPORTE</th>
                                        </tr>
                                    </thead>
                                    <tbody class="contenido">
                                        @foreach($det_gastos_operativos as $r)
                                        <tr>
                                            <td>{{$r->descripcion}}</td>
                                            <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-6">                          
                                <h6>TOTAL: $ {{number_format($total_det_g_oper,2,',','.')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal" onclick="ver_gastos_operativos()"></i>Volver</button>
            </div>
        </div>
    </div>
</div>

