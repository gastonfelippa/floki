<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
    <div class="col-sm-12 col-md-8 layout-spacing">             
        <div class="widget-content-area"style="border-bottom-left-radius:0px;border-bottom-right-radius:0px;">
            <div class="widget-one">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h3><b>{{$title}}</b></h3>
                    </div> 
                </div>  		
                <div class="row justify-content-between">
                    <div class="col-sm-12 col-md-6">
                        @if($action == 1)
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                                </div>
                                <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="{{$placeHolderSearch}}" aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                            </div>
                        @else
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg></span>
                                </div>
                                <select wire:model="cliente" class="form-control form-control-sm">
                                    <option value="Elegir">Cliente</option>
                                    @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">
                                        {{$c->apellido}} {{$c->nombre}}
                                    </option>                                       
                                    @endforeach 
                                </select>
                            </div>
                        @endif
                    </div>
                    {{-- @if($comercioTipo == 10 || $comercioTipo == 11)
                    <div class="col-sm-12 col-md-6">
                        @if($action == 1)
                            <button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
                            @if($comercioTipo == 10)
                                <span style="text-decoration: underline;">V</span>er Condicional Por Cliente</button>
                            @else 
                                <span style="text-decoration: underline;">V</span>er Stock Por Consignatario</button> 
                            @endif
                        @else
                            <button id="btnNuevo" type="button" wire:click="doAction(1)" class="btn btn-danger btn-block">
                            <span style="text-decoration: underline;">V</span>er Stock Local</button>
                        @endif
                    </div>
                    @endif --}}
                </div>
                @if($action == 1)
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                                             
                                    <th>DESCRIPCIÓN</th>
                                    @if($comercioTipo == 10 || $comercioTipo == 11)
                                        <th class="text-center">LOCAL</th>
                                        @if($comercioTipo == 10)
                                            <th class="text-center">CONDICIONAL</th>
                                        @else
                                            <th class="text-center">EN CONSIGNACIÓN</th>
                                        @endif
                                        <th class="text-center">TOTAL</th>
                                    @else
                                        <th class="text-center">CANTIDAD/UN. DE MEDIDA</th>
                                    @endif
                                    @can('Productos_create')
                                    <th class="text-right">PRECIO COSTO</th>
                                    <th class="text-right">IMPORTE</th>
                                    <th class="text-center">ACCIONES</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>
                                    @if ($r->stock_local < 0)                    
                                        <td style="color: red">{{$r->descripcion}}</td>
                                    @else
                                        <td>{{$r->descripcion}}</td>
                                    @endif
                                    {{-- intval() convierte a entero y floatval() conviente a decimal, si los dos son iguales
                                        significa que no hay decimales --}}                                    
                                    @if (intval($r->stock_local) == floatval($r->stock_local)) {{--  si no hay decimales --}}
                                        @if ($r->stock_local < 0)
                                            <td class="text-center" style="color: red;">{{number_format($r->stock_local,0)}} {{$r->unidad_de_medida}}</td>
                                        @else
                                            <td class="text-center">{{number_format($r->stock_local,0)}} {{$r->unidad_de_medida}}</td>
                                        @endif
                                    @else
                                        @if ($r->unidad_de_medida == 'Un')
                                            @if ($r->stock_local < 0)
                                                <td class="text-center" style="color: red;">{{number_format($r->stock_local,1,',','.')}} {{$r->unidad_de_medida}}</td>
                                            @else
                                                <td class="text-center">{{number_format($r->stock_local,1,',','.')}} {{$r->unidad_de_medida}}</td>
                                            @endif
                                        @else
                                            @if ($r->stock_local < 0)
                                                <td class="text-center" style="color: red;">{{number_format($r->stock_local,3,',','.')}} {{$r->unidad_de_medida}}</td>
                                            @else
                                                <td class="text-center">{{number_format($r->stock_local,3,',','.')}} {{$r->unidad_de_medida}}</td>
                                            @endif
                                        @endif
                                    @endif
                                    @if($modConsignaciones == '1')
                                        <td class="text-center">
                                            <ul class="table-controls">
                                                <li>
                                                    {{$r->stock_en_consignacion}} 
                                                </li>
                                                @if($search)
                                                    @if($r->stock_en_consignacion != null)    
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                        wire:click="verStockEnConsignacion({{$r->id}},{{$r->producto}})"  
                                                        data-toggle="tooltip" data-placement="top" title="Ver stock por Consignatario">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                                    </li>
                                                    @endif
                                                @endif
                                            </ul>
                                        </td>
                                        <td class="text-center">{{$r->stock_total}}</td>
                                    @endif
                                    @can('Productos_create')
                                    @if ($r->stock_local < 0)
                                        <td class="text-right" style="color: red;">{{number_format($r->precio_costo,2,',','.')}}</td>
                                        <td class="text-right" style="color: red;">{{number_format($r->subtotal,2,',','.')}}</td>
                                    @else
                                        <td class="text-right">{{number_format($r->precio_costo,2,',','.')}}</td>
                                        <td class="text-right">{{number_format($r->subtotal,2,',','.')}}</td>
                                    @endif
                                        <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                <a href="javascript:void(0);"   
                                                onclick="openModalHistorial({{$r->id}})"
                                                data-toggle="tooltip" data-placement="top" title="Historial de movimientos">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock-history text-success" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/></svg>                                               
                                            </li>
                                        </ul>
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- @can('Productos_create')
                    @if($modConsignaciones == "1")
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mt-1"><b>Stock Local:</b> $ {{number_format($valorTotalStockLocal,2,',','.')}}</h6>
                        </div>
                        <div class="col-12">
                            <h6 class="mt-1"><b>Stock En Consignación:</b> $ {{number_format($valorTotalStockConsignacion,2,',','.')}}</h6>
                        </div>
                        <div class="col-12">
                            <h5><b>Total Stock:</b> $ {{number_format($valorTotalStock,2,',','.')}}</h5>  
                        </div>
                    </div>
                    @else
                        <h5><b>Total Stock:</b> $ {{number_format($valorTotalStock,2,',','.')}}</h5>
                    @endif
                    @endcan --}}
                @include('livewire.stock.modal')
                @include('livewire.stock.modalHistorial')
                @include('livewire.stock.modal_productoHistorial')	
                @include('livewire.stock.modal_detalleHistorialStock')	
                @else
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                               
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-left">DESCRIPCION</th>
                                    @can('Productos_create')
                                    @if($modConsignaciones == '1')
                                        @if($comercioTipo == 11) <!-- consignacion -->
                                            <th class="text-right">PRECIO LISTA 2</th>
                                        @else                  <!-- tienda -->
                                            <th class="text-right">PRECIO LISTA 1</th>
                                        @endif
                                        <th class="text-right">IMPORTE</th>
                                    @endif
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($infoCli as $r)
                                <tr>  
                                    @if($r->cantidad > 0)                   
                                        <td class="text-center">{{number_format($r->cantidad,3,',','.')}}</td>
                                        <td class="text-left">{{$r->articuloDesc}}</td>
                                        @can('Productos_create')
                                        <td class="text-right">{{number_format($r->precio_venta,2,',','.')}}</td>
                                        <td class="text-right">{{number_format($r->subtotal,2,',','.')}}</td>
                                        @endcan
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- @can('Productos_create')    
                        <h5><b>Total Stock:</b> $ {{number_format($valorTotalStockPorConsignatario,2,',','.')}}</h5>                 
                    @endcan --}}
                @endif
            </div>
        </div>
        <div class="widget-content-area" style="background-color:#4eb1a0;border-top-left-radius:0px;
            border-top-right-radius:0px;">
            <div class="widget-one">
                @if($comercioTipo == 10 || $comercioTipo == 11)
                <div class="col-sm-12 col-md-6">
                    @if($action == 1)
                        <button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
                        @if($comercioTipo == 10)
                            <span style="text-decoration: underline;">V</span>er Condicional Por Cliente</button>
                        @else 
                            <span style="text-decoration: underline;">V</span>er Stock Por Consignatario</button> 
                        @endif
                    @else
                        <button id="btnNuevo" type="button" wire:click="doAction(1)" class="btn btn-danger btn-block">
                        <span style="text-decoration: underline;">V</span>er Stock Local</button>
                    @endif
                </div>
                @endif
                @if($action == 1)
                    @can('Productos_create')
                    @if($modConsignaciones == "1")
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mt-1"><b>Stock Local:</b> $ {{number_format($valorTotalStockLocal,2,',','.')}}</h6>
                        </div>
                        <div class="col-12">
                            <h6 class="mt-1"><b>Stock En Consignación:</b> $ {{number_format($valorTotalStockConsignacion,2,',','.')}}</h6>
                        </div>
                        <div class="col-12">
                            <h5><b>Total Stock:</b> $ {{number_format($valorTotalStock,2,',','.')}}</h5>  
                        </div>
                    </div>
                    @else
                        <h5><b>Total Stock:</b> $ {{number_format($valorTotalStock,2,',','.')}}</h5>
                    @endif
                    @endcan
                @else
                    @can('Productos_create')    
                        <h5><b>Total Stock:</b> $ {{number_format($valorTotalStockPorConsignatario,2,',','.')}}</h5>                 
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
    function recargarPagina()
    {
        window.location.href="{{ url('stock') }}";
    } 
    function openModalHistorial(id)
    { 
        $('#modal_productoHistorial').modal('hide');
        $('#modal_detalleHistorialStock').modal('hide'); 
        window.livewire.emit('productoHistorial', id);
    } 
    window.onload = function() {
        document.getElementById("search").focus();
        Livewire.on('abrirModal',()=>{
            $('#modalStock').modal('show');
		})
        Livewire.on('abrirModalHistorial',()=>{
            $('#modalStock').modal('hide');
            $('#modalHistorialStock').modal('show');
		})
        Livewire.on('abrirModalHistorialStock',()=>{
            $('#modal_detalleHistorialStock').modal('hide');
            $('#modal_productoHistorial').modal('show'); 
		})
        Livewire.on('abrirModalDetalleHistorialStock',()=>{
            $('#modal_productoHistorial').modal('hide');
            $('#modal_detalleHistorialStock').modal('show'); 
		})
    }
</script>
