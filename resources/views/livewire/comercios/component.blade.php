<div class="row layout-top-spacing justify-content-center">
    <div class="col-sm-12 col-md-10 layout-spacing"> 
        @include('common.alerts')
        @include('common.messages')
        @include('livewire.comercios.modal') 
        <div class="widget-content-area">
            <div class="widget-one">
                <h3 class="text-center"><b>Datos de la Empresa</b></h3>
                <div class="row mt-3">
                    <div class="col-12 col-md-4 layout-spacing">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-shop"></i></span>
                            </div>
                            <input id="nombre" type="text" class="form-control text-uppercase" placeholder="Nombre" wire:model.lazy="nombre" autofocus autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 layout-spacing">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-telephone"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder=Teléfono wire:model.lazy="telefono" autocomplete="off">
                        </div>
                    </div>   
                    <div class="col-12 col-md-4 layout-spacing">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder=Email wire:model.lazy="email" autocomplete="off">
                        </div>
                    </div> 
                </div> 
                <div class="row">                               
                    <div class="col-7 col-md-4 layout-spacing">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fullscreen-exit" viewBox="0 0 16 16"><path d="M5.5 0a.5.5 0 0 1 .5.5v4A1.5 1.5 0 0 1 4.5 6h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5zm5 0a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 10 4.5v-4a.5.5 0 0 1 .5-.5zM0 10.5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 6 11.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zm10 1a1.5 1.5 0 0 1 1.5-1.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4z"/></svg></span>
                            </div>
                            <input type="text" class="form-control text-capitalize" placeholder=Calle wire:model.lazy="calle" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-5 col-md-4 layout-spacing">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-hash"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder=Número wire:model.lazy="numero" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 layout-spacing">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/></svg></span>
                            </div>
                            <select wire:model="localidad" class="form-control">
                                <option value="Elegir">Localidad</option>
                                @foreach($localidades as $l)
                                <option value="{{ $l->id }}">
                                    {{$l->descripcion}}
                                </option>                                       
                                @endforeach 
                            </select>
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="openModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></span>
                            </div>
                        </div>			               
                    </div>                    
                </div> 
                <div class="row"> 
                    <div class="form-group col-sm-12 col-md-8">
                        <label>Logo</label>
                        <input type="file" class="form-control" id="image"
                        wire:change="$emit('fileChoosen',this)" accept="image/x-png, image/gif, image/jpeg">

                    </div>
                    @if ($logo_nuevo)
                    <div class="form-group col-sm-12 col-md-4 text-center">
                        <img class="rounded-circle" src="{{$logo_nuevo}}" height="100px">
                    </div>    
                    @endif
                    
                </div>
                <div class="row">
                    <div class="col-12" style="border-top: 1px solid grey">
                        <div class="mt-3">
                        <button type="button" onclick="salir()" class="btn btn-dark mr-1">
                            <i class="mbri-left"></i> Cancelar
                        </button>
                        <button type="button"
                            wire:click.prevent="Guardar()"   
                            class="btn btn-primary">
                            <i class="mbri-success"></i> Guardar
                        </button> 
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal()
    {        
        $('#localidad').val('')
        $('#provincia').val('Elegir')
        $('#modalAddLocalidad').modal('show')
	}
	function save()
    {
        if($('#localidad').val() == '') {
            toastr.error('El campo Localidad no puede estar vacío')
            return;
        }
        if($('#provincia option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para la Provincia')
            return;
        }
        var data = JSON.stringify({
            'localidad': $('#localidad').val(),
            'provincia_id'  : $('#provincia option:selected').val()
        });

        $('#modalAddLocalidad').modal('hide')
        window.livewire.emit('createFromModal', data)
    } 

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
