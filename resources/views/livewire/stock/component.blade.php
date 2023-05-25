<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
    <div class="col-sm-12 col-md-8 layout-spacing">             
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h3><b>{{$title}}</b></h3>
                    </div> 
                </div>  		
                <div class="row justify-content-between">
                    <div class="col-sm-12 col-md-6 mb-1">
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
                </div>
                @if($action == 1)
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                                             
                                    <th class="">DESCRIPCIÓN</th>
                                    @if($comercioTipo == 10 || $comercioTipo == 11)
                                        <th class="text-center">LOCAL</th>
                                        @if($comercioTipo == 10)
                                            <th class="text-center">CONDICIONAL</th>
                                        @else
                                            <th class="text-center">EN CONSIGNACIÓN</th>
                                        @endif
                                        <th class="text-center">TOTAL</th>
                                    @else
                                        <th class="text-center">CANTIDAD</th>
                                    @endif
                                    @can('Productos_create')
                                    <th class="text-right">PRECIO COSTO</th>
                                    <th class="text-right">IMPORTE</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                            @if($mostrar_subproducto == 0)
                                @foreach($info as $r)
                                <tr>                    
                                    <td>{{$r->descripcion}}</td>
                                    <td class="text-center">{{number_format($r->stock_local,3,',','.')}}</td>
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
                                    <td class="text-right">{{number_format($r->precio_costo,2,',','.')}}</td>
                                    <td class="text-right">{{number_format($r->subtotal,2,',','.')}}</td>
                                    @endcan
                                </tr>
                                @endforeach
                            @else
                                @foreach($info_sp as $r)
                                <tr>                    
                                    <td>{{$r->descripcion}}</td>
                                    <td class="text-center">{{$r->stock_local}}</td>
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
                                    <td class="text-right">{{number_format($r->precio_costo,2,',','.')}}</td>
                                    <td class="text-right">{{number_format($r->subtotal,2,',','.')}}</td>
                                    @endcan
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
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
                @include('livewire.stock.modal')
                @include('livewire.stock.modalHistorial')
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
                    @can('Productos_create')                  
                        <h5><b>Total Stock:</b> $ {{number_format($valorTotalStockPorConsignatario,2,',','.')}}</h5>                 
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>
 
<style type="text/css" scoped>
    .scroll{
        position: relative;
        height: 235px;
        margin-top: .5rem;
        overflow: auto;
        margin-bottom: 10px;
    }
    thead tr th {     /* fija la cabecera de la tabla */
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
    function recargarPagina()
    {
        window.location.href="{{ url('stock') }}";
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
    }
</script>
