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

                                    <td class="text-center">{{number_format($r->cantidad,3,',','.')}}</td>
                                    <td class="text-left">{{$r->nombre_empresa}}</td>
                                    @can('Categorias_create')
                                    <td class="text-center">{{number_format($r->precio,2,',','.')}}</td>
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
                                    <td class="text-center" style="background:#F09F8F;">{{number_format($r->cantidad,3,',','.')}}</td>
                                    <td class="text-left" style="background:#F09F8F;">{{$r->producto}}</td>
                                    @can('Categorias_create')
                                    <td class="text-center">{{number_format($r->precio_costo,2,',','.')}}</td>
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
                                    <td class="text-center">{{number_format($r->stock_actual,3,',','.')}}</td>
                                    <td class="text-center">{{number_format($r->stock_ideal,3,',','.')}}</td>
                                    <td class="text-center">{{number_format($r->stock_minimo,3,',','.')}}</td>
                                    @if($r->cantidad_pedido > 0)
                                    <td class="text-center" style="background:#F1907D;">{{number_format($r->cantidad_pedido,3,',','.')}}</td>
                                    <td style="background:#F09F8F;">{{$r->descripcion}}</td>
                                    @else
                                    <td class="text-center" style="background:#13BE05;">0,000</td>
                                    <td style="background:#25DA0C;">{{$r->descripcion}}</td>
                                    @endif
                                    <td class="text-center">
                                        <ul class="table-controls">
                                        <li>
											<a href="javascript:void(0);" 
											wire:click="buscarHistorial({{$r->productoId}})" 
											data-toggle="tooltip" data-placement="top" title="Ver historial de compras">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock-history text-success" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/></svg></a>
										</li>
                                            <li>
                                                @if($r->item_pedido)
                                                <a href="javascript:void(0);"          		
                                                data-toggle="tooltip" data-placement="top" title="Item pedido">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart-check text-danger" viewBox="0 0 16 16"><path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/><path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg></a>
                                                @else
                                                <a href="javascript:void(0);"         		
                                                onclick="openModal({{$r->productoId}},'{{$r->descripcion}}',{{$r->cantidad_pedido}},0)"
                                                data-toggle="tooltip" data-placement="top" title="Agregar al pedido">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg></a>
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
                                    <td class="text-center" style="background:#F09F8F;">{{number_format($r->cantidad,3,',','.')}}</td>
                                    <td class="text-left" style="background:#F09F8F;">{{$r->producto}}</td>
                                    @can('Categorias_create')
                                    <td class="text-center">{{number_format($r->precio_costo,2,',','.')}}</td>
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
