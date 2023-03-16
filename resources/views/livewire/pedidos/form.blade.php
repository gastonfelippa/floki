<div class="col-sm-12 col-md-10 layout-spacing">    
    <div class="widget-content-area">
        <div class="widget-one">
            @include('common.messages')
            <div class="row">
                @if($estadoPedido == 'cargado')
                <div class="col-sm-12 col-md-6">
                @else
                <div class="col-12">
                @endif
                    <div class="row layout-content-between">
                        <div class="col-4">
                            <h3><b>Pedido</b></h3>
                        </div>
                        <div class="col-8 text-right">
                            <div id="btnGroup" class="btn-group" role="group"> 
                                @if($estadoPedido == "realizado")
                                    <button type="button" class="btn btn-danger" onclick="RecibirPedido()">
                                        Recibir Pedido
                                    </button>
                                @elseif($estadoPedido == "cargado")
                                    @if($pedido_id)
                                    <button type="button" class="btn btn-outline-danger" title="Imprimir Pedido">
                                        <a href="{{url('pdfpedidos',array($pedido_id))}}" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg></a>
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="HacerPedido()">
                                        Hacer Pedido
                                    </button>
                                    @endif
                                @endif
                                <button type="button" class="btn btn-dark" wire:click="doAction(1)">
                                    Volver
                                </button>
                            </div>  
                        </div>
                    </div>
                    @if(!$empresa)
                    <div class="row">     
                        <div class="col-12 layout-spacing">
                            <label >Proveedor</label>
                            <select id="nombre" wire:model="proveedor" class="form-control text-left">
                                <option value="Elegir">Elegir</option>
                                @foreach($proveedores as $t)
                                <option value="{{ $t->id }}">
                                    {{$t->nombre_empresa}}
                                </option>                                       
                                @endforeach                              
                            </select>
                        </div>  
                    </div>
                    @else	
                    <div class="row layout-content-between mt-1">
                        <div class="col-sm-12 col-md-6">
                            <h5><b>{{$empresa}}</b></h5>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <h5><b>Total: $ {{number_format($total,2,',','.')}}</b></h5>
                        </div>
                    </div>
                    @endif
                </div>
                @if($estadoPedido == 'cargado')
                <div class="col-sm-12 col-md-6">
                    <p>Historial de compras de: <b>{{$producto}}</b></p>
                @else
                <div class="col-12">
                @endif
                    @if($producto)
                    <div class="table-responsive scrollHistorial">
                    @else
                    <div class="table-responsive scrollHistorialCorto">
                    @endif
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
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @if($estadoPedido == "realizado" || $estadoPedido == "recibido")
                <li class="nav-item">
                    <a class="nav-link {{$tab == 'realizado' ? 'active' : ''}}" style="color:red;" id="pedido-tab" data-toggle="tab" href="#pedido" role="tab" aria-controls="pedido" aria-selected="true"><b>Detalle Pedido</b></a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link {{$tab == 'sugerido' ? 'active' : ''}}" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><b>Pedido Sugerido</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$tab == 'pedido' ? 'active' : ''}}" style="color:red;" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true"><b>Detalle Pedido</b></a>
                </li>
                @endif
            </ul>
            <div class="tab-content" id="myTabContent">
                @if($estadoPedido == "realizado" || $estadoPedido == "recibido")
                <div class="tab-pane fade show {{$tab == 'realizado' ? 'active' : ''}}" id="pedido" role="tabpanel" aria-labelledby="pedido-tab">
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-left">PRODUCTO</th>
                                    @can('Categorias_create')
                                    <th class="text-center">COSTO/UN.</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($infoDetPedido as $r)
                                <tr>
                                    <td class="text-center" style="background:#F09F8F;">{{$r->cantidad}}</td>
                                    <td class="text-left" style="background:#F09F8F;">{{$r->producto}}</td>
                                    @can('Categorias_create')
                                    <td class="text-center">{{$r->precio_costo}}</td>
                                    @endcan
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="tab-pane fade show {{$tab == 'sugerido' ? 'active' : ''}}" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">STOCK ACTUAL</th>
                                    <th class="text-center">STOCK IDEAL</th>
                                    <th class="text-center">STOCK MÍNIMO</th>
                                    <th class="text-center">REPONER</th>
                                    <th class="text-left">PRODUCTO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($infoPedido as $r)
                                <tr>
                                    <td class="text-center">{{$r->stock_actual}}</td>
                                    <td class="text-center">{{$r->stock_ideal}}</td>
                                    <td class="text-center">{{$r->stock_minimo}}</td>
                                    <td class="text-center" style="background:#F09F8F;">{{$r->cantidad_pedido}}</td>
                                    <td style="background:#F09F8F;">{{$r->descripcion}}</td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                        <li>
											<a href="javascript:void(0);" 
											wire:click="buscarHistorial({{$r->productoId}})" 
											data-toggle="tooltip" data-placement="top" title="Ver historial de compras">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>                                 
										</li>
                                            <li>
                                                @if($r->item_pedido)
                                                <a href="javascript:void(0);"          		
                                                data-toggle="tooltip" data-placement="top" title="Item pedido">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" style="font-weight: bold;" class="bi bi-check2 text-danger" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg></a>
                                                @else
                                                <a href="javascript:void(0);"         		
                                                onclick="openModal({{$r->productoId}},'{{$r->descripcion}}',{{$r->cantidad_pedido}},0)"
                                                data-toggle="tooltip" data-placement="top" title="Agregar al pedido">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16"><path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/></svg></a>
                                                @endif
                                            </li>
                                        </td>
                                    </tr>
                                </ul>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show {{$tab == 'pedido' ? 'active' : ''}}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-left">PRODUCTO</th>
                                    @can('Categorias_create')
                                    <th class="text-center">COSTO/UN.</th>
                                    @endcan
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($infoDetPedido as $r)
                                <tr>
                                    <td class="text-center" style="background:#F09F8F;">{{$r->cantidad}}</td>
                                    <td class="text-left" style="background:#F09F8F;">{{$r->producto}}</td>
                                    @can('Categorias_create')
                                    <td class="text-center">{{$r->precio_costo}}</td>
                                    @endcan
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);" 
                                                onclick="openModal({{$r->producto_id}},'{{$r->producto}}',{{$r->cantidad}},1)"
                                                data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);"          		
                                                onclick="ConfirmItem('{{$r->id}}')"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>	
    </div>
</div>
