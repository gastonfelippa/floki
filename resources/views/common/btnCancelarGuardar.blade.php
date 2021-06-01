<div class="row ">
    <div class="col-12">
		<button type="button" wire:click="doAction(1)" onclick="setfocus('nombre')"  class="btn btn-dark mr-1">
			<i class="mbri-left"></i> Cancelar
		</button>
        <button type="button"
            wire:click="StoreOrUpdate()" onclick="setfocus('nombre')"   
            class="btn btn-primary">
            <i class="mbri-success"></i> Guardar
        </button>       
	</div>
</div>