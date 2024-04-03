<div class="row layout-top-spacing justify-content-center p-2"> 
    <div class="col-sm-12 layout-spacing">  
        <div class="widget-content-area">
            @include('common.alerts')
            @include('common.messages')
            <div class="widget-one">
                <div class="row p-2">
                    <div>
                        <h3><b>Procedimiento para la elaboración de {{$prod_receta}}</b></h3>
                    </div> 
    			</div> 
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <textarea id="procedimiento" rows="8" class="form-control" wire:model="procedimiento" placeholder="Agrega la descripción..."></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                            Cancelar
                        </button>
                        <button type="button"  class="btn btn-warning"
                            wire:click="GrabarProcedimiento">
                            Guardar                           
                        </button> 
                    </div>
                </div>
            </div>
    	</div> 
    </div> 
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 170px;
    margin-top: .3rem;
    overflow: auto;
}
</style>

<script type="text/javascript">
    function Confirm()
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Eliminar el registro, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
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
					window.livewire.emit('deleteRow', comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }
    $(document).ready(function() {
        document.getElementById("procedimiento").focus();
    });
</script>