<div class="row ">
    <div class="col-12">
		<button type="button" id="btnCancelar" wire:click="doAction(1)" onclick="setfocus('nombre')"  class="btn btn-primary mr-1">
			<i class="mbri-left"></i> Cancelar
		</button>
        <button type="button" id="btnGuardar"
            wire:click="StoreOrUpdate()" onclick="setfocus('nombre')"   
            class="btn btn-success">
            <i class="mbri-success"></i> <span style="text-decoration: underline;">G</span>uardar
        </button>       
	</div>
</div>