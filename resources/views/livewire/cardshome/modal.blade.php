<div class="modal fade" id="modalCardsHome" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div id="modalReservas" class="table-responsive scrollmodal">                    
                            <ul class="nav nav-tabs mt-1" id="myTab" role="tablist">
                                @if($infoReservasA->count() > 0)
                                <li class="nav-item">
                                    <a class="nav-link {{$tab == 'asignada' ? 'active' : ''}}" id="home-tab" data-toggle="tab" href="#asignada" role="tab" aria-controls="home" aria-selected="true">Asignadas</a>
                                </li>
                                @endif
                                @if($infoReservasP->count() > 0)
                                <li class="nav-item">
                                    <a class="nav-link {{$tab == 'sinasignar' ? 'active' : ''}}" id="profile-tab" data-toggle="tab" href="#sinasignar" role="tab" aria-controls="profile" aria-selected="true"><b>Sin Asignar</b></a>
                                </li>
                                @endif
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show {{$tab == 'asignada' ? 'active' : ''}}" id="asignada" role="tabpanel" aria-labelledby="home-tab">
                                    <table class="table table-hover table-checkable table-sm mb-4">
                                        <thead>
                                            <tr>
                                                <th>HORARIO</th>
                                                <th>CLIENTE</th>
                                                <th class="text-center">CANTIDAD</th>
                                                <th class="text-center">MESA N°</th>
                                                <th class="text-center">ACCION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($infoReservasA as $r)
                                            <tr>
                                                <td>{{$r->horario}}</td>
                                                <td>{{$r->apellido}} {{$r->nombre}}</td>
                                                <td class="text-center">{{$r->cantidad}}</td>
                                                <td class="text-center">{{$r->mesaDesc}}</td>
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="abrirReservaAsignada({{$r->mesa_id}})"  
                                                    data-toggle="tooltip" data-placement="top" title="Abrir mesa">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>  
                                                </td> 
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table> 
                                </div>
                            
                                <div class="tab-pane fade show {{$tab == 'sinasignar' ? 'active' : ''}}" id="sinasignar" role="tabpanel" aria-labelledby="home-tab">
                                
                                    <table class="table table-hover table-checkable table-sm mb-4">
                                        <thead>
                                            <tr>
                                                <th>HORARIO</th>
                                                <th>CLIENTE</th>
                                                <th class="text-center">CANTIDAD</th>
                                                <th class="text-center">MESA N°</th>
                                                <th class="text-center">ACCION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($infoReservasP as $r)
                                            <tr>
                                                <td>{{$r->horario}}</td>
                                                <td>{{$r->apellido}} {{$r->nombre}}</td>
                                                <td class="text-center">{{$r->cantidad}}</td>
                                                <td class="text-center">{{$r->mesaDesc}}</td>
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="abrirReservaPendiente({{$r->id}})"  
                                                    data-toggle="tooltip" data-placement="top" title="Asignar mesa">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>  
                                                </td> 
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table> 
                                </div>
                            
                                <div class="tab-pane fade show {{$tab == 'sinasignar' ? 'active' : ''}}" id="sinasignar" role="tabpanel" aria-labelledby="home-tab">
                                    <table class="table table-hover table-checkable table-sm mb-4">
                                        <thead>
                                            <tr>
                                                <th>HORARIO</th>
                                                <th>CLIENTE</th>
                                                <th class="text-center">CANTIDAD</th>
                                                <th class="text-center">MESA N°</th>
                                                <th class="text-center">ACCION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($infoReservasP as $r)
                                            <tr>
                                                <td>{{$r->horario}}</td>
                                                <td>{{$r->apellido}} {{$r->nombre}}</td>
                                                <td class="text-center">{{$r->cantidad}}</td>
                                                <td class="text-center">{{$r->mesaDesc}}</td>
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="abrirReservaPendiente({{$r->id}})"  
                                                    data-toggle="tooltip" data-placement="top" title="Asignar mesa">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>  
                                                </td> 
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table> 
                                </div>
                            </div>                  
                        </div>                 
                        <div id="modalProductosCompra" class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th>DESCRIPCION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoProductosCompra as $r)
                                    <tr>
                                        <td>{{$r}}</td>                                
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalProductosVenta" class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th>DESCRIPCION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoProductosVenta as $r)
                                    <tr>
                                        <td>{{$r}}</td>                                
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalPedidos" class="table-responsive scrollmodal">
                            <ul class="nav nav-tabs mt-1" id="myTab" role="tablist">
                                @if($infoPedidosC->count() > 0)
                                <li class="nav-item">
                                    <a class="nav-link {{$tabP == 'cargado' ? 'active' : ''}}" id="home-tab" data-toggle="tab" href="#cargado" role="tab" aria-controls="home" aria-selected="true">A realizar</a>
                                </li>
                                @endif
                                @if($infoPedidosP->count() > 0)
                                <li class="nav-item">
                                    <a class="nav-link {{$tabP == 'pedido' ? 'active' : ''}}" id="profile-tab" data-toggle="tab" href="#pedido" role="tab" aria-controls="profile" aria-selected="true"><b>A recibir</b></a>
                                </li>
                                @endif
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show {{$tabP == 'cargado' ? 'active' : ''}}" id="cargado" role="tabpanel" aria-labelledby="home-tab">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>PROVEEDOR</th>
                                                <th class="text-right">IMPORTE</th>
                                                <th class="text-center">ACCION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($infoPedidosC as $r)
                                            <tr>
                                                <td>{{$r->nombre_empresa}}</td>  
                                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td> 
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="abrirPedido"  
                                                    data-toggle="tooltip" data-placement="top" title="Abrir pedido">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>  
                                                </td>                                                                        
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>                   
                                </div>
                                <div class="tab-pane fade show {{$tabP == 'pedido' ? 'active' : ''}}" id="pedido" role="tabpanel" aria-labelledby="home-tab">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>PROVEEDOR</th>
                                                <th class="text-right">IMPORTE</th>
                                                <th class="text-center">ACCION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($infoPedidosP as $r)
                                            <tr>
                                                <td>{{$r->nombre_empresa}}</td>  
                                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>   
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="abrirPedido"  
                                                    data-toggle="tooltip" data-placement="top" title="Abrir pedido">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
                                                </td>                                                                        
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>                   
                                </div>
                            </div>
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