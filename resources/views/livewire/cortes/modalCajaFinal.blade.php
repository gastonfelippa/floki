<div class="modal fade" id="modalCajaFinal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one"> 
                        <div id="modalCajaFinal2" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-right">EFECTIVO</th>
                                        <th class="text-right">T. DÉBITO</th>
                                        <th class="text-right">T. CRÉDITO</th>
                                        <th class="text-right">TRANSFERENCIA</th>
                                        <th class="text-right">CHEQUE</th>
                                        <th class="text-right">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    @foreach($cajaI as $r)
                                        <td class="text-right">{{$r}}</td>
                                    @endforeach
                                    </tr>
                                    <tr>
                                    @foreach($ingresosPorVentas as $r)
                                        <td class="text-right">{{$r}}</td>
                                    @endforeach
                                    </tr>
                                    <tr>
                                    @foreach($cobros as $r)
                                        <td class="text-right">{{$r}}</td>
                                    @endforeach
                                    </tr>
                                    <tr>
                                    @foreach($gastos as $r)
                                        <td class="text-right">{{$r}}</td>
                                    @endforeach
                                    </tr>
                                    <tr>
                                    @foreach($totales as $r)
                                        <td class="text-right"><b>{{$r}}</b></td>
                                    @endforeach
                                    </tr>
                                </tbody>
                            </table>                   
                        </div>  
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Volver</button>
            </div>
        </div>
    </div>
</div>