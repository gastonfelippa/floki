<div class="row layout-top-spacing justify-content-center">
    @if($action == 1)	
    <div class="col-md-12 col-lg-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one widget-h">
                @if($delivery == 1)
                <!-- <div class="row ml-1 justify-content-end">
                    <h5 class="bg-danger ml-1 p-1" style="border-radius: 5px;">Total: $ {{number_format($total,2,',','.')}}</h5> 
                </div>  -->
                @else
                <div class="row ml-1 justify-content-center">
                    <h5 class="bg-danger p-1" style="border-radius: 5px;">Mesa: {{$mesaDesc}}</h5>
                    <h5 class="bg-danger ml-1 p-1" style="border-radius: 5px;">Mozo: {{$mozoDesc}}</h5>
                    <h5 class="bg-danger ml-1 p-1" style="border-radius: 5px;">Total: $ {{number_format($total,2,',','.')}}</h5> 
                </div>  
                <div class="row">
                    <div class="col-md-3">
                        <div style="font-size:13px;"><b>Fact. N° </b> {{str_pad($numFactura, 6, '0', STR_PAD_LEFT)}} </div>
                        <div style="font-size:13px;"><b>Fecha</b> {{\Carbon\Carbon::parse(strtotime($fecha))->format('d-m-Y')}} </div>
                    </div>                    
                    <div class="col-md-9 text-right">
                        <div class="btn-group mb-2" role="group" aria-label="Basic mixed styles example">            
                        @if($total == 0)
                            <!-- <button type="button" onclick="openModal({{$factura_id}})"
                                class="btn btn-dark" enabled>
                                Delivery   
                            </button>         -->
                        @else
                            @if($delivery == 1)
                                <button type="button" onclick="openModal({{$factura_id}})"
                                    class="btn btn-dark" enabled>
                                    Mod Cli/Rep                                         
                                </button>
                                <button type="button" onclick="dejar_pendiente()"
                                    class="btn btn-warning" enabled>
                                    Dejar Pendiente
                                </button>
                            @endif 
                            <button type="button" onclick="Cobrar({{$delivery}},{{$clienteId}})" 
                                class="btn btn-primary" enabled>
                                Cobrar   
                            </button>
                            <button type="button" class="btn btn-success" enabled>
                            <!-- <a id="link">
                            Imprimir</a> -->
                                <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                Imprimir</a>
                            </button>
                            <button type="button" onclick="AnularFactura({{$factura_id}})" 
                                class="btn btn-info" enabled>
                                Anular Factura  
                            </button>
                        @endif
                        </div>
                    </div>
                </div>
                @endif
                <!-- si es delivery y es inicio de factura -->
                @if($delivery == 1)          
                    @if($total == 0)   
                        <div class="row mt-2">
                            <div class="col-6">
                                <h6>Cliente:  {{$apeNomCli}}</h6>
                            </div>
                            <div class="col-6">
                                <h6>Rep:  {{$apeNomRep}}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h6>Dirección:  {{$dirCliente}}</h6>
                            </div>
                            <div class="col-6">
                                @if($saldoCtaCte < 0)
                                    <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                                @else
                                    <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                                @endif
                            </div>
                        </div>       
                    @else   <!-- si muestra datos en BD de la factura -->
                        <div class="row mt-2">
                            <div class="col-6">
                                <h6>Cliente:  {{$encabezado[0]->apeCli}} {{$encabezado[0]->nomCli}}</h6>
                            </div>
                            <div class="col-6">
                                <h6>Rep:  {{$encabezado[0]->apeRep}} {{$encabezado[0]->nomRep}}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h6>Dirección:  {{$encabezado[0]->calle}} {{$encabezado[0]->numero}}</h6>
                            </div>
                            <div class="col-6">
                                @if($saldoCtaCte < 0)
                                    <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                                @else
                                    <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                                @endif
                            </div>
                        </div>                
                    @endif          
                @endif
                <!-- @if($mostrar_datos == 1)
                    <div class="row mt-2">
                        <div class="col-6">
                            <h6>Cliente:  {{$apeNomCli}}</h6>
                        </div>
                        <div class="col-6">
                            <h6>Rep:  {{$apeNomRep}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6>Dirección:  {{$dirCliente}}</h6>
                        </div>
                        <div class="col-6">
                            @if($saldoCtaCte < 0)
                                <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                            @else
                                <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                            @endif
                        </div>
                    </div>   
                @endif -->

                @include('common.alerts')
        
                
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{$tab == 'factura' ? 'active' : ''}}" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Factura</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab == 'comanda' ? 'active' : ''}}" style="color:red;" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><b>Comanda</b></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab == 'enviarComanda' ? 'active' : ''}}" wire:click="enviarComanda()" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Enviar Comanda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab == 'verComanda' ? 'active' : ''}}" onclick="verCom()" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home2" aria-selected="false">Ver Comanda</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show {{$tab == 'factura' ? 'active' : ''}}" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="table-responsive scroll">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">DESCRIPCIÓN</th>
                                        <th class="text-right">P/UNITARIO</th>
                                        <th class="text-right">IMPORTE</th>
                                        <th class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($info as $r)
                                    <tr class="">
                                        <td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
                                        <td class="text-left">{{$r->producto}}</td>
                                        <td class="text-right">{{number_format($r->precio,2,',','.')}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                        <td class="text-center">
                                            @if(!$r->sectorcomanda_id)
                                                <ul class="table-controls">
                                                    <li>
                                                        <a href="javascript:void(0);"          		
                                                        onclick="Confirm('{{$r->id}}','{{$r->producto_id}}','{{$r->subproducto_id}}','{{$r->cantidad}}', null)"
                                                        data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                                    </li>
                                                </ul>
                                                <!-- @include('common.actions-destroy', ['destroy' => 'Facturas_destroy_item']) -->
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>  
                    </div>
                    <div class="tab-pane fade show {{$tab == 'comanda' ? 'active' : ''}}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="table-responsive scroll">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">DESCRIPCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoComanda as $r)
                                    <tr class="">
                                        <td class="text-center">{{number_format($r->cantidad,0)}}</td>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div> 
                    </div>
                    <div class="tab-pane fade show {{$tab == 'verComanda' ? 'active' : ''}}" id="home2" role="tabpanel" aria-labelledby="home-tab2">
                        <div class="table-responsive scroll">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">DESCRIPCIÓN</th>
                                        <th class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoComandaEnEspera as $r)
                                    <tr class="">
                                        <td class="text-center">{{number_format($r->cantidad,0)}}</td>
                                        <td class="text-left">{{$r->descripcion}}</td>
                                        <td class="text-center">
                                            <ul class="table-controls">
                                                <li>
                                                    <a href="javascript:void(0);"          		
                                                    onclick="Confirm('{{$r->id}}','{{$r->producto_id}}','{{$r->subproducto_id}}','{{$r->cantidad}}', 1)"
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
                @if($delivery == 1)
                <div class="row mr-1 pr-1 justify-content-end">
                    <h5 class="bg-danger ml-1 p-1" style="border-radius: 5px;">Total: $ {{number_format($total,2,',','.')}}</h5> 
                </div>
                @endif
            </div>			
		</div>
	</div>

    <div class="col-md-12 col-lg-6 layout-spacing">
        <div class="widget-content-area">
     

            <div class="row mt-2">
                <div class="col-sm-12 col-lg-4">
                    <!-- <div class="widget-content-area"> -->
                        @if($categorias->count() > 6)
                            <div class="widget-one scrollb"> 
                                <div class="scrollContent"> 
                                    @foreach($categorias as $c)                    
                                        <button style="width: 100%;"  wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-warning mb-1">{{$c->descripcion}}</button>
                                    @endforeach
                                </div>
                            </div>
                        @else                   
                            @foreach($categorias as $c)                    
                                <button style="width: 100%;"  wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-warning mb-1">{{$c->descripcion}}</button>
                            @endforeach                       
                        @endif
                    <!-- </div> -->
                </div>
                <div class="col-sm-12 col-lg-8">
                    <!-- <div class="widget-content-area"> -->
                        <div class="widget-one scrollb"> 
                            <div class="scrollContent"> 
                                @if($articulos != null)
                                    @if($mostrar_sp == 0)
                                        @foreach($articulos as $a)                    
                                            <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdateButton({{$a->id}})" type="button" class="btn btn-primary mb-1">{{$a->descripcion}}</button>
                                        @endforeach 
                                    @else
                                        @foreach($tiene_sp as $sp)                    
                                            <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdateButton({{$sp->id}})" type="button" class="btn btn-success mb-1">{{$sp->descripcion}}</button>
                                        @endforeach 
                                    @endif
                                @endif                   
                            </div>
                        </div>
                    <!-- </div> -->
                </div>
            </div>
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta"> 
            <input type="hidden" id="forzar_arqueo" wire:model="forzar_arqueo">
            <input type="hidden" id="ultima_factura" wire:model="ultima_factura">
            <input type="hidden" id="inicio_factura" value="{{$inicio_factura}}">  
            <input type="hidden" id="tiene_comentario" value="{{$comentario_comanda}}">  
            <input type="hidden" id="idFact" value="{{$factura_id}}">  
            <input type="hidden" id="delivery" value="{{$delivery}}">  
        </div> 
    </div>
    @include('livewire.facturas.modal-bar')  
    @include('livewire.facturas.modalCtacte')  
    @include('livewire.facturas.modalSalsas')    
    @else    
    @include('livewire.facturas.formaDePago')  
    @include('livewire.facturas.modalNroCompPago')  
	@endif        
