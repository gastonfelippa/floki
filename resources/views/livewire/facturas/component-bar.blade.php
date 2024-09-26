<div class="row layout-top-spacing justify-content-center">
    @if($action == 1)	
    <div id="factura" class="col-md-12 col-lg-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one widget-h">
                @if($delivery != 1)
                    <div class="row">
                        @if($entrega > 0)
                            <div class="row col-4 ml-1">
                                <div>
                                    <h6 class="bg-danger p-2" style="border-radius: 5px;">Mesa: {{$mesaDesc}}</h6>
                                </div>
                                <div>
                                        <!-- <span class="badge bg-dark p-2">Mozo: {{$mozoDesc}} </span></p> -->
                                    <h6 id="mozo" class="bg-danger p-2" style="border-radius: 5px;">Mozo: {{$mozoDesc}}</h6>
                                </div>
                            </div>                        
                            <div class="row col-4">
                                <div>
                                    <span class="badge bg-dark text-right" style="width:140px;">Total: $ {{number_format($total,2,',','.')}}</span>
                                </div>
                                <div>
                                    <span class="badge bg-dark text-right" style="width:140px;">Entrega: $ {{number_format($entrega,2,',','.')}}</span>
                                </div>
                                <div>
                                    <span class="badge bg-danger text-right" style="width:140px;">Saldo: $ {{number_format($saldo,2,',','.')}}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <button id="btnAgregar" type="button" onclick= "verBotones()" title="Agregar"class="btn btn-dark mr-1" enabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>                                       
                                </button>
                                <div id="btnGroup" class="btn-group" role="group"> 
                                    <button type="button" class="btn btn-outline-danger" title="Imprimir"
                                        onclick="grabarImpresion">
                                        <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg></a>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" title="Cobrar"
                                        wire:click="doAction(2)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16"><path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/></svg>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" title="Anular"
                                        onclick="AnularFactura({{$factura_id}})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                    </button>
                                    <button type="button" class="btn btn-dark" title="Salir"
                                        onclick="Salir()">Salir
                                    </button>
                                    <button type="button" class="btn btn-dark" title="Salir"
                                        onclick="Salir()">Salir
                                    </button>
                                </div> 
                            </div>
                        @else
                            <div class="row col-8">
                                <div class="col-5">
                                <h6 class="bg-danger p-1" style="border-radius: 5px;">Mesa: {{$mesaDesc}}</h6>
                                </div>
                                <div class="col-7">                                    
                                    <h6 class="bg-danger p-1" style="border-radius: 5px;">Total: $ {{number_format($total,2,',','.')}}</h6> 
                                </div>
                            </div>  
                            <div class="col-4">
                                <button id="btnAgregar" type="button" onclick= "verBotones()" title="Agregar"class="btn btn-dark mr-1" enabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>                                       
                                </button>
                                <div id="btnGroup" class="btn-group" role="group">                                     
                                    @if($total > 0)
                                        <button type="button" class="btn btn-outline-danger" title="Imprimir"
                                            onclick="grabarImpresion()">
                                            <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg></a>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Cobrar"
                                            wire:click="doAction(2)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16"><path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/></svg>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Anular"
                                            onclick="AnularFactura({{$factura_id}})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-danger" title="Imprimir" disabled>
                                            <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg></a>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Cobrar" disabled
                                            wire:click="doAction(2)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16"><path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/></svg>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Anular" disabled
                                            onclick="AnularFactura({{$factura_id}})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-dark" title="Salir"
                                        onclick="Salir()">Salir
                                    </button>
                                </div>                               
                            </div>
                            <div class="ml-3">
                                <span class="badge bg-dark">Mozo: {{$mozoDesc}}</span></p>
                            </div>                            
                        @endif       
                    </div>
                    @if($camarero)
                    <div class="row">
                        <div class="col-md-3">
                            <div style="font-size:13px;"><b>Fact. N° </b> {{str_pad($numFactura, 6, '0', STR_PAD_LEFT)}} </div>
                            <div style="font-size:13px;"><b>Fecha</b> {{\Carbon\Carbon::parse(strtotime($fecha))->format('d-m-Y')}} </div>
                        </div>                    
                        <div class="col-md-9 text-right">
                            <div class="btn-group mb-2" role="group" aria-label="Basic mixed styles example">            
                            @if($total != 0)
                                <button type="button" wire:click="doAction(2)" 
                                    class="btn btn-primary" enabled>
                                    Cobrar   
                                </button>
                                <button type="button" class="btn btn-outline-success" enabled
                                    onclick="grabarImpresion()">
                                <!-- <a id="link">
                                Imprimir</a> -->
                                    <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                    Imprimir</a>
                                </button>
                                <button type="button" onclick="AnularFactura({{$factura_id}})" 
                                    class="btn btn-info" enabled>
                                    Anular Factura 
                                </button>
                            @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @else       <!-- SI ES DELIVERY -->
                    @if($total == 0)   <!-- si es delivery y es inicio de factura -->
                        <div class="row mt-2">
                            <div class="col-6">
                                <h6>Cliente:  {{$apeNomCli}}</h6>
                            </div>
                            <div class="col-6">
                                <h6>Rep:  {{$apeNomRep}}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h6>Dirección:  {{$dirCliente}}</h6>
                            </div>
                            <div class="col-6">
                                @if($saldoCtaCte < 0)
                                    <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                                @else
                                    <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                                @endif
                            </div>
                        </div>       
                    @else   <!-- si muestra datos en BD de la factura -->
                        <div class="row mt-2">
                            <div class="col-6">
                                <h6>Cliente:  {{$encabezado[0]->apeCli}} {{$encabezado[0]->nomCli}}</h6>
                            </div>
                            <div class="col-6">
                                <h6>Rep:  {{$encabezado[0]->apeRep}} {{$encabezado[0]->nomRep}}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h6>Dirección:  {{$encabezado[0]->calle}} {{$encabezado[0]->numero}}</h6>
                            </div>
                            <div class="col-6">
                                @if($saldoCtaCte < 0)
                                    <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                                @else
                                    <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                                @endif
                            </div>
                        </div>                
                    @endif 
                    <div class="row">
                        <div class="col-4 ml-2 mt-1">                             
                            <!-- <span class="badge bg-dark text-right" style="width:140px;">Total: $ {{number_format($total,2,',','.')}}</span> -->
                            <h6 class="bg-danger p-2" style="border-radius: 5px;">Total: $ {{number_format($total,2,',','.')}}</h6> 
                        </div>                        
                        <div class="btn-group btn-sm col-7" role="group"> 
                            <button type="button" class="btn btn-outline-danger" title="Modificar Cliente/Repartidor" enabled 
                                onclick="openModal({{$factura_id}})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                            </button>
                            <button type="button" class="btn btn-outline-danger" title="Imprimir" enabled
                                onclick="grabarImpresion()">
                                <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg></a>
                            </button>
                            <button type="button" class="btn btn-outline-danger" title="Cobrar"
                                wire:click="doAction(2)" enabled>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16"><path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/></svg>
                            </button>
                            <button type="button" class="btn btn-outline-danger" title="Anular"
                                onclick="AnularFactura({{$factura_id}})" enabled>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" color="black" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                            </button>
                            <button type="button" class="btn btn-dark" title="Dejar Pendiente" enabled 
                                onclick="dejar_pendiente()">
                                Salir
                            </button>                               
                        </div>
                    </div>         
                @endif

                @include('common.alerts')
                       
                @if($delivery != 1)
                    <ul class="nav nav-tabs mt-1" id="myTab" role="tablist">
                        <li class="nav-item">
                            @if($info)
                                <a class="nav-link {{$tab == 'factura' ? 'active' : ''}}" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><b>Factura</b></a>
                            @else
                                <a class="nav-link {{$tab == 'factura' ? 'active' : ''}}" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Factura</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            @if($infoComanda)
                                <a class="nav-link {{$tab == 'comanda' ? 'active' : ''}}" style="color:red;" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><b>Comanda</b></a>
                            @else
                                <a class="nav-link {{$tab == 'comanda' ? 'active' : ''}}" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Comanda</a>
                            @endif
                        </li>
                        @if($infoComanda)
                        <li class="nav-item">
                                <a class="nav-link {{$tab == 'enviarComanda' ? 'active' : ''}}" wire:click="enviarComanda()" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Enviar Comanda</a>
                            </li>
                            @endif
                        <li class="nav-item">
                            @if($infoComandaEnEspera)
                                <a class="nav-link {{$tab == 'verComanda' ? 'active' : ''}}" style="color:red;" onclick="verCom()" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home2" aria-selected="false"><b>Ver Comanda Enviada</b></a>
                            @else
                                <!-- <a class="nav-link {{$tab == 'verComanda' ? 'active' : ''}}" onclick="verCom()" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home2" aria-selected="false">Ver Comanda Enviada</a> -->
                            @endif
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show {{$tab == 'factura' ? 'active' : ''}}" id="home" role="tabpanel" aria-labelledby="home-tab">
                @endif
                            <div class="table-responsive scroll">
                                <table style="background:#F2E8E6;" class="table table-hover table-checkable table-sm mb-4">
                                    <thead>
                                        <tr style="background:#DBD1CF" >
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">DESCRIPCIÓN</th>
                                            <th class="text-right">P/UNITARIO</th>
                                            <th class="text-right">IMPORTE</th>
                                            <!-- <th class="text-center">ACCIONES</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($info as $r)
                                        <tr class="">
                                         <td class="text-center">   
                                        <div class="btn-group" role="group">
                                            @if(!$r->sectorcomanda_id)
                                                <button type="button" wire:click="StoreOrUpdateButton({{$r->producto_id}},0, {{$r->id}}, {{$r->precio}})" class="btn btn-success">-</button>
                                            @else
                                                <button type="button" onclick="descontar_producto()" class="btn btn-success">-</button>
                                            @endif
                                            <button type="button" class="btn btn-primary">{{number_format($r->cantidad,0)}}</button>
                                            <button type="button" style="font: size 35px;" wire:click="StoreOrUpdateButton({{$r->producto_id}},1, {{$r->id}}, {{$r->precio}})" class="btn btn-success">+</button>
                                        </div>
                                            </td>
                                            <td class="text-left">{{$r->producto}}</td>
                                            <td class="text-right">{{number_format($r->precio,2,',','.')}}</td>
                                            <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                   
                            </div>
                @if($delivery != 1)
                        </div>
                        <div class="tab-pane fade show {{$tab == 'comanda' ? 'active' : ''}}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            @if($infoComanda)    
                            <div class="table-responsive scroll">
                                <table class="table table-hover table-checkable table-sm mb-4">
                                    <thead>
                                        <tr>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">DESCRIPCIÓN</th>
                                            <th class="text-center">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($infoComanda as $r)
                                        <tr>
                                            <td class="text-center">{{number_format($r->cantidad,0)}}</td>
                                            <td class="text-left">{{$r->descripcion}}</td>
                                            <td class="text-center">
                                            @include('common.actions-destroy-item', ['destroy' => 'Facturas_destroy_item'])
                                            </td>                                        
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                   
                            </div> 
                            @else
                            <h1 class="text-center mt-5">Sin datos para enviar...</h1>
                            @endif
                        </div>
                        <div class="tab-pane fade show {{$tab == 'verComanda' ? 'active' : ''}}" id="home2" role="tabpanel" aria-labelledby="home-tab2">
                            @if($infoComandaEnEspera) 
                            <div class="table-responsive scroll">
                                <table class="table table-hover table-checkable table-sm mb-4">
                                    <thead>
                                        <tr>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">DESCRIPCIÓN</th>
                                            <th class="text-center">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($infoComandaEnEspera as $r)
                                        <tr class="">
                                            <td class="text-center">{{number_format($r->cantidad,0)}}</td>
                                            <td class="text-left">{{$r->descripcion}}</td>
                                            <td class="text-center">
                                                <ul class="table-controls">
                                                    <li>
                                                        <a href="javascript:void(0);"          		
                                                        onclick="Confirm('{{$r->id}}','{{$r->producto_id}}','{{$r->subproducto_id}}','{{$r->cantidad}}', 1)"
                                                        data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                   
                            </div> 
                            @endif
                        </div>
                    </div>
                @endif
            </div>			
		</div>
	</div>
    <!-- AREA DE BOTONES -->
    <div id="botones" class="col-md-12 col-lg-6 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one widget-h">
                <div class="row btnRubro mt-1">
                    <div class="col-4 pl-0 pr-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-8">
                    @foreach($rubros as $r)                    
                        <button wire:click.prevent="buscarCategoria({{$r->id}})" type="button" class="btn btn-outline-success mb-1">{{$r->descripcion}}</button>
                    @endforeach
                    <button id="btnVolver" type="button" onclick= "verBotones()" class="btn btn-dark ml-1" enabled>
                        Volver                                         
                    </button>  
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12 col-lg-4 ">
                        @if($categorias->count() > 8)
                            <div class="widget-one scrollb"> 
                                <div class="scrollContent"> 
                                    @foreach($categorias as $c)                    
                                        <button style="width: 100%;"  wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-outline-dark mb-1">{{$c->descripcion}}</button>
                                    @endforeach
                                </div>
                            </div>
                        @else                   
                            @foreach($categorias as $c)                    
                                <button style="width: 100%;"  wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-outline-dark mb-1">{{$c->descripcion}}</button>
                            @endforeach                       
                        @endif
                    </div>
                    <div class="col-sm-12 col-lg-8">
                        <div class="widget-one scrollb"> 
                            <div class="scrollContent"> 
                                @if($articulos != null)
                                    @if($mostrar_sp == 0)
                                        @foreach($articulos as $a)                    
                                            <button style="width: 30%;height: 75px;font-size: 13px;" wire:click="StoreOrUpdateButton({{$a->id}},1,'','')" type="button" class="btn btn-outline-primary mb-1">{{$a->descripcion}}</button>
                                        @endforeach 
                                    @else
                                        @foreach($tiene_sp as $sp)                    
                                            <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdateButton({{$sp->id}},1,'','')" type="button" class="btn btn-outline-success mb-1">{{$sp->descripcion}}</button>
                                        @endforeach 
                                    @endif
                                @endif                   
                            </div>
                        </div>
                    </div>
                </div>              
                <input type="hidden" id="caja_abierta" wire:model="caja_abierta"> 
                <input type="hidden" id="forzar_arqueo" wire:model="forzar_arqueo">
                <input type="hidden" id="ultima_factura" wire:model="ultima_factura">
                <input type="hidden" id="permisos" wire:model="permisos">
                <input type="hidden" id="inicio_factura" value="{{$inicio_factura}}">  
                <input type="hidden" id="tiene_comentario" value="{{$comentario_comanda}}">  
                <input type="hidden" id="idFact" value="{{$factura_id}}">  
                <input type="hidden" id="delivery" value="{{$delivery}}">  
                <input type="hidden" id="total" value="{{$total}}">
                
                <input type="hidden" id="modDelivery" wire:model="modDelivery">  
                <input type="hidden" id="lista" wire:model="lista">  
                <input type="hidden" id="importeTotal" value="{{$total}}">  
                <input type="hidden" id="comercioTipo" wire:model="comercioTipo"> 
            </div> 
        </div> 
    </div>
    <input type="hidden" id="texto_base" value="{{$texto_base}}">
    <input type="hidden" id="tiene_salsa" value="{{$salsa}}">  
    <input type="hidden" id="tiene_guarnicion" value="{{$guarnicion}}">  
    <input type="hidden" id="texto_salsa">  
    <input type="hidden" id="texto_guarnicion"> 
    <input type="hidden" id="stock" value="{{$stock}}">  
