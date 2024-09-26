<div class="modal fade" id="modalCajaRep" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div id="modalCajaInicial" class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-left">CAJA</th>
                                        <th class="text-left">FECHA/HORA INICIO</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoCajaInicial as $r)
                                    <tr>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-left">{{\Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i')}}</td>
                                        <td class="text-right">{{number_format($r->sumaImporte,2,',','.')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>                 
                        <div id="modalVentas" class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-left">CAJA</th>
                                        <th class="text-center">VENTAS</th>
                                        <th class="text-center">COBROS CTA CTE</th>
                                        <th class="text-center">OTROS INGRESOS</th>
                                        <th class="text-center">TOTALES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaVentas as $r)
                                    <tr>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-right">{{number_format($r->sumaVentas,2,',','.')}}</td>  
                                        <td class="text-right">{{number_format($r->sumaCobros,2,',','.')}}</td>    
                                        <td class="text-right">{{number_format($r->sumaOtIngresos,2,',','.')}}</td>  
                                        <td class="text-right" style="font-weight: bold;">{{number_format($r->sumaIngresosTotal,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalCobros" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left">CAJA</th>
                                        <th class="text-left">FECHA/HORA INICIO</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaVentas as $r)
                                    <tr>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-left">{{\Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i')}}</td>
                                        <td class="text-right">{{number_format($r->sumaImporte,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalIngresos" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left">DESCRIPCION</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaVentas as $r)
                                    <tr>
                                        <td class="text-right">{{number_format($r->sumaImporte,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalEgresos" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left">CAJA</th>
                                        <th class="text-left">FECHA/HORA INICIO</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaEgresos as $r)
                                    <tr>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-left">{{\Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i')}}</td>
                                        <td class="text-right">{{number_format($r->sumaImporte,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalCajaFinal">
                            <div class="row mb-1">
                                <div class="col-7">
                                    <b>Caja Inicial</b>
                                </div>
                                <div class="col-1 text-right">
                                    <b>$</b>
                                </div>
                                <div class="col-3 text-right">
                                    <b>{{number_format($cajaInicial,2,',','.')}}</b>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-7">
                                    <b>Total Ingresos</b> 
                                </div>
                                <div class="col-1 text-right">
                                    <b>$</b>
                                </div>
                                <div class="col-3 text-right">
                                    <b>{{number_format($totalIngresos,2,',','.')}}</b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <b>Total Egresos</b>
                                </div>
                                <div class="col-1 text-right">
                                    <b>$</b>
                                </div>
                                <div class="col-3 text-right">
                                    <b>- {{number_format($egresos,2,',','.')}}</b>
                                </div>                        
                            </div>
                            <hr>                          
                            <div class="row" style="color: #ff7f26">
                                <div class="col-7">
                                    <b>CAJA FINAL SISTEMA</b>
                                </div>
                                <div class="col-1 text-right">
                                    <b>$</b>
                                </div>
                                <div class="col-3 text-right">
                                    <b>{{number_format($cajaFinal,2,',','.')}}</b>
                                </div>
                            </div>
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