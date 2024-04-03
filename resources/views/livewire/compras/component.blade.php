<div class="row layout-top-spacing justify-content-center">
    @if($action == 1)
    <div class="col-md-12 col-lg-6 layout-spacing"> 		
        <div class="widget-content-area br-4">
            <div class="widget-one widget-h">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <h5>Factura: '{{$letra}}' {{str_pad($sucursal, 4, '0', STR_PAD_LEFT)}}-{{str_pad($numFact, 8, '0', STR_PAD_LEFT)}}</h5>  
                        <h6>Fecha: {{$fecha}}</h6>  
                        <h6>Proveedor: {{$nombreProveedor}}</h6>  
                    </div>
                    <div class="col-md-6 text-center">
                        <h3 class="bg-danger" style="border-radius: 5px;">Total : $ {{number_format($total,2,',','.')}}</h3> 
                        <div class="btn-group mb-2" role="group" aria-label="Basic mixed styles example">            
                            @if($total > 0)                             
                                <button type="button" onclick="openModal({{$factura_id}})"
                                    class="btn btn-dark" enabled>
                                    Modificar Encabezado                                         
                                </button>
                                <button type="button" onclick="Cobrar(0,1)" 
                                    class="btn btn-primary" enabled>
                                    Pagar   
                                </button>
                                <button type="button" onclick="AnularFactura({{$factura_id}})" 
                                    class="btn btn-info" enabled>
                                    Anular Factura  
                                </button>
                            @endif
                        </div>
                    </div>
                </div>     
                @if($mostrar_datos == 1)
                    <div class="row mt-2">
                        <div class="col-7">
                            <h6>Proveedor:  {{$encabezado[0]->nombre_empresa}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-7">
                            <h6>Dirección:  {{$encabezado[0]->calle}} {{$encabezado[0]->numero}} 
                                        - {{$encabezado[0]->descripcion}}</h6>   
                        </div>
                    </div>   
                @endif
                @include('common.alerts')
                <div class="table-responsive scroll">
                    <table class="table table-hover table-checkable table-sm mb-4">
                        <thead>
                            <tr>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-center">DESCRIPCIÓN</th>
                                <th class="text-center">PRECIO UNITARIO</th>
                                <th class="text-center">IMPORTE</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr>
                                <td class="text-center">{{number_format($r->cantidad,3,',','.')}}</td>
                                <td class="text-left">{{$r->producto}}</td>
                                <td class="text-right">{{$r->precio}}</td>
                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" wire:click="edit({{$r->id}},{{$r->es_producto}})" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="Confirm('{{$r->id}}','{{$r->es_producto}}')"
                                            data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>                   
                </div>
            </div>			
        </div>
    </div>
    <div class="col-md-12 col-lg-6 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one">
                @if($selected_id > 0)
                    <h5><b>Editar Item</b></h5>
                @else
                <div class="row justify-content-between">
                    <div class="col-4">
                        <h5><b>Agregar Item</b></h5>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-danger btn-sm" onclick="agregarProducto()">Agregar Producto</button>
                    </div>
                </div>
                @endif 
                @include('common.messages')
                <div class="row mt-1">
                    <div class="form-group col-sm-12 col-md-2">
                        <label>Cantidad</label>
                        <input id="cantidad" wire:model.lazy="cantidad" onclick.keydown.enter="setfocus('barcode')" type="text" 
                            class="form-control form-control-sm text-center">
                    </div> 
                    <div class="form-group col-sm-12 col-md-2">
                        <label >Código</label>
                        <input id="barcode" wire:model.lazy="barcode"  type="text" 
                            onblur="buscarPorCodigo()" class="form-control form-control-sm">
                    </div>
                    <div class="form-group col-sm-12 col-md-5">
                        @if($es_producto == 1)
                            <label>Producto</label>
                            @if($selected_id > 0)
                                <select wire:model="producto" onclick="ocultar_sp()" class="form-control form-control-sm" disabled>
                                @foreach($productos as $t)    
                                    <option value="{{ $t->id }}">{{$t->descripcion}}</option>
                                @endforeach 
                                </select>
                            @else
                                <select wire:model="producto" onclick="ocultar_sp()" class="form-control form-control-sm">
                                    <option value="Elegir" >Elegir</option>
                                    @foreach($productos as $t)
                                        <option value="{{ $t->id }}">{{$t->descripcion}}</option>                                         
                                    @endforeach   
                                </select>			               
                            @endif 
                            <input type="hidden" id="costo_actual" wire:model="costo_actual">	
                        @else                            
                            <label>Subproducto</label>
                            @if($selected_id > 0)
                                <select wire:model="subproducto" class="form-control form-control-sm" disabled>
                                    @foreach($subproductos as $t)
                                        <option value="{{ $t->id }}">{{$t->descripcion}}</option> 
                                    @endforeach   
                                </select>  
                            @else
                                <select wire:model="subproducto" class="form-control form-control-sm">
                                    <option value="Elegir" >Elegir</option>
                                        @foreach($subproductos as $t)
                                        <option value="{{ $t->id }}">
                                            {{$t->descripcion}}                         
                                        </option> 
                                        @endforeach   
                                </select> 
                            @endif 
                        @endif 
                    </div>            
                    <div class="form-group col-sm-12 col-md-3">
                        <label>P/Unitario</label>
                        <input id="precio" wire:model.lazy="precio" onblur="precioBajo()" type="text" class="form-control form-control-sm text-right">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 col-sm-12 mb-2">
                        <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">Cancelar</button>
                        <button type="button" wire:click="calcularPrecioVenta()" class="btn btn-primary">
                        Guardar</button>    
                    </div>
                    <div class="col-md-7 col-sm-12">
                        @if($cambiar_precios == 'no')
                            <p><b>(Al Guardar NO SE MODIFICARÁ NINGÚN PRECIO... ni de Costo, ni Sugeridos ni de Listas)</b>
                        @elseif($cambiar_precios == 'solo_costos')
                            <p><b>(Al Guardar SOLO SE ACTUALIZARÁN el Precio de Costo y los Precios Sugeridos)</b>
                        @else
                            <p><b>(Al Guardar SE ACTUALIZARÁN TODOS LOS PRECIOS... el de Costo, los Sugeridos y los de Lista)</b>
                        @endif
                        <span class="badge bg-danger" onClick="opcionCambiarPrecios()">Cambiar Opción de Guardado</span></p>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="action" value="{{$action}}"> 
        <input type="hidden" id="inicio_factura" value="{{$inicio_factura}}"> 
        <input type="hidden" id="_letra" value="{{$letra}}"> 
        <input type="hidden" id="_sucursal" value="{{$sucursal}}"> 
        <input type="hidden" id="_numFact" value="{{$numFact}}"> 
        <input type="hidden" id="_fecha" value="{{$fecha}}"> 
        <input type="hidden" id="_proveedor" value="{{$proveedor}}"> 
    </div>
        @include('livewire.compras.modal')
    @elseif ($action == 2)    
        @include('livewire.compras.formaDePago')  
        @include('livewire.compras.modalNroCompPago') 
    @else
        @include('livewire.compras.modal') 
    @endif    
</div>

<style type="text/css" scoped>
    thead tr th {     /* fija la cabecera de la tabla */
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff;
    }
    .widget-h{
        position: relative;
        height:375px;
        overflow: hidden;
    }
    .scroll{
        position: relative;
        max-height: 230px;
        margin-top: .5rem;
        overflow: auto;
    }
    .scrollb {
        width: 100%;
        max-height:240px;
        overflow:hidden;
    }
    .scrollContent{
        width: 108%;
        height:240px;
        overflow-y:auto;
        overflow-x:hidden;
    }
    .scrollc {
        width: 100%;
        height:200px;
        overflow:hidden;
    }
    .scrollContentC{
        width: 108%;
        height:200px;
        overflow-y:auto;
        overflow-x:hidden;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
 	function Confirm(id, es_producto)
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
                window.livewire.emit('deleteRow', id, es_producto)    
                swal.close()   
            })
    }
    function Cobrar(delivery, idCli)
    {
        Swal.fire({
            title: 'Elige una opción...',
            showDenyButton: true,
            showCancelButton: true,
            cancelButtonText: `Cancelar`,
            confirmButtonText: `Contado`,
            denyButtonText: `Cuenta Corriente`,
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('elegirFormaDePago');
            } else if (result.isDenied) {
                if(delivery == 0) {
                    modalCtacte()
                }else {
                    var data = JSON.stringify({
                        'cliente_id' : idCli
                    });
                    window.livewire.emit('factura_ctacte', data)
                }
            }
        })
    }
    function precioBajo()
    {
        const costo_actual = new Number($('[id="costo_actual"]').val());
        const precio = new Number($('[id="precio"]').val());
        if(precio > 0){
            if(costo_actual > precio){
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Atención!!  Verificar la Opción de Guardado...',
                    text: 'El Precio del Producto que estás cargando ES MENOR que el Precio de Costo actual que tiene dicho Producto.',
                    showConfirmButton: true
                })
            } 
        }       
    }
    function opcionCambiarPrecios() 
    {          
        Swal.fire({
            icon: 'question',
            title: 'Elige una opción de Guardado...',
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Deseo que solo se modifiquen los Precios de Costo y de Venta Sugeridos',
            denyButtonText: 'Deseo modificar tanto los Precios de Costo como así también los de Venta Sugeridos y los de Lista',
            showCancelButton: true,
            cancelButtonText: 'NO DESEO MODIFICAR NINGÚN PRECIO... ni de Costo, ni Sugeridos ni de Lista'
        }).then((result) => {
            if (result.isConfirmed) { 
                window.livewire.emit('opcionCambiarPrecios', 'solo_costos');
            } else if (result.isDenied) {
                window.livewire.emit('opcionCambiarPrecios', 'cambiar_todo');
            } else if (result.dismiss == 'cancel') {
                window.livewire.emit('opcionCambiarPrecios', 'no');
            }
        });        
    }
    function buscarPorCodigo()
    {
        window.livewire.emit('buscarPorCodigo')
    }
    function factura_contado()
    { 
        if($('[id="formaDePago"]').val() != 1 && $('[id="nroCompPago"]').val() == ''){ 
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Faltan datos, se cobrará como efectivo!!',
                showConfirmButton: false,
                timer: 1500
            })
            $('[id="formaDePago"]').val(1)
        }else{
            window.livewire.emit('factura_contado')
        }    
    }
    function AnularFactura(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Anular la Factura, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
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
					Swal.fire(
						'Anulado!',
						'Tu registro se Anuló correctamente...',
						'success'
					);
					window.livewire.emit('anularFactura', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire(
					'Cancelado',
					'Tu registro está a salvo :)',
					'error'
				)
            }
		})
    }
    function dejar_pendiente()
    {
        window.livewire.emit('dejar_pendiente')
    }
    function modalCtacte()
    {
        $('#cliente2').val('Elegir')
        $('#modalCtacte').modal('show')
	}
	function saveCtacte()
    {     
        if($('#cliente2 option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Cliente')
            return;
        }
        var data = JSON.stringify({
            'cliente_id'   : $('#cliente2 option:selected').val()
        });
        $('#modalCtacte').modal('hide')
        window.livewire.emit('factura_ctacte', data)
    } 
    function openModal(id)
    {
        $('#facturaId').val(id)
        $('#facturaId').hide()
        if($('#inicio_factura').val()){
            $('#letra').val('B')
            $('#sucursal').val('')
            $('#numFact').val('')
            $('#proveedor').val('Elegir') 
        }else{
            $('#letra').val($('#_letra').val())
            $('#sucursal').val($('#_sucursal').val())
            $('#numFact').val($('#_numFact').val())
            $('#fecha').val($('#_fecha').val())
            $('#proveedor').val($('#_proveedor').val())
        }
        
        $('#modal').modal('show')
	}
	function save()
    {
        var facturaId    = null
        var letra        = null
        var sucursal     = null
        var numero       = null
        var fecha        = null
        var proveedor_id = null

        if($('#proveedor option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Proveedor')
            return;
        }
        facturaId    = $('#facturaId').val()
        letra        = $('#letra option:selected').val()
        sucursal     = $('#sucursal').val()
        numero       = $('#numFact').val()
        fecha        = $('#fecha').val()
        proveedor_id = $('#proveedor option:selected').val()

        var data = JSON.stringify({
            'factura_id'   : facturaId,
            'letra'        : letra,
            'sucursal'     : sucursal,
            'numero'       : numero,
            'fecha'        : fecha,
            'proveedor_id' : proveedor_id
        });

        $('#modal').modal('hide')
        window.livewire.emit('CrearModificarEncabezado', data)
    }
    function ocultar_sp()
    {
        window.livewire.emit('ocultar_sp')
    } 
    function mostrarInput()
    {		
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4' || $('[id="formaDePago"]').val() == '5') {
			$('#modalNroComprobanteDePago').modal('show');
		}else{
			guardarDatosPago();
		}
	}
	function guardarDatosPago()
    {
		$('[id="num"]').val($('[id="nroCompPago"]').val())
        if($('[id="num"]').val() != ''){
            var formaDePago = $('[id="formaDePago"]').val();
		    var nroCompPago = $('[id="nroCompPago"]').val();
        }else{
            $('[id="formaDePago"]').val(1)           
        }		
		window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago);
	}
    function agregarProducto()
    {
        window.location.href="{{ url('productos') }}";
    }
    /////código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 120000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    window.onload = function() {

        if($('#inicio_factura').val()) openModal();

        Livewire.on('facturaCobrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura Pagada!!',
                showConfirmButton: false,
                timer: 1500
            })
		})          
        Livewire.on('facturaCtaCte',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura enviada a Cuenta Corriente!!',
                showConfirmButton: false,
                timer: 2500
            })
		})
        Livewire.on('item_existente',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'El producto que deseas cargar ya existe en el detalle!!',
                text: 'Para agregarlo deberás modificar el mismo...',
                showConfirmButton: true
            })
		}) 
        Livewire.on('cambiarPrecioDetalle',(cantidad, accion)=>{
            var existe = 'Existen ';
            var factura = ' facturas abiertas y/o pendientes ';
            if(cantidad == 1){
                existe = 'Existe ';
                factura = ' factura abierta y/o pendiente ';
            } 
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: '¡¡¡ATENCIÓN!!!',
                text: existe + cantidad + factura + 'en donde tenés cargado este producto con el precio anterior..., ¿Qué acción deseas realizar?',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Actualizar los precios de todas las facturas...',
                showCancelButton: true,
                cancelButtonText: 'Continuar sin modificar las facturas...'
            }).then((result) => {
                if (result.isConfirmed) { 
                    window.livewire.emit('actualizarPreciosCargados', accion);
                }
            });       
		})
        Livewire.on('peps_iniciado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Esta Factura no se puede Eliminar!!',
                text: 'Ya se vendieron productos que figuran en ella y su eliminación traería inconsistencia de datos',
                showConfirmButton: true
            })
		})
        Livewire.on('errorAlGrabarStock',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Esta Factura no se puede Pagar!!',
                html: 'Existe un error al actualizar el Stock de los productos adquiridos. <br>' +
                'Intente actualizar la página y pruebe nuevamente pagar la factura.<br>' +
                'De persistir el error, deberá anular esta factura y proceder a confeccionarla nuevamente...',
                showConfirmButton: true
            })
		})
    } 
</script>