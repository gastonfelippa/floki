<div class="row layout-top-spacing">    
    <div class="col-sm-12 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
				<form >
					<h3>Crear/Editar Movimientos</h3>
					@include('common.messages')
					<div class="row">
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
							<label>Tipo</label>
							<select wire:model.lazy="tipo" class="form-control">
								<option value="Elegir">Elegir</option>
								<option value="Ingreso">Ingreso</option>
								<option value="Gasto">Gasto</option>
							</select>
						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12">
							<label>Monto</label>
							<input type="number" wire:model.lazy="monto" class="form-control text-center" placeholder="ej: 100.00">
						</div>
						<div class="form-group col-lg-12 col-sm-12 mb-8">
							<label>Ingresa la descripción</label>					
							<input type="text" class="form-control" wire:model.lazy="concepto" placeholder="...">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-5 mt-2  text-left">
							<button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
								<i class="mbri-left"></i> Regresar
							</button>
							<button type="button"
								wire:click.prevent="StoreOrUpdate() " 
								class="btn btn-primary ml-2">
								<i class="mbri-success"></i> Guardar
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>