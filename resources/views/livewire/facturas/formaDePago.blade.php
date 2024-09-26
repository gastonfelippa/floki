<div class="col-sm-12 col-md-6 layout-spacing">
	<div class="widget-content-area">
        <div class="modal-header" style="background-color:#808080;border-radius:1em;">
            <div class="row col-12">
                <div class="col-4">
                    <h3><b>Cobrar Factura</b></h3>
                </div>
                <div class="col-8 text-right mt-1">
                    @if($entrega == 0)
                        <h1 class="text-center"><b>Total $ {{number_format($total,2,',','.')}}</b></h1>
                    @else
                        <h6>Total $ {{number_format($total,2,',','.')}}</h6>
                        <h6>Entrega $ - {{number_format($entrega,2,',','.')}}</h6>
                        <h6><b>Saldo $ {{number_format($saldo,2,',','.')}}</b></h6>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-body">
            @if($total != $saldo)
            <div class="row">
                <div class="table-responsive scrollPagos">
                    <table class="table table-hover table-checkable table-sm mb-4">
                        <thead>
                            <tr>
                                <th class="text-left">MEDIO DE PAGO</th>
                                <th class="text-center">N° COMPROBANTE</th>
                                <th class="text-right">IMPORTE</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($infoMediosDePago as $r)
                            <tr class="">
                                <td class="text-left">{{$r->medio_pago}}</td>
                                <td class="text-center">{{$r->num_comp_pago}}</td>
                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <!-- <li>
                                            <a href="javascript:void(0);" wire:click="editEntrega({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li> -->
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="eliminarEntrega({{$r->id}})"
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
            <hr style="height: 0.1em;background-color:#808080;"/>
            @endif
            <div class="row">   
                <div class="form-group col-4">
                    <label style="font-size:12px;color:black;"><b>Medio de Pago</b></label>
                    <select id="formaDePago" onchange="mostrarInput()" class="form-control form-control-sm">
                        <option value="1">Efectivo</option>
                        <option value="2">Tarjeta Débito</option>
                        <option value="3">Tarjeta Crédito</option>
                        <option value="4">Transferencia</option>
                        <option value="5">Cheque</option>
                        <option value="6">Cuenta Corriente</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label style="font-size:12px;color:black;"><b>N° Comp. de Pago</b></label>
                    <input id="num" class="form-control form-control-sm text-center" disabled>
                </div>
                <div class="form-group col-4">
                    <label style="font-size:12px;color:black;"><b>Importe</b></label>
                    <input id="importe" class="form-control form-control-sm text-center"
                    {{ $modComandas == 1 ? 'enabled' : 'disabled' }}>                  
                </div>
            </div>
        </div> 
        <div class="modal-footer">
            <div class="row ">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">Cancelar</button>
                    <button type="button" onclick="cobrar_factura()" class="btn btn-primary">Cobrar</button>       
                </div>
            </div>  
        </div>  
        <input type="hidden" id="clienteId" value="{{$clienteId}}"> 
        <input type="hidden" id="esConsFinal" value="{{$esConsFinal}}">  
        <input type="hidden" id="saldo" value="{{$saldo}}">  
        <input type="hidden" id="modComandas" wire:model="modComandas">   
    </div>
</div>

<style type="text/css" scoped>
    h1{
        background-color:rgba(255, 145, 71, 0.829);
        color:white;
        border-radius:5px;"
    }
    h3{
        color: white;
    }

</style>
 
<script type="text/javascript"> 
    $(document).ready(function () {
        $('#importe').val(Number.parseFloat($('#saldo').val()).toFixed(2));
    });
</script>