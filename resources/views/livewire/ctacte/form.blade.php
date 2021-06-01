<div class="col-sm-12 col-md-6 layout-spacing">
		<div class="widget-content-area">
            <div class="widget-one">
                <h5>
                    <b>@if($selected_id ==0) Nuevo Cobro  @else Editar Recibo @endif  </b>
                </h5>
                <div class="row">                 
                    <div class="form-group col-sm-12 col-md-5">
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
                        <!-- <input wire:model.lazy="saldoFactura" type="text" class="form-control text-center" enabled> -->
                    </div>
                    <div class="form-group col-sm-12 col-md-4">
                        <label >Forma de Pago</label>
                        <select wire:model="f_de_pago" class="form-control text-center">
                            <option value="efectivo">Efectivo</option>
                            <option value="cheque">Cheque</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                </div>
				<div class="row mb-3">
                    <div class="col-12">
                        <textarea rows="2" class="md-textarea form-control" wire:model="comentario" placeholder="Agrega un comentario..."></textarea>
                    </div>   
                </div>   
					@include('common.btnCancelarGuardar')
            </div>
        </div>	
	</div>