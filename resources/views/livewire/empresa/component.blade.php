<div class="widget-content-area mt-3">
    <div class="widget-one">
        <div class="row">
            <div class="col-12">
                @include('common.messages')
                @include('common.alerts')
            </div>
            <div class="col-12">
                <h3 class="text-center"><b>Datos de la Empresa</b></h3>
            </div>
            <div class="form-group col-sm-12">
                <label>Nombre</label>
                <input wire:model.lazy="nombre" type="text" class="form-control text-left">
            </div>
            <div class="form-group col-sm-12 col-md-2">
                <label>Teléfono</label>
                <input wire:model.lazy="telefono" maxlength="12" type="text" class="form-control text-center">
            </div>
            <div class="form-group col-sm-12 col-md-4">
                <label>Email</label>
                <input wire:model.lazy="email" maxlength="65" type="text" class="form-control text-center">
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Dirección</label>
                <input wire:model.lazy="direccion" type="text" class="form-control text-left">
            </div>
            <div class="form-group col-sm-12">
                <label>Logo</label>
                <input type="file" class="form-control" id="image"
                wire:change="$emit('fileChoosen',this)" accept="image/x-png, image/gif, image/jpeg">
            </div>
        </div>
        <div class="row ">
            <div class="col-12">
                <button type="button" onclick="salir()"  class="btn btn-dark mr-1">
                    <i class="mbri-left"></i> Cancelar
                </button>
                <button type="button"
                    wire:click.prevent="Guardar"   
                    class="btn btn-primary">
                    <i class="mbri-success"></i> Guardar
                </button>       
            </div>
        </div>
    </div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		window.livewire.on('fileChoosen', () => {
            let inputField = document.getElementById('image')
            let file = inputField.files[0]
            let nombreLogo = file.name; //capturo el nombre de la imagen
			let reader = new FileReader();
			reader.onloadend = ()=> {
				window.livewire.emit('logoUpload', reader.result, nombreLogo)
			}
			reader.readAsDataURL(file);
		});
        Livewire.on('grabado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Información de Empresa registrada!!',
                showConfirmButton: false,
                timer: 12000
            });
            window.location.href="{{ url('home') }}";
		})    
	});
    function salir()
    {
        window.location.href="{{ url('home') }}";
    }
</script>
