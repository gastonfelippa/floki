<div class="row layout-top-spacing">
    @if($action == 1)    
    <div class="col-sm-12 layout-spacing">      
    	<div class="widget-content-area">
            <div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Proveedores</b></h3>
    				</div> 
    			</div> 
                @if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else    		
                    @include('common.inputBuscarBtnNuevo', ['create' => 'Proveedores_create'])
                    <div class="table-resposive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="">NOMBRE/EMPRESA</th>
                                    <th class="">COND. IVA/N° CUIT</th>
                                    <th class="">DIRECCIÓN</th>
                                    <th class="text-center">TELÉFONO/EMPRESA</th>
                                    <th class="">|||</th>
                                    <th class="">NOMBRE/CONTACTO</th>
                                    <th class="text-center">TELEFONO/CONTACTO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>
                                    <td>{{$r->nombre_empresa}}</td>
                                    <td>{{$r->condiva}} / {{$r->cuit}}</td>
                                    <td>{{$r->calle}} {{$r->numero}} - {{$r->localidad}}</td>
                                    <td class="text-center">{{$r->tel_empresa}}</td>
                                    <td></td>
                                    <td>{{$r->apellido_contacto}} {{$r->nombre_contacto}}</td>
                                    <td class="text-center">{{$r->tel_contacto}}</td>
                                    <td class="text-center">
                                        @include('common.actions', ['edit' => 'Proveedores_edit', 'destroy' => 'Proveedores_destroy']) <!--botones editar y eliminar -->            
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
    	</div> 
    </div>
    @else
	@can('Proveedores_create')
	@include('livewire.proveedores.form')		
	@include('livewire.proveedores.modal')		
	@include('livewire.proveedores.modalIva')		
	@include('livewire.proveedores.modalCategoria')		
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
</style>

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
    function openModalIva()
    {        
        $('#descripcion').val('')
        $('#modalAddIva').modal('show')
	}
	function saveIva()
    {
        if($('#descripcion').val() == '') {
            toastr.error('El campo Descripción no puede estar vacío')
            return;
        }
        var data = JSON.stringify({
            'descripcion': $('#descripcion').val()
        });

        $('#modalAddIva').modal('hide')
        window.livewire.emit('createIvaFromModal', data)
    } 
    function openModalCategoria()
    {
        $('.modal-title').text('Agregar Categoría de Egresos')    
        $('#descripcion_cat').val('')
		// $('.gastoFijo').val
        $('#modalAddCategoria').modal('show')
	}
    function saveCategoria()
    {
        if($('#descripcion_cat').val() == '') {
            toastr.error('El campo Descripción no puede estar vacío')
            return;
        }
		var tipo;
		var elementos = document.getElementsByName("checks");
		for(var i=0; i<elementos.length; i++) {
			if(!elementos[i].checked) tipo = 1;
			else tipo = 2;
		}
        var data = JSON.stringify({
            'descripcion': $('#descripcion_cat').val(),
			'tipo': tipo,
        });

        $('#modalAddCategoria').modal('hide')
        window.livewire.emit('createCategoriaFromModal', data)
    }
    function guardar()
    {
		document.getElementById("nombre").focus();
        window.livewire.emit('StoreOrUpdate');
    }
    window.onload = function() {
        document.getElementById("search").focus();
    }
</script>