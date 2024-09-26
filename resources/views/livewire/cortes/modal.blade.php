<div class="modal fade" id="modalCaja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <th class="text-left">FECHA/HORA</th>
                                        <th class="text-left">TIPO</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoCajaInicial as $r)
                                    <tr>
                                        <td class="text-left">{{\Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i')}}</td>
                                        <td class="text-left">{{$r->tipoIngreso}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>                 
                        <div id="modalVentas" class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-left">N° COMPROBANTE</th>
                                        <th class="text-left">CLIENTE</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaVentas as $r)
                                    <tr>
                                        <td class="text-left">FAC-{{str_pad($r->numero, 6, '0', STR_PAD_LEFT)}}</td>
                                        <td class="text-left">{{$r->nomCli}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalCobros" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left">N° COMPROBANTE</th>
                                        <th class="text-left">CLIENTE</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaCobros as $r)
                                    <tr>
                                        <td class="text-left">REC-{{str_pad($r->numero, 6, '0', STR_PAD_LEFT)}}</td>
                                        <td class="text-left">{{$r->apellido}} {{$r->nombre}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>                                  
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
                                    @foreach($listaIngresos as $r)
                                    <tr>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalEgresos" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left">DESCRIPCION</th>
                                        <th class="text-right">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaEgresos as $r)
                                    <tr>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>                                  
                                    </tr>
                                    @endforeach
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