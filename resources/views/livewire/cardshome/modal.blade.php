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
                        <div id="modalProductos" class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th>DESCRIPCION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoProductos as $r)
                                    <tr>
                                        <td>{{$r->descripcion}}</td>                                
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalPedidos" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>PROVEEDOR</th>
                                        <th class="text-center">ACCION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoPedidos as $r)
                                    <tr>
                                        <td>{{$r->nombre_empresa}}</td>  
                                        <td class="text-center">                                 
                                            <a href="javascript:void(0);"
                                            wire:click="abrirPedido"  
                                            data-toggle="tooltip" data-placement="top" title="Abrir pedido">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>  
                                        </td>                                                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                        <div id="modalMesas" class="table-responsive scrollmodal">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">MESA N°</th>
                                        <th>MOZO</th>
                                        <th class="text-center">IMPORTE</th>
                                        <th class="text-center">ACCION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoMesas as $r)
                                    <tr>
                                        <td class="text-center">{{$r->mesa}}</td>                                  
                                        <td>{{$r->name}} {{$r->apellido}}</td>                                  
                                        <td class="text-right">{{$r->importe}}</td> 
                                        <td class="text-center">                                 
                                            <a href="javascript:void(0);"
                                            wire:click="abrirMesa({{$r->mesa_id}})"  
                                            data-toggle="tooltip" data-placement="top" title="Abrir mesa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>  
                                        </td>                                 
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