</div>
 
    @include('livewire.facturas.modal-bar')  
    @include('livewire.facturas.modalSalsas')    
    @else    
    @include('livewire.facturas.formaDePago')  
    @include('livewire.facturas.modalNroCompPago') 
    @include('livewire.facturas.modalCheques')  
    @include('livewire.facturas.modalBancos')  
    @include('livewire.facturas.modalClientes')  
	@endif        

<style type="text/css" scoped>
     .widget-h{
        position: relative;
        height:375px;
        overflow: hidden;
    }
    .scroll{
        position: relative;
        max-height: 230px;
        margin-top: .5rem;
        overflow: auto;
    }
    /* .scrollb {
        width: 100%;
        height:350px;
        overflow:hidden;
    }
    .scrollContent{
        width: 106%;
        height:330px;
        overflow: auto;
    }
    */
    thead tr th {   
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff;
    } 

    @media screen and (max-width: 640px) {
        #botones {
            display:none;
        }
        #importe {
            display:none;
        }
        .btnRubro {
            width: 100%;
            margin: auto;
        }
        #btnCat {
            width: 31%;
            height: 50px;
            background: blue;
            font-size: 12px;
            color: white;
        }
        #mozo{
            display:none;
        }
        #btnGroup{
            display:none;
        }
    }

    @media screen and (min-width: 640px) and (max-width: 1280px) {
        #btnCat {
            background: red;
        }
    }

    @media screen and (min-width: 1280px) {
        #factura {
            display:block !important;
        }
        #botones {
            display:block !important;
        }
        #btnAgregar {
            display:none !important;
        }
        #btnVolver {
            display:none !important;
        }
        .btnRubro {
            width: 100%;
            margin: auto;
        }
        #btnCat {
            width: 100%;
            background: green;
            font-size: 12px;
            color: white;
        }
        #btnPro {
            width: 30%;
            height: 50px;
            font-size: 12px;
          
        }
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">
    function grabarImpresion()
    {
        window.livewire.emit('grabarImpresion');
    }
    function Salir()
    {
        window.location.href="{{ url('reservas-estado-mesas') }}";
    }
 	function Confirm(id, idProducto, idSubproducto, cantidad, comanda)
    {
        let me = this
        swal({
            title: 'CONFIRMAR',
            text: '¿DESEAS ELIMINAR EL REGISTRO?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('deleteRow', id, idProducto, idSubproducto, cantidad, comanda)  
                swal.close()   
            })
    }
    function cobrar_factura()
    { 
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        $('[id="importe"]').val(Number.parseFloat($('[id="importe"]').val()).toFixed(2));
        var saldo           = $('[id="saldo"]').val();
        var formaDePago     = $('[id="formaDePago"]').val();
        var nroCompPago     = $('[id="num"]').val();
        var importe         = $('[id="importe"]').val();
        var terminarFactura = 1;

        if(importe > saldo){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        }
   
        if(importe != saldo) terminarFactura = 0; 

        Swal.fire({
            icon: 'question',
            title: 'Confirmar',
            text: '¿Deseas registrar el pago de esta factura?',
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            denyButtonText: 'Cancelar',
            closeOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {                    
                    window.livewire.emit('cobrar_factura',formaDePago,nroCompPago,importe,terminarFactura);
                } else if (result.isDenied) {
                    Swal.fire('Cancelado','Tu registro está a salvo :)','info')
                    resetear();
                }
            });            
    }
 	function ConfirmItem(id, cantidad, controlar_stock)
    {
        let me = this
        swal({
            title: 'CONFIRMAR',
            text: '¿DESEAS ELIMINAR EL REGISTRO?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('eliminarItemComanda', id, cantidad, controlar_stock)   
                swal.close()   
            })
    }
    function verCom()
    {
        var x = document.getElementById("home-tab2");
              
        Swal.fire({
            title: 'Confirmar',
            text: '¿DESEAS MODIFICAR LA COMANDA?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Si`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                x.innerHTML = "Modificar Comanda";
                window.livewire.emit('modificarComanda');
            } else if (result.isDenied){
                x.innerHTML = "Ver Comanda";
                window.livewire.emit('verComanda', data)
            }
        })
    }
    function eliminarEntrega(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Eliminar la Entrega, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
    		icon: 'warning',
			input: 'text',
    		showCancelButton: true,
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false,
			inputValidator: comentario => {
				if (!comentario) return "Por favor escribe un breve comentario";
				else return undefined;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				if (result.value) {
					let comentario = result.value;
					window.livewire.emit('eliminarEntrega', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire(
					'Cancelado',
					'Tu registro está a salvo :)',
					'error'
				)
            }
		})
    }
    function AnularFactura(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Anular la Factura, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
    		icon: 'warning',
			input: 'text',
    		showCancelButton: true,
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false,
			inputValidator: comentario => {
				if (!comentario) return "Por favor escribe un breve comentario";
				else return undefined;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				if (result.value) {
					let comentario = result.value;
					Swal.fire(
						'Anulado!',
						'Tu registro se Anuló correctamente...',
						'success'
					);
					window.livewire.emit('anularFactura', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire(
					'Cancelado',
					'Tu registro está a salvo :)',
					'error'
				)
            }
		})
    }
    function dejar_pendiente()
    {
        window.livewire.emit('dejar_pendiente')
    }
    function openModal(id)
    {
        $('#facturaId').val(id)
        $('#facturaId').hide()
        $('#cliente').val('Elegir')
        $('#empleado').val('Elegir')
        $('#modal').modal({backdrop: 'static', keyboard: false})  //hace que no desaparezca al hacer click fuera del modal
	}
    function save()
    {
        if($('#lista').val() != 3){     
            if($('#cliente option:selected').val() == 'Elegir') {
                toastr.error('Elige una opción válida para el Cliente')
                return;
            }
        }else{
            if($('#consignatario option:selected').val() == 'Elegir') {
                toastr.error('Elige una opción válida para el Cliente')
                return;
            }
        }
        if($('#modDelivery').val() == "1"){	
            if($('#empleado option:selected').val() == 'Elegir') {
                toastr.error('Elige una opción válida para el Repartidor')
                return;
            }
        }
        if($('#modDelivery').val() == "1"){	
            var data = JSON.stringify({
                'factura_id'   : $('#facturaId').val(),
                'cliente_id'   : $('#cliente option:selected').val(),
                'empleado_id'  : $('#empleado option:selected').val()
            });
        }else{
            var cliente = null;
            if($('#lista').val() != 3) cliente = $('#cliente option:selected').val();
            else cliente = $('#consignatario option:selected').val();
            var data = JSON.stringify({
                'factura_id'   : $('#facturaId').val(),
                'cliente_id'   : cliente,
                'empleado_id'  : "Salon"
            });
        }
        $('#modal').modal('hide')
        window.livewire.emit('modCliRep', data)
    } 
    function openModalBancos()
    {
        $('#modalCheques').modal('hide')
        $('#banco').val('')
        $('#sucursal').val('')
        $('#modalBancos').modal('show')
	}
    function guardarBanco()
    {      
        if($('#descripcion').val() == '') {
            toastr.error('Ingresa un nombre válido para el Banco')
            return;
        }
        if($('#sucursal').val() == '') {
            toastr.error('Ingresa un nombre válido para la Sucursal')
            return;
        }
        var data = JSON.stringify({
            'banco'    : $('#descripcion').val(),
            'sucursal' : $('#sucursal').val()
        });
       
        $('#modalBancos').modal('hide');
        window.livewire.emit('agregarBanco', data);

        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldo').val()).toFixed(2));
    }
    function ocultar_sp()
    {
        window.livewire.emit('ocultar_sp')
    } 
    function mostrarInput()
    {
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
     
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4') {        
            $('[id="importeComp"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));        
			$('#modalNroComprobanteDePago').modal('show');
        }else if($('[id="formaDePago"]').val() == '5'){
            if($('[id=clienteId]').val() && $('[id=clienteId]').val() != $('[id=esConsFinal]').val()){
                $('#modalCheques').modal('show');
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'Primero debes cargar un Cliente!!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('[id="formaDePago"]').val(1);
            }
        }else if($('[id="formaDePago"]').val() == '6'){
            if($('[id=clienteId]').val() && $('[id=clienteId]').val() != $('[id=esConsFinal]').val()){
                Swal.fire({
                    icon: 'question',
                    title: 'Confirmar',
                    text: '¿Deseas enviar esta factura a Cuenta Corriente?',
                    showDenyButton: true,
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    denyButtonText: 'Cancelar',
                    closeOnConfirm: false
                    }).then((result) => {
                        if (result.isConfirmed) {                    
                            window.livewire.emit('factura_ctacte');
                        } else if (result.isDenied) {
                            Swal.fire('Cancelado','Tu registro está a salvo :)','info')
                            resetear();
                        }
                    });
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'Primero debes cargar un Cliente!!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCli').modal('show');
                //$('[id="formaDePago"]').val(1);
            }
		}else{
			guardarDatosPago();
		}
	}
	function guardarDatosPago()
    {
        $('[id="num"]').val($('[id="nroCompPago"]').val());
		$('[id="importe"]').val(Number.parseFloat($('[id="importeComp"]').val()).toFixed(2));        
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        var formaDePago = $('[id="formaDePago"]').val();
        var nroCompPago = $('[id="nroCompPago"]').val();
        var importe     = $('[id="importe"]').val();
        var saldo       = $('[id="saldo"]').val();
     

		window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago,importe,saldo);
	}
	function guardarDatosCheque()
    {
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        $('[id="importeCheque"]').val(Number.parseFloat($('[id="importeCheque"]').val()).toFixed(2));
        var saldo           = $('[id="saldo"]').val();
        var importe         = $('[id="importeCheque"]').val();
        var terminarFactura = 1;

        if(importe > saldo){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        } 
        
        if(importe != saldo) terminarFactura = 0;

        var data = JSON.stringify({
                'banco'         : $('#banco').val(),
                'numero'        : $('#numCheque').val(),
                'fechaDeEmision': $('#fechaDeEmision').val(),
                'fechaDePago'   : $('#fechaDePago').val(),
                'importe'       : $('#importeCheque').val(),
                'cuitTitular'   : $('#cuitTitular').val(),
                'terminarFactura' : terminarFactura,
            });		
		window.livewire.emit('enviarDatosCheque', data);
        resetear();
	}
    function resetear()
    {
        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldo').val()).toFixed(2));
        return;
    }
    function cerrar()
    {
        window.location.href="{{ url('reservas-estado-mesas') }}";
    }
    function agregarCliente()
    {
        window.location.href="{{ url('clientes') }}";
    }
    function saveCliente()
    {
        var clienteId = $('[id="cli"]').val();
        $('#modalCli').modal('hide');
        window.livewire.emit('guardarCliente', clienteId);
    }
    function openModalComandas()
    {
        console.log($('#tiene_salsa').val(),$('#tiene_guarnicion').val())
        if($('#tiene_salsa').val() == 1) $('#divSalsas').show();
        else $('#divSalsas').hide(); 
        if($('#tiene_guarnicion').val() == 1) $('#divGuarniciones').show();
        else $('#divGuarniciones').hide();
        $('#texto_comanda').val($('#texto_base').val());
        $('#texto_comentario').val('');
        $('#modalSalsas').modal('show');
	}
    function crear_descripcion(descripcion, concepto)
    {
        var tGuarnicion = '';
        var tSalsa      = '';
        var texto_base  = $('#texto_base').val();
        var texto_comanda;
        var tComentario = $('#texto_comentario').val();

        if(concepto == 'salsa'){
            $('#texto_salsa').val(descripcion);
            tSalsa      = $('#texto_salsa').val();
            tGuarnicion = $('#texto_guarnicion').val();
            if(tGuarnicion == ''){
                texto_comanda = texto_base +' c/'+ tSalsa; 
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';   
            }else{
                texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion; 
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';
            }   
        }else{
            $('#texto_guarnicion').val(descripcion);
            tGuarnicion = $('#texto_guarnicion').val();
            tSalsa      = $('#texto_salsa').val();
            if(tSalsa == ''){
                texto_comanda = texto_base +' c/'+ tGuarnicion; 
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';
            }else{
                texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion;
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';
            }   
        }
        $('#texto_comanda').val(texto_comanda); 
    }
    function agregarComentario()
    {
        var texto_base    = $('#texto_base').val();
        var tGuarnicion   = $('#texto_guarnicion').val();
        var tSalsa        = $('#texto_salsa').val();
        var tComentario   = $('#texto_comentario').val();
        var texto_comanda = '';
        
        if(tComentario != ''){
            if(tSalsa != '') texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion +' ('+ tComentario + ')';
            else texto_comanda = texto_base +' c/'+ tGuarnicion +' ('+ tComentario + ')';
        }else{
            if(tSalsa != '') texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion;
            else texto_comanda = texto_base +' c/'+ tGuarnicion;
        }
        if(tGuarnicion == '' && tSalsa == '') texto_comanda = texto_base +' ('+ tComentario + ')';

        $('#texto_comanda').val(texto_comanda);
    }
    function StoreOrUpdate()
    {
        var cantidad = $('#cantidad').val();
        var texto_comanda = $('#texto_comanda').val();
        $('#modalSalsas').modal('hide')
        window.livewire.emit('StoreOrUpdate', cantidad, texto_comanda);
    } 
    function verBotones()
    {
        if(document.getElementById('botones').style.display == 'block') {
            document.getElementById('botones').style.display = 'none';
            document.getElementById('factura').style.display = 'block';
        }else {
            document.getElementById('botones').style.display = 'block';
            document.getElementById('factura').style.display = 'none';
        }
        
    }
    // function grabarCantidad()
    // {
    //     var stock = Math.trunc($('[id="stock"]').val());  //'Math.trunc' devuelve la parte entera de un numero removiendo cualquier dígito decimal 
    //     var cantidad = $('[id="cantidad"]').val();
        
    //     if(stock < cantidad){
    //         var texto = 'Solo restan ';
    //         var unidades = ' unidades';
    //         if(stock == 0 || stock == null){
    //             texto = 'Restan ';
    //             stock = '0';
    //         }else if(stock == 1){
    //             texto = 'Solo resta ';
    //             unidades = ' unidad';  
    //         } 
    //         Swal.fire({
    //             position: 'center',
    //             icon: 'success',
    //             title: 'Stock no disponible',
    //             text: texto + stock + unidades,
    //             showConfirmButton: true
    //         })
    //         $('[id="cantidad"]').val(stock);
    //     }       
    // }
    function descontar_producto()
    {
        Swal.fire({
            position: 'center',
            icon: 'info',
            title: 'Este Producto debe descontarse desde la pestaña "Comanda"...',
            showConfirmButton: false,
            timer: 3000
        })
    }
    function factura_vacia()
    { 
        Swal.fire({
            position: 'center',
            icon: 'info',
            title: 'La factura se eliminará porque su importe es cero...',
            text: '¿Qué acción desea realizar?',
            showDenyButton: true,
            confirmButtonText: `Continuar agregando ventas`,
            denyButtonText: `Salir y eliminar esta factura`,
            timer: 5000,
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            } else if (result.isDenied) {
                window.livewire.emit('permitirCargaSinStock', 'no', id);
            }
        })
    }
    //ejecuta código antes de salir...
    $(window).on("beforeunload", function() {    
        total = $('#importeTotal').val();
        if($('[id="delivery"]').val() == 1){     //si es delivery me aseguro de que
            window.livewire.emit('salir');       //la factura quede como pendiente
        } else if (total == 0) {
            id = $('#idFact').val();            //si es una mesa, la anulo y queda como Disponible
            comentario = 'Factura en cero';
            window.livewire.emit('anularFactura',id, comentario);;
        }
    });
    /////código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 120000);
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    // document.getElementById("link").addEventListener("click", function(){
    //     var id = document.getElementById("idFact").value
    //     var ruta = "{{url('print/visita')}}" + "/" + id
    //     var w = window.open(ruta,"_blank","width=100,height=100")
    //     setTimeout(function(){
    //         w.close();
    //     }, 1000); /* 1 Segundo*/
    // });
    
    window.onload = function() {
        if($('#permisos').val() == 0){	
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'No tienes permisos para abrir esta factura...',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href="{{ url('reservas-estado-mesas') }}";
                    swal.close();
                }  
            })               
		}else{
            if($('#forzar_arqueo').val() == 1){		
                swal({
                    title: 'Existe un Arqueo General pendiente de cierre!',
                    text: 'Para seguir facturando primero debes finalizar todas las facturas que tengas abiertas...',
                    type: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Volver',
                    closeOnConfirm: false
                },
                function() {  
                    window.location.href="{{ url('reservas-estado-mesas') }}";
                    swal.close()
                })
            }
        }        
        if($('#caja_abierta').val() == 0){
            swal({
                title: 'Caja inhabilitada!',
                text: '',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('notify') }}";
                swal.close()   
            })
        }
        if($('#delivery').val() == 1 && $('#inicio_factura').val()){
            openModal(2);
        }
        Livewire.on('facturaCobrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura Cobrada!!',
                showConfirmButton: false,
                timer: 2500
            })
            if($('#ultima_factura').val() == 1){
                window.location.href = "{{ url('notify') }}";
            }else window.location.href = "{{ url('reservas-estado-mesas') }}";
		})          
        Livewire.on('facturaCtaCte',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura enviada a Cuenta Corriente!!',
                showConfirmButton: false,
                timer: 1500
            })
            if($('#ultima_factura').val() == 1){
                window.location.href="{{ url('notify') }}";
            }else{
                window.location.href="{{ url('reservas-estado-mesas') }}";
            }
		})
        Livewire.on('primeroEnviarACtaCte',()=>{
            Swal.fire('Cancelado','Primero se debe enviar la factura a Cuenta Corriente y luego se podrá hacer un pago a cuenta de la misma...','info');
            resetear();
		})      
        Livewire.on('importeMayorQueSaldo',()=>{
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
		})
        Livewire.on('modal_comanda',()=>{
            openModalComandas();
		})
        Livewire.on('comandaEnviada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Comanda enviada exitosamente!!',
                showConfirmButton: false,
                timer: 1500
            })
		})
        Livewire.on('comandaVacia',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'No existen Comandas para enviar...',
                showConfirmButton: false,
                timer: 1500
            })
		})
        Livewire.on('comandaEnEspera',()=>{
            Swal.fire({
            title: 'Existe una Comanda esperando ser elaborada para la misma mesa...',
            text: '¿Qué acción desea realizar?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Unir las 2 comandas`,
            denyButtonText: `Dejar 2 comandas separadas`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('unirComandas', 'si');
                } else if (result.isDenied) {
                    window.livewire.emit('unirComandas', 'no');
                }
            })
		})
        Livewire.on('entregaAnulada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro anulado exitosamente...',
                showConfirmButton: false,
                timer: 1500
            })
            resetear();
		})
        Livewire.on('registroAgregado',(accion)=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro ' + accion + ' exitosamente...',
                showConfirmButton: false,
                timer: 1000
            })            
		})
        Livewire.on('stock_no_disponible_sin_opcion',(stock, producto)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock === null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            }
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                html: texto + stock + unidades + ' de ' + producto + '.<br><br>' +
                'Para continuar con su carga deberá modificar su STOCK dirigiéndose a la pestaña <br> ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_no_disponible_con_opcion',(stock, producto, id)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock == null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            }else if(stock < 1){
                texto = 'Tienes stock negativo';
                stock = '';
                unidades = '';  
            } 
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                text: texto + stock + unidades + ' de ' + producto,
                showDenyButton: true,
                confirmButtonText: `Permitir cargar sin stock`,
                denyButtonText: `Anular carga`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('permitirCargaSinStock', 'si', id);
                } else if (result.isDenied) {
                    window.livewire.emit('permitirCargaSinStock', 'no', id);
                }
            })
        })
        Livewire.on('stock_receta_no_disponible_sin_opcion',(stock, id)=>{
            texto = '';
            for (var clave in stock) {
                texto = texto + 'Restan ' + stock[clave].stock + stock[clave].unidadDeMedida + ' de ' + stock[clave].descripcion + '<br>'
            };
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                html: texto +
                'Para continuar con su carga deberá modificar su STOCK dirigiéndose a la pestaña <br> ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_receta_no_disponible_con_opcion',(stock, id)=>{
            texto = '';
            for (var clave in stock) {
                texto = texto + 'Restan ' + stock[clave].stock + stock[clave].unidadDeMedida + ' de ' + stock[clave].descripcion + '<br>'
            };
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock de Materia Prima no disponible',
                html: texto,
                showDenyButton: true,
                confirmButtonText: `Permitir cargar sin stock`,
                denyButtonText: `Anular carga`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('permitirCargaSinStock', 'si', id);
                } else if (result.isDenied) {
                    window.livewire.emit('permitirCargaSinStock', 'no', id);
                }
            })
        })
        Livewire.on('receta_sin_detalle',(producto)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debe agregar un detalle a la receta de '+ producto + ' para poder descontar su stock.',
                text: 'O simplemente indicar que no tiene receta, o que no se controla stock para este producto desde la pestaña ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
		})
        Livewire.on('receta_sin_principal',(producto)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debe designar algún componente de la receta de '+ producto + ' como "principal" para poder descontar su stock.',
                text: 'O simplemente indicar que este producto no tiene receta, o que no se controla stock para el mismo desde la pestaña ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
		})        
        Livewire.on('bancoCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Banco fue creado!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('chequeCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Cheque se registró correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('cobroRegistrado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El cobro a cuenta fue registrado!!',
                showConfirmButton: false,
                timer: 2000
            });          
            resetear();
        })
        Livewire.on('cliente_agregado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Cliente agregado exitosamente!!',
                showConfirmButton: false,
                timer: 1500
            })
		})
        Livewire.on('mensaje',(data)=>{
            Swal.fire('Stock', data,'info');
            resetear();
		})
    } 
</script>