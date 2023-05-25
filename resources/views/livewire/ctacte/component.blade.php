<div class="row layout-top-spacing justify-content-center">  
	@include('common.alerts')
	@include('common.messages')
	@if($action == 1) 
    <div class="col-sm-12 col-md-8 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Cuenta Corriente</b></h3>
    				</div> 
    			</div>
				<div class="row">
					<div class="col-sm-12 col-md-4 mb-2">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
							</div>
							<input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
							<div class="input-group-append">
								<span class="input-group-text"
									wire:click="clearClientSelected()">
									<i>Inicio</i>
								</span>
							</div>
						</div>
					</div>
    				<div class="col-sm-12 col-md-8 text-left">
					@if($clienteId != '')  
						<div class="btn-group">
						@if($verHistorial == 0)	
							<button type="button" wire:click="verHistorial(1)" class="btn btn-dark" enabled>Ver Historial</button>
							@if($totalCli > 0)
							<button type="button" class="btn btn-success" enabled>
								<a href="{{url('pdfResumenDeCuenta',array($clienteId))}}" target="_blank">Imprimir Resumen de Cuenta</a>
							</button>
							<button type="button" onclick="prepararCobro()" class="btn btn-warning" enabled>Cobrar</button>
							@endif
						@else
							<button type="button" wire:click="verHistorial(0)" class="btn btn-dark" enabled>Ver Saldo</button>
						@endif
						</div>
					@else 
						<button type="button" class="btn btn-success" enabled>
							<a href="{{url('pdfListadoCtaCte')}}" target="_blank">Imprimir Listado</a>
						</button>
					@endif 		
					</div>				
				</div>
				@if($search == '')
					<div class="col-6 mb-1">
						<h5><b>Total Cta Cte: $ {{number_format($suma,2,',','.')}}</b></h5>
						<h6 id="cli" style="display: none;"><b>Cliente: </b><span >{{$nomApeCli}}</span></h6>
					</div>
				@elseif($clienteId != '')
					<div class="row mt-2">
						<div class="col-sm-12 col-md-4 mb-1">
							<h5 id="cli" style="display: inline;"><b>Cliente: </b><span >{{$nomApeCli}}</span></h5>
						</div>
						@if($verHistorial == 0)
							<div class="col-sm-12 col-md-6 mt-1">
								<div class="row">
									<div class="col-8">
										<h6><b>Facturas Cuenta Cte.....$ </b></h6>
									</div>
									<div class="col-4 text-right">
										<h6><b>{{number_format($totalCli,2,',','.')}}</b></h6>
									</div>
								</div>
								@if($totalCli > 0)
								<div class="row">
									<div class="col-8">		
										<h6><b>Entregas a cuenta.........$ </b></h6>
										</div>
									<div class="col-4 text-right">	
										<h6><b>- {{number_format($importeEntrega,2,',','.')}}</b></h6>
									</div>
								</div>
								<div class="row" style="color: #ff7f26">
									<div class="col-8">	
										<h6 style="color: #ff7f26"><b>Saldo................................$ </b></h6>
									</div>
									<div class="col-4 text-right">								
										<b><h6 style="color: #ff7f26">{{number_format($saldo,2,',','.')}}</b></h6>							
									</div>
								</div>
								@endif
							</div>
						@endif
					</div>
				@endif			
				<div class="table-responsive scroll">
					<table onclick="ver_id()" id="tabla" class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								@if($clienteId == '')
									<th></th> 
								@endif
								@if($search != '' && $clienteId != '')
									@if($verHistorial == 0)
										<th>                                    	
											<input onclick="manejarChecks()" id="modChecks" value="0" class="name" name= "thChecks" type="checkbox" checked>                                                                         
										</th>
									@endif
									<th class="text-center">FECHA</th>
									<th class="text-center">COMPROBANTE</th>
								@endif
								@if($search == '' || $clienteId == '')
									<th class="text-left">CLIENTE</th>
									<th class="text-left">DIRECCION</th>
								@endif
								<th class="text-center">IMPORTE</th>
								@if($search != '' && $clienteId != '')								
									<th class="text-center">ACCION</th>
								@endif
							</tr>
						</thead>
						<tbody>	
							@foreach($info as $r)
							<tr>
								@if($clienteId == '')
								<td>{{$r->cliente_id}}</td>
								@endif
								@if($search != '' && $clienteId != '')
									@if($verHistorial == 0)
										@if($r->importe_factura == 1)   <!--si es una factura -->
											<td class="text-left">
												<input onclick="calcularTotal()"  id="{{$r->factura_id}}" value="{{$r->importe}}" class="name" name="checks" type="checkbox" checked>                                                                         
											</td>
											@else
											<th></th>
										@endif
									@endif
									<td class="text-center" style=" width: 150px;">{{\Carbon\Carbon::parse(strtotime($r->fecha))->format('d-m-Y')}}</td>
									@if($r->importe_factura == 1)	<!--si es una factura -->
										<td class="text-center">FAC-{{str_pad($r->numero_fac, 6, '0', STR_PAD_LEFT)}}</td>
									@else							<!--si es un recibo -->
										<td class="text-center">REC-{{str_pad($r->numero_rec, 6, '0', STR_PAD_LEFT)}}</td>
									@endif	
								@endif
								@if($search == '' || $clienteId == '')
									<td>{{$r->apellido}} {{$r->nombre}}</td>
									<td>{{$r->calle}} {{$r->numero}} - {{$r->localidad}}</td>
								@endif

								@if($r->importe_factura == 1)   <!--si es una factura -->
									<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
								@else                           <!--si es un recibo -->
									<td class="text-right">({{number_format($r->importe,2,',','.')}})</td>
								@endif
								@if($search != '' && $clienteId != '')
									<td class="text-center">
										<ul class="table-controls">
											<li>
											@if($r->importe_factura == 1)   <!-- si es una factura -->                              
												<a href="{{url('pdfFactDel',array($r->factura_id))}}" target="_blank" title="Ver Factura">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>      
											@else                           <!-- si es un recibo -->  
											<a href="{{url('pdfRecibos',array($r->recibo_id))}}" target="_blank" title="Ver Recibo">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>  											
											@endif
											</li>										
										</ul>
									</td>								
								@endif								
							</tr>
							@endforeach							
						</tbody>
					</table>
				</div>
			</div>
			<input id="caja_abierta" type="hidden" wire:model="caja_abierta">
			<input id="buscar" type="hidden" wire:model="search">
			
    	</div> 
    </div>
	@else
		@can('Ctacte_index')
			@include('livewire.ctacte.formaDePago')			
			@include('livewire.ctacte.modalCheques')		
			@include('livewire.ctacte.modalNroCompPago')	
			@include('livewire.ctacte.modalBancos')	
		@endcan
	@endif
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 185px;
    margin-top: .5rem;
    overflow: auto;
}
</style>
<!-- probar instalar la sig linea en public->asset -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<script type="text/javascript">
	function prepararCobro() //ctdo/ctacte
    { 
		if($('#caja_abierta').val() == 0){
			Swal.fire('Oops!','No tenés una Caja Habilitada...')
        }else{
			Swal.fire({
				title: 'CONFIRMAR',
				text: "¿Qué acción deseas realizar con las facturas seleccionadas?",
				icon: 'warning',
				showDenyButton: true,
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: `Cobro Total/Saldo`,
				cancelButtonText: `Cancelar`,
				denyButtonText: `Entrega a Cuenta`,
			}).then((result) => {
				if (result.isConfirmed) {   //si el cobro es por el total o el saldo
					var cantidad = 0;

					//creo un array con los Id de las facturas que se cobran y luego lo paso a Json 
					var arrId = $('[name="checks"]:checked').map(function(){
						return this.id;
					}).get();  
					var data = JSON.stringify(arrId);

					//creo un array con los Importes de las facturas que se cobran
					var arrImporte = $('[name="checks"]:checked').map(function(){
						return this.value;
					}).get();
					var total = 0;  //calculo el total a cobrar
					for(var i of arrImporte) {
						total = parseInt(total) + parseInt(i);
						cantidad ++; 
					}
					window.livewire.emit('preparar_cobro',data,total.toFixed(2),'0',cantidad);
				} else if (result.isDenied) {    //si es una entrega
						var cantidad =0;
						var arrImporte = $('[name="checks"]:checked').map(function(){
							return this.value;
						}).get();
						for(var i of arrImporte) {
							cantidad ++;
						}
					if(cantidad == 0) Swal.fire('Ops!','Debes regresar y seleccionar la factura a la cual deseas hacerle una entrega a cuenta...','info')
					else if(cantidad > 1) Swal.fire('Ops!','Debes regresar y seleccionar solo la factura a la cual deseas hacerle una entrega a cuenta...','info')
					else {  //recupero el Id de la factura a la cual se le hace una entrega
						var arrId = $('[name="checks"]:checked').map(function(){
							return this.id;
						}).get();               
						var data = JSON.stringify(arrId);
						//recupero el Importe de la factura a la cual se le hace una entrega
						var arrImporte = $('[name="checks"]:checked').map(function(){
							return this.value;
						}).get();
						var total = JSON.stringify(arrImporte);
						//envío el id, el total y la señal de entrega
						window.livewire.emit('preparar_cobro',data,total,'1',cantidad);
					}
				}
			})
		}
    }
	function cobrar_factura()
    {       
        var saldo           = Number.parseFloat($('[id="saldoFactura"]').val()).toFixed(2);
        var importe         = Number.parseFloat($('[id="importe"]').val()).toFixed(2);
        var entrega         = $('[id="entrega"]').val();
        var formaDePago     = $('[id="formaDePago"]').val();
        var nroCompPago     = $('[id="num"]').val();
        var terminarFactura = '1';

		if(entrega == '1'){
			if(importe >= saldo){
				Swal.fire('Cancelado','El importe a registrar debe ser menor que el saldo de la factura... en esta vista solo se registran entregas','info');
				resetear();
			}else{
				terminarFactura = '0'; 
				Swal.fire({
					icon: 'question',
					title: 'Confirmar',
					text: '¿Deseas registrar el pago de esta factura?',
					showDenyButton: true,
					confirmButtonColor: '#3085d6',
					denyButtonColor: '#d33',
					confirmButtonText: 'Aceptar',
					denyButtonText: 'Cancelar',
					closeOnConfirm: false
				}).then((result) => {
					if (result.isConfirmed) {                  
						window.livewire.emit('StoreOrUpdate',formaDePago,nroCompPago,importe,terminarFactura);
					} else if (result.isDenied) {
						Swal.fire('Cancelado','Tu registro está a salvo :)','info')
						resetear();
					}
				});
			}
		}else{
			if(importe != saldo){
				Swal.fire('Cancelado','El importe a registrar debe ser igual que el saldo de la factura...', 'info');
				resetear();
			}else{
				Swal.fire({
					icon: 'question',
					title: 'Confirmar',
					text: '¿Deseas registrar el pago de esta factura?',
					showDenyButton: true,
					confirmButtonColor: '#3085d6',
					denyButtonColor: '#d33',
					confirmButtonText: 'Aceptar',
					denyButtonText: 'Cancelar',
					closeOnConfirm: false
				}).then((result) => {
					if (result.isConfirmed) {                  
						window.livewire.emit('StoreOrUpdate',formaDePago,nroCompPago,importe,terminarFactura);
					} else if (result.isDenied) {
						Swal.fire('Cancelado','Tu registro está a salvo :)','info')
						resetear();
					}
				});
			}
		}       
    }
	function resetear()
    {
        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldoFactura').val()).toFixed(2));
        return;
    }
	function manejarChecks()
	{
		var isSelected = $('[id="modChecks"]').is(":checked");
		if(isSelected) $('[name="checks"]').prop("checked", true);
		else $('[name="checks"]').prop("checked", false);
		calcularTotal();
	}
	function calcularTotal()
	{
		$(document).ready(function() {
			$('[name="checks"]').change(function() {  
			//recupero los Importes de las facturas que se cobran
				var arrImporte = $('[name="checks"]:checked').map(function(){
					return this.value;
				}).get();
				var total =0;
            	for(var i of arrImporte) total = parseInt(total) + parseInt(i); 

				$('#impAcobrar').text(total.toFixed(2));
			});
		});
	}
	function esVisible(elemento) {
		var esVisible = true;
		if($(elemento).is(':visible') && $(elemento).css("visibility") != "hidden"
				&& $(elemento).css("opacity") > 0) {
			esVisible = false;
		}
		return esVisible;
	}
	//selecciona una fila de la tabla
	function ver_id() {
		var buscar = document.getElementById('search').value;

		if(buscar != ''){
			if (esVisible('#cli')) {
				var rows = document.getElementById('tabla').getElementsByTagName('tr');
				for (i = 0; i < rows.length; i++) {
					rows[i].onclick = function() {
					var idCli = this.getElementsByTagName('td')[0].innerHTML;
					// var nomCli = this.getElementsByTagName('td')[1].innerHTML;
					window.livewire.emit('mostrar_facturas', idCli);
					}
				}
			}
		}
	}
	function mostrarInput()
    {
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
     
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4') {        
            $('[id="importeComp"]').val(Number.parseFloat($('[id="saldoFactura"]').val()).toFixed(2));        
			$('#modalNroComprobanteDePago').modal('show');
        }else if($('[id="formaDePago"]').val() == '5'){
            if($('[id=clienteId]').val() && $('[id=clienteId]').val() != $('[id=esConsFinal]').val()){
                $('#modalCheques').modal('show');
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'Primero debes cargar un Cliente!!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('[id="formaDePago"]').val(1);
            }
        }else if($('[id="formaDePago"]').val() == '6'){
            if($('[id=clienteId]').val() && $('[id=clienteId]').val() != $('[id=esConsFinal]').val()){
                Swal.fire({
                    icon: 'question',
                    title: 'Confirmar',
                    text: '¿Deseas enviar esta factura a Cuenta Corriente?',
                    showDenyButton: true,
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    denyButtonText: 'Cancelar',
                    closeOnConfirm: false
                    }).then((result) => {
                        if (result.isConfirmed) {                    
                            window.livewire.emit('factura_ctacte');
                        } else if (result.isDenied) {
                            Swal.fire('Cancelado','Tu registro está a salvo :)','info')
                            resetear();
                        }
                    });
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'Primero debes cargar un Cliente!!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('[id="formaDePago"]').val(1);
            }
		}else{
			guardarDatosPago();
		}
	}
	function guardarDatosCheque()
    {
        $('[id="importe"]').val(Number.parseFloat($('[id="importe"]').val()).toFixed(2));
        $('[id="importeCheque"]').val(Number.parseFloat($('[id="importeCheque"]').val()).toFixed(2));
        var saldo           = $('[id="importe"]').val();       //saldo
        var importe         = $('[id="importeCheque"]').val();
        var terminarFactura = '1';

        if(parseInt(importe) > parseInt(saldo)){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
			return;
        } 
        
        if(importe != saldo) terminarFactura = '0';

        var data = JSON.stringify({
			'banco'           : $('#banco').val(),
			'numero'          : $('#numCheque').val(),
			'fechaDeEmision'  : $('#fechaDeEmision').val(),
			'fechaDePago'     : $('#fechaDePago').val(),
			'importe'         : $('#importeCheque').val(),
			'cuitTitular'     : $('#cuitTitular').val(),
			'terminarFactura' : terminarFactura,
		});		
		window.livewire.emit('enviarDatosCheque', data);
	}
	function guardarDatosPago()
    {
        $('[id="num"]').val($('[id="nroCompPago"]').val());
		$('[id="importe"]').val(Number.parseFloat($('[id="importeComp"]').val()).toFixed(2));        
        $('[id="saldoFactura"]').val(Number.parseFloat($('[id="saldoFactura"]').val()).toFixed(2));
        var formaDePago = $('[id="formaDePago"]').val();
        var nroCompPago = $('[id="nroCompPago"]').val();
        var importe     = $('[id="importe"]').val();
        var saldo       = $('[id="saldoFactura"]').val();
        
        if(parseInt(importe) > parseInt(saldo)){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        } 
		window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago,importe);
	}
	function openModalBancos()
    {
        $('#modalCheques').modal('hide')
        $('#banco').val('')
        $('#sucursal').val('')
        $('#modalBancos').modal('show')
	}
	function guardarBanco()
    {      
        if($('#descripcion').val() == '') {
            toastr.error('Ingresa un nombre válido para el Banco')
            return;
        }
        if($('#sucursal').val() == '') {
            toastr.error('Ingresa un nombre válido para la Sucursal')
            return;
        }
        var data = JSON.stringify({
            'banco'    : $('#descripcion').val(),
            'sucursal' : $('#sucursal').val()
        });
       
        $('#modalBancos').modal('hide');
        window.livewire.emit('agregarBanco', data);

		resetear();
    }
	function resetear()
    {
        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldoFactura').val()).toFixed(2));
        return;
    }
    window.onload = function() {
        document.getElementById("search").focus();
		Livewire.on('facturaConEntrega',()=>{
			Swal.fire('Ops!','En la selección de facturas existe alguna que tiene entregas a cuenta,' +
				' para continuar primero debes cancelar dicha factura y luego podrás hacer lo mismo' +
				' con el resto.','info')
		})
		Livewire.on('bancoCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Banco fue creado!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('chequeCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Cheque se registró correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
		Livewire.on('facturaCobrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura Cobrada!!',
                showConfirmButton: false,
                timer: 1500
            });
            if($('#ultima_factura').val() == 1){
                window.location.href="{{ url('notify') }}";
            }
		})
        Livewire.on('cobroRegistrado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El cobro a cuenta fue registrado!!',
                showConfirmButton: false,
                timer: 2000
            });            
            resetear();
        })
    }
</script>