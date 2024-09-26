<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
	@if($action == 1)  
    <div class="col-sm-12 col-md-8 layout-spacing">      
    	<div class="widget-content-area">
            <div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Socios</b></h3>
    				</div> 
    			</div>
                @if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else     		
                <div class="row justify-content-between">
                    <div class="col-12 col-md-3 mb-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-1">
                        <select wire:model="tipo" class="form-control">
                            <option value="1">COMÚN</option>
                            <option value="2">ESPECIAL</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 mb-1">
                        <select wire:model="estado" class="form-control">
                            <option value="Activo">ACTIVO</option>
                            <option value="Suspendido">SUSPENDIDO</option>
                            <option value="Baja">BAJA</option>
                        </select>
                    </div>
                    @can('Clientes_create')
                    <div class="col-12 col-md-3">
                        <button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
                            <span style="text-decoration: underline;">N</span>uevo
                        </button>
                    </div>
                    @endcan
                </div>
                    <div class="table-resposive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="">NOMBRE</th>
                                    <th class="">DIRECCIÓN</th>
                                    <th class="text-center">TELÉFONO</th>
                                    @can('Clientes_index')
                                    <th class="text-center">GRUPO FAMILIAR</th>
                                    @endcan
                                    @can('Clientes_edit')
                                    <th class="text-center">ACCIONES</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>
                                    <td >{{$r->apellido}}, {{$r->nombre}}</td>
                                    <td>{{$r->calle}} {{$r->numero}} - {{$r->localidad}}</td>
                                    <td class="text-center">{{$r->telefono}}</td>
                                    @if($r->grupo_familiar == 1)
                                        @if($r->tieneGrupoFamiliar == 1)
                                            <td class="text-center">                                 
                                                <a href="javascript:void(0);"
                                                wire:click="verGrupo({{$r->id}}, 3)"  
                                                data-toggle="tooltip" data-placement="top" title="Ver grupo familiar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                            </td>
                                        @else
                                            <td class="text-center">                                 
                                                <a href="javascript:void(0);" 
                                                data-toggle="tooltip" data-placement="top" title="No tiene grupo familiar cargado">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                            </td>
                                        @endif
                                    @else
                                        <td class="text-center">
                                            <a href="javascript:void(0);"   
                                            data-toggle="tooltip" data-placement="top" title="No tiene grupo familiar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off text-danger"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                        </td>
                                    @endif
                                    <td class="text-center">                                        
                                        <ul class="table-controls">
                                        @can('Clientes_edit')
                                            <li>
                                                <a href="javascript:void(0);" 
                                                wire:click="actividades({{$r->id}})" 
                                                data-toggle="tooltip" data-placement="top" title="Otros Débitos"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16"><path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/></svg></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" 
                                                wire:click="edit({{$r->id}})" 
                                                data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                            </li>
                                        @endcan
                                        @can('Clientes_destroy')
                                            <li>
                                                <a href="javascript:void(0);"          		
                                                onclick="Confirm('{{$r->id}}')"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                            </li>
                                        @endcan
                                        </ul>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @can('Clientes_destroy')
            <input type="hidden" id="caja_abierta" value="1">
            @else
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta">
            @endcan  
    	</div> 
    </div>
    @elseif($action == 2)
        <input type="hidden" id="tieneGrupo" wire:model="modClubes">
        @can('Clientes_create')
            @include('livewire.socios.form')    
            @include('livewire.socios.modal')     
        @endcan
    @elseif($action == 3)
        @can('Clientes_create')
            @include('livewire.socios.actividades') 
            @include('livewire.socios.modalactividades') 
        @endcan
    @endif
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 270px;
    margin-top: .5rem;
    overflow: auto;
}
  .ui-datepicker-calendar {
        display: none;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">
    function Confirm(id)
    {
    	let me = this
    	swal({
    		title: 'CONFIRMAR',
    		text: '¿DESEAS ELIMINAR EL REGISTRO?',
    		type: 'warning',
    		showCancelButton: true,
    		confirmButtonColor: '#3085d6',
    		cancelButtonColor: '#d33',
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false
    	},
    	function() {
    		window.livewire.emit('deleteRow', id) 
    		swal.close()  
    	})
    }
    function guardar()
    {
        var cobrar_en = $('#cobrar_en').val()
        window.livewire.emit('guardar',cobrar_en);
    }
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
            'provincia_id' : $('#provincia option:selected').val()
        });

        $('#modalAddLocalidad').modal('hide')
        window.livewire.emit('createFromModal', data)

    }
    function openModalActividades()
    {        
        $('#actividad').val('Elegir')
        $('#modalActividades').modal('show')
	}
	function saveActividad()
    {
        if($('#actividad option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para la Actividad')
            return;
        }
        var data = JSON.stringify({
            'actividad_id'  : $('#actividad option:selected').val()
        });

        $('#modalActividades').modal('hide')
        window.livewire.emit('createActividadFromModal', data)
    }
    $(document).ready(function() {
        $('[id="fecha"]').change(function() {
            var data =  $('#fecha').val(); 
            window.livewire.emit('cambiarFecha', data);
        });
    });
    function verificarPorDni()
	{
		var dni = document.getElementById("documento");
		var ex_regular_dni; 
		ex_regular_dni = /^\d{8}(?:[-\s]\d{4})?$/;
		if (ex_regular_dni.exec(dni.value)){
			window.livewire.emit('verificarPorDni')
		}else{
			toastr.error('DNI erróneo, formato no válido. Ingrese solo números.')
			dni.focus();
			return false;
		}		
	} 
    function cobrarEn()
    {
        var numero = $('#numero').val();
        if(numero == '') numero = 'S/N';       
        if($('#calle').val() != ''){
            var cobrar =  $('#calle').val() + ' ' + numero;
            $('#cobrar_en').val(cobrar);
        }
	}
    //código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 1200000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
     window.onload = function() {
        Livewire.on('usuario_repetido',()=>{
			var dni = document.getElementById("documento");
			toastr.error('El DNI ya está registrado...', 'Verifica los datos!')
			dni.focus();
			return false;		
		})
    //     if($('#caja_abierta').val() == 0){
    //         swal({
    //             title: 'Caja inhabilitada!',
    //             text: '',
    //             type: 'warning',
    //             confirmButtonColor: '#3085d6',
    //             confirmButtonText: 'Volver',
    //             closeOnConfirm: false
    //         },
    //         function() {  
    //             window.location.href="{{ url('notify') }}";
    //             swal.close()   
    //         })
    //     }
    //     document.getElementById("search").focus();
     }
</script>