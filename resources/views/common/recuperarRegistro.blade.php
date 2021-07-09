<div class="row">
	<div class="col-8">
		<button type="button" style="height: 175px;"
			wire:click="RecuperarRegistro({{$id_soft_deleted}})"   
			class="btn btn-warning btn-block">
			<i class="mbri-success"></i> El registro: <b>{{$descripcion_soft_deleted}}</b> fué eliminado anteriormente pero existe en el sistema, para recuperarlo haga click <i>aquí</i>
		</button>
		</div>
		<div class="col-4">
		<button style="height: 75px;" type="button" wire:click="volver()" class="btn btn-dark mr-1 btn-block">
			Cancelar
		</button>
	</div>
</div> 