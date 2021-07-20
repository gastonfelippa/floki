<div class="col-sm-12 col-md-6 layout-spacing">
	<div class="widget-content-area">
        <div class="widget-one">
            <h5>
                <b>@if($selected_id ==0) Nuevo Cobro  @else Editar Recibo @endif  </b>
            </h5>
            <div class="row">                 
                <div class="form-group col-sm-12 col-md-4">
                    <label >Cliente</label>
                    <input wire:model.lazy="nomCli" type="text" class="form-control" disabled>
                </div>
                <div class="form-group col-sm-12 col-md-3">
                    <label >Importe</label> 
                    @if($entrega == 0)                                               
                    <input wire:model.lazy="importeCobrado" type="text" class="form-control text-center" disabled>
                    @else
                    <input wire:model.lazy="importeCobrado" type="text" class="form-control text-center" enabled>
                    @endif
                </div>
                <div class="form-group col-sm-12 col-md-5">
                    <label >Forma de Pago</label>
                    <select id="formaDePago" onchange="mostrarInput()" class="form-control text-center">
                        <option value="1">Efectivo</option>
                        <option value="2">Tarjeta Débito</option>
                        <option value="3">Tarjeta Crédito</option>
                        <option value="4">Transferencia</option>
                        <option value="5">Cheque</option>
                    </select>
                </div>
            </div>
			<div class="row">
                <div class="form-group col-7 mt-2">
                    <textarea rows="2" class="md-textarea form-control" wire:model="comentario" placeholder="Agrega un comentario..."></textarea>
                </div>   
                <div class="form-group col-5">
                    <label >N° del Comprobante de Pago</label>
                    <input id="num" class="form-control text-center" disabled>
                </div>
            </div> 
            <div class="row ">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">Cancelar</button>
                    <button type="button" onclick="datos_pago()" class="btn btn-primary">Guardar</button>       
                </div>
            </div>  
        </div>
    </div>	
</div>