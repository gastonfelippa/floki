<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
	@if($action == 1)  
    <div class="col-sm-12 col-md-8 layout-spacing">      
    	<div class="widget-content-area">
            <div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Clientes</b></h3>
    				</div> 
    			</div>
                @if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else 	
                    @include('common.inputBuscarBtnNuevo', ['create' => 'Clientes_create'])
                    <div class="table-resposive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="">NOMBRE</th>
                                    <th class="">DIRECCIÓN</th>
                                    <th class="text-left">TELÉFONO</th>
                                    @if($modViandas == "1")
                                        @can('Viandas_index')
                                        <th class="text-center">CLIENTE/VIANDA</th>
                                        @endcan
                                    @endif
                                    @if($modConsignaciones == "1")
                                        <th class="text-left">TIPO</th>
                                    @endif
                                    @can('Clientes_edit')
                                    <th class="text-center">ACCIONES</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>
                                    @if($r->esConsFinal == 0)
                                        <td >{{$r->apellido}}, {{$r->nombre}}</td>
                                        <td>{{$r->calle}} {{$r->numero}} - {{$r->localidad}}</td>
                                    @else
                                        <td >{{$r->apellido}} {{$r->nombre}}</td>
                                        <td>...</td>
                                    @endif
                                    <td class="text-left">{{$r->telefono}}</td>
                                    @if($modViandas == "1")
                                        @can('Viandas_index')
                                        @if($r->vianda == 1)
                                            @if($r->tieneViandasCargadas == 1)
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="verViandas({{$r->id}}, 3)"  
                                                    data-toggle="tooltip" data-placement="top" title="Ver viandas">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                                </td>
                                            @elseif($r->tieneViandasCargadas == 0)
                                                <td class="text-center">                                 
                                                    <a href="javascript:void(0);"
                                                    wire:click="verViandas({{$r->id}}, 3)"  
                                                    data-toggle="tooltip" data-placement="top" title="No posee viandas cargadas">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-warning"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                                </td>
                                            @endif
                                        @else
                                            <td class="text-center">
                                                <a href="javascript:void(0);"   
                                                data-toggle="tooltip" data-placement="top" title="No es cliente de viandas">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off text-danger"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </td>
                                        @endif
                                        @endcan
                                    @endif
                                    @if($modConsignaciones == "1")
                                        <td class="text-left">{{$r->tipo}}</td>
                                    @endif
                                    @if($r->esConsFinal == 0)
                                        <td class="text-center">
                                            @include('common.actions', ['edit' => 'Clientes_edit', 'destroy' => 'Clientes_destroy']) <!--botones editar y eliminar -->            
                                        </td>
                                    @endif
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
        <input type="hidden" id="cliVianda" wire:model="modViandas">
        <input type="hidden" id="cliConsig" wire:model="modConsignaciones">
        @can('Clientes_create')
            @include('livewire.clientes.form')    
            @include('livewire.clientes.modal')     
        @endcan
    @elseif($action == 3)    
        @include('livewire.clientes.viandas') 
    @endif
</div>

{{-- <style type="text/css" scoped>
.scroll{
    position: relative;
    height: 270px;
    margin-top: .5rem;
    overflow: auto;
}
thead tr th {     /* fija la cabecera de la tabla */
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style> --}}

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
        var vianda = false, consignatario = false;
        if(document.getElementById('cliVianda').value == 1 && document.getElementById('vianda_si').checked) vianda = true;
        if(document.getElementById('cliConsig').value == 1 && document.getElementById('consignatario_si').checked) consignatario = true;
        window.livewire.emit('guardar',vianda,consignatario);
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
            'provincia_id'  : $('#provincia option:selected').val()
        });

        $('#modalAddLocalidad').modal('hide')
        window.livewire.emit('createFromModal', data)
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
    function setfocus(id) {
        document.getElementById(id).focus();
    }
    window.onload = function() {
        if($('#caja_abierta').val() == 0){
            swal({
                title: 'Caja inhabilitada!',
                text: '',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('notify') }}";
                swal.close()   
            })
        }
        document.getElementById("search").focus();
    }
</script>