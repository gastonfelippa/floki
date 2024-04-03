<div class="modal fade" id="modal_productoHistorial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
                <h5 class="modal-title">Historial de Movimientos de Stock de: <br>
                 <b style="color: coral">{{$productoStock}}</b>
        </div>       
        <div class="modal-body">
            <div class="widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="col-6">Existencia Inicial</div>
                        @if (intval($e_i) == floatval($e_i)) {{--  si no hay decimales --}}
                            <div class="col-4 text-right">{{number_format($e_i,0)}} {{$unidadDeMedida}}</div>
                        @else
                            @if ($unidadDeMedida == 'Un')
                                <div class="col-4 text-right">{{number_format($e_i,1,',','.')}} {{$unidadDeMedida}}</div>
                            @else
                                <div class="col-4 text-right">{{number_format($e_i,3,',','.')}} {{$unidadDeMedida}}</div>
                            @endif
                        @endif
                        <div class="col-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-6">Compras</div>                        
                        @if (intval($compras) == floatval($compras)) {{--  si no hay decimales --}}
                            <div class="col-4 text-right">{{number_format($compras,0)}} {{$unidadDeMedida}}</div>
                        @else
                            @if ($unidadDeMedida == 'Un')
                                <div class="col-4 text-right">{{number_format($compras,1,',','.')}} {{$unidadDeMedida}}</div>
                            @else
                                <div class="col-4 text-right">{{number_format($compras,3,',','.')}} {{$unidadDeMedida}}</div>
                            @endif
                        @endif
                        <div class="col-2">
                            <a href="javascript:void(0);" 
                                data-toggle="tooltip" data-placement="top" title="Ver Compras"
                                wire:click="verHistorialStock(2)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">Ventas</div>
                        @if (intval($ventasTotal) == floatval($ventasTotal)) {{--  si no hay decimales --}}
                            <div class="col-4 text-right">{{number_format($ventasTotal,0)}} {{$unidadDeMedida}}</div>
                        @else
                            @if ($unidadDeMedida == 'Un')
                                <div class="col-4 text-right">{{number_format($ventasTotal,1,',','.')}} {{$unidadDeMedida}}</div>
                            @else
                                <div class="col-4 text-right">{{number_format($ventasTotal,3,',','.')}} {{$unidadDeMedida}}</div>
                            @endif
                        @endif
                        <div class="col-2">
                            <a href="javascript:void(0);"  
                                data-toggle="tooltip" data-placement="top" title="Ver Ventas"
                                wire:click="verHistorialStock(3)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
                            </a>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-6">Ventas Sin Stock</div>
                        <div class="col-4 text-right">{{$ventasSinStock}}</div>
                        <div class="col-2">
                            <a href="javascript:void(0);"  
                                data-toggle="tooltip" data-placement="top" title="Ver Ventas Sin Stock"
                                wire:click="verHistorialStock(8)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
                            </a>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-6">Modific. Manuales Directas</div>
                        @if (intval($modificacion_manual_directa) == floatval($modificacion_manual_directa)) {{--  si no hay decimales --}}
                            <div class="col-4 text-right">{{number_format($modificacion_manual_directa,0)}} {{$unidadDeMedida}}</div>
                        @else
                            @if ($unidadDeMedida == 'Un')
                                <div class="col-4 text-right">{{number_format($modificacion_manual_directa,1,',','.')}} {{$unidadDeMedida}}</div>
                            @else
                                <div class="col-4 text-right">{{number_format($modificacion_manual_directa,3,',','.')}} {{$unidadDeMedida}}</div>
                            @endif
                        @endif
                        <div class="col-2">
                            <a href="javascript:void(0);" 
                                data-toggle="tooltip" data-placement="top" title="Ver Modificaciones Manuales Directas"
                                wire:click="verHistorialStock(4)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">Modific. Manuales Indirectas</div>
                        @if (intval($modificacion_manual_indirecta) == floatval($modificacion_manual_indirecta)) {{--  si no hay decimales --}}
                            <div class="col-4 text-right">{{number_format($modificacion_manual_indirecta,0)}} {{$unidadDeMedida}}</div>
                        @else
                            @if ($unidadDeMedida == 'Un')
                                <div class="col-4 text-right">{{number_format($modificacion_manual_indirecta,1,',','.')}} {{$unidadDeMedida}}</div>
                            @else
                                <div class="col-4 text-right">{{number_format($modificacion_manual_indirecta,3,',','.')}} {{$unidadDeMedida}}</div>
                            @endif
                        @endif
                        <div class="col-2">
                            <a href="javascript:void(0);" 
                                data-toggle="tooltip" data-placement="top" title="Ver Modificaciones Manuales Indirectas"
                                wire:click="verHistorialStock(5)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="font-weight: bold">
                        <div class="col-6">TOTAL</div>
                        @if (intval($cantidad) == floatval($cantidad)) {{--  si no hay decimales --}}
                            <div class="col-4 text-right">{{number_format($cantidad,0)}} {{$unidadDeMedida}}</div>
                        @else
                            @if ($unidadDeMedida == 'Un')
                                <div class="col-4 text-right">{{number_format($cantidad,1,',','.')}} {{$unidadDeMedida}}</div>
                            @else
                                <div class="col-4 text-right">{{number_format($cantidad,3,',','.')}} {{$unidadDeMedida}}</div>
                            @endif
                        @endif
                        <div class="col-2"></div>
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