</div>

<style type="text/css" scoped>
    .widget-h{
        position: relative;
        height:375px;
        overflow: hidden;
    }
    .scroll{
        position: relative;
        max-height: 220px;
        margin-top: .5rem;
        overflow: auto;
    }
    .scrollb {
        width: 100%;
        height:240px;
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
 	function Confirm(id, idProducto, idSubproducto, cantidad, comanda)
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
                window.livewire.emit('deleteRow', id, idProducto, idSubproducto, cantidad, comanda)    
                toastr.success('info', 'Registro eliminado con éxito')
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
    function verCom()
    {
        var x = document.getElementById("home-tab2");
              
        Swal.fire({
            title: 'Confirmar',
            text: '¿DESEAS MODIFICAR LA COMANDA?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Si`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                x.innerHTML = "Modificar Comanda";
                window.livewire.emit('modificarComanda');
            } else if (result.isDenied){
                x.innerHTML = "Ver Comanda";
                window.livewire.emit('verComanda', data)
            }
        })
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
        $('#cliente').val('Elegir')
        $('#empleado').val('Elegir')
        $('#modal').modal('show')
	}
	function save()
    {     
        if($('#cliente option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Cliente')
            return;
        }
        if($('#empleado option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Repartidor')
            return;
        }
        var data = JSON.stringify({
            'factura_id'   : $('#facturaId').val(),
            'cliente_id'   : $('#cliente option:selected').val(),
            'empleado_id'  : $('#empleado option:selected').val()
        });
        $('#modal').modal('hide')
        window.livewire.emit('modCliRep', data)
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
    function openModalComandas()
    {
        if($('#tiene_salsa').val() == 1) $('#divSalsas').show();
        else $('#divSalsas').hide(); 
        if($('#tiene_guarnicion').val() == 1) $('#divGuarniciones').show();
        else $('#divGuarniciones').hide();
        $('#texto_comanda').val($('#texto_base').val());
        $('#texto_comentario').val('');
        $('#modalSalsas').modal('show');
	}
    function crear_descripcion(descripcion, concepto)
    {
        var tGuarnicion = '';
        var tSalsa      = '';
        var texto_base  = $('#texto_base').val();
        var texto_comanda;
        var tComentario = $('#texto_comentario').val();

        if(concepto == 'salsa'){
            $('#texto_salsa').val(descripcion);
            tSalsa      = $('#texto_salsa').val();
            tGuarnicion = $('#texto_guarnicion').val();
            if(tGuarnicion == ''){
                texto_comanda = texto_base +' c/'+ tSalsa; 
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';   
            }else{
                texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion; 
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';
            }   
        }else{
            $('#texto_guarnicion').val(descripcion);
            tGuarnicion = $('#texto_guarnicion').val();
            tSalsa      = $('#texto_salsa').val();
            if(tSalsa == ''){
                texto_comanda = texto_base +' c/'+ tGuarnicion; 
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';
            }else{
                texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion;
                if(tComentario != '') texto_comanda = texto_comanda +' ('+ tComentario + ')';
            }   
        }
        $('#texto_comanda').val(texto_comanda); 
    }
    function agregarComentario()
    {
        var texto_base    = $('#texto_base').val();
        var tGuarnicion   = $('#texto_guarnicion').val();
        var tSalsa        = $('#texto_salsa').val();
        var tComentario   = $('#texto_comentario').val();
        var texto_comanda = '';
        
        if(tComentario != ''){
            if(tSalsa != '') texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion +' ('+ tComentario + ')';
            else texto_comanda = texto_base +' c/'+ tGuarnicion +' ('+ tComentario + ')';
        }else{
            if(tSalsa != '') texto_comanda = texto_base +' c/'+ tSalsa +' y '+ tGuarnicion;
            else texto_comanda = texto_base +' c/'+ tGuarnicion;
        }
        if(tGuarnicion == '' && tSalsa == '') texto_comanda = texto_base +' ('+ tComentario + ')';

        $('#texto_comanda').val(texto_comanda);
    }
    function StoreOrUpdate()
    {
        var texto_comanda = $('#texto_comanda').val();
        $('#modalSalsas').modal('hide')
        window.livewire.emit('StoreOrUpdate', texto_comanda);
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
    }, 120000);
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    // document.getElementById("link").addEventListener("click", function(){
    //     var id = document.getElementById("idFact").value
    //     var ruta = "{{url('print/visita')}}" + "/" + id
    //     var w = window.open(ruta,"_blank","width=100,height=100")
    //     setTimeout(function(){
    //         w.close();
    //     }, 1000); /* 1 Segundo*/
    // });
    
    window.onload = function() {
        if($('#forzar_arqueo').val() == 1){		
            swal({
                title: 'Caja inhabilitada!',
                text: 'Existe un Arqueo General pendiente de cierre...',
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
        if($('#delivery').val() == 1){
            openModal(2);
        }
        Livewire.on('facturaCobrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura Cobrada!!',
                showConfirmButton: false,
                timer: 1500
            })
            if($('#ultima_factura').val() == 1){
                window.location.href = "{{ url('notify') }}";
            }else window.location.href = "{{ url('abrir-mesa') }}";
		})          
        Livewire.on('facturaCtaCte',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura enviada a Cuenta Corriente!!',
                showConfirmButton: false,
                timer: 1500
            })
            if($('#ultima_factura').val() == 1){
                window.location.href="{{ url('notify') }}";
            }
		})
        Livewire.on('modal_comanda',()=>{
            openModalComandas();
		})
        Livewire.on('comandaEnviada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Comanda enviada exitosamente!!',
                showConfirmButton: false,
                timer: 1500
            })
		})
        Livewire.on('comandaVacia',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'No existen Comandas para enviar...',
                showConfirmButton: false,
                timer: 1500
            })
		})
        Livewire.on('comandaEnEspera',()=>{
            Swal.fire({
            title: 'Existe una Comanda esperando ser elaborada para la misma mesa...',
            text: '¿Qué acción desea realizar?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Unir las 2 comandas`,
            denyButtonText: `Dejar 2 comandas separadas`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('unirComandas', 'si');
                } else if (result.isDenied) {
                    window.livewire.emit('unirComandas', 'no');
                }
            })
		})
        Livewire.on('stock_no_disponible',(stock)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock == null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            } 
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Stock no disponible',
                text: texto + stock + unidades,
                showConfirmButton: true
            })
        })
    } 
</script>