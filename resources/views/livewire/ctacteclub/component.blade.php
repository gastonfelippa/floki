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
									wire:click="clearSocioSelected()">
									<i>Inicio</i>
								</span>
							</div>
						</div>
					</div>
    				<div class="col-sm-12 col-md-8 text-left">
					@if($socioId != '')  
						<div class="btn-group">
						@if($verHistorial == 0)	
							<button type="button" wire:click="verHistorial(1)" class="btn btn-dark" enabled>Ver Historial</button>
							@if($totalSocio > 0)
							<button type="button" class="btn btn-success" enabled>
								<a href="{{url('pdfResumenDeCuenta',array($socioId))}}" target="_blank">Imprimir Resumen de Cuenta</a>
							</button>
							<button type="button" onclick="Cobrar()" class="btn btn-warning" enabled>Cobrar</button>
							@endif
						@else
							<button type="button" wire:click="verHistorial(0)" class="btn btn-dark" enabled>Ver Saldo</button>
						@endif
						</div>
					@else 
						<button type="button" class="btn btn-success" enabled>
							<a href="{{url('pdfListadoCtaCteClub')}}" target="_blank">Imprimir Listado</a>
						</button>
					@endif 		
					</div>				
				</div>
				@if($search == '')
				<div class="col-6 mb-1">
					<h5><b>Total Cta Cte: $ {{number_format($suma,2,',','.')}}</b></h5>
					<h6 id="cli" style="display: none;"><b>Socio: </b><span >{{$nomApeSocio}}</span></h6>
				</div>
				@elseif($socioId != '')
				<div class="row mt-2">
					<div class="col-sm-12 col-md-4">
						<h5 id="cli" style="display: inline;"><b>Socio: </b><span >{{$nomApeSocio}}</span></h5>
					</div>
					@if($verHistorial == 0)
					<div class="col-sm-12 col-md-5 mt-1">
						<div class="row">
							<div class="col-8">
								<h6><b>Débitos Cuenta Cte.....$ </b></h6>
							</div>
							<div class="col-4 text-right">
								<h6><b>{{number_format($totalSocio,2,',','.')}}</b></h6>
							</div>
						</div>
						@if($totalSocio > 0)
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
							@if($socioId == '')
							<th></th> 
							@endif
								@if($search != '')
									@if($socioId != '')
										@if($verHistorial == 0)
											<th>                                    	
												<input onclick="manejarChecks()" id="modChecks" value="0" class="name" name= "thChecks" type="checkbox" checked>                                                                         
											</th>
										@endif
										<th class="text-center">FECHA</th>
										<th class="text-center">COMPROBANTE</th>
									@endif								
								@endif
								@if($search == '' || $socioId == '')
								<th class="text-left">SOCIO</th>
								<th class="text-left">DIRECCION</th>
								@endif
								<th class="text-center">IMPORTE</th>
								@if($search != '' && $socioId != '')								
									<th class="text-center">ACCION</th>
								@endif
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								@if($socioId == '')
									<td>{{$r->socio_id}}</td>
								@endif
								@if($search != '')
									@if($socioId != '')
										@if($verHistorial == 0)
										<td class="text-left">
											<input onclick="calcularTotal()"  id="{{$r->debito_id}}" value="{{$r->importe}}" class="name" name="checks" type="checkbox" checked>                                                                         
										</td>
										@endif
									<td class="text-center" style=" width: 150px;">{{\Carbon\Carbon::parse(strtotime($r->fecha))->format('d-m-Y')}}</td>
										@if($r->importe_debito == 1)
											<td class="text-center">DEB-{{str_pad($r->numero_deb, 6, '0', STR_PAD_LEFT)}}</td>
										@elseif( $r->importe_debito == 2)
											<td class="text-center" style="font-weight: bold;">DEB-{{str_pad($r->numero_deb, 6, '0', STR_PAD_LEFT)}} (resto $ {{number_format($r->resto,2,',','.')}})</td>
										@else
											<td class="text-center">REC-{{str_pad($r->numero_fdeb, 6, '0', STR_PAD_LEFT)}}</td>
										@endif
							
									@endif
								@endif
								@if($search == '' || $socioId == '')
								<td class="text-left">{{$r->apellido}} {{$r->nombre}}</td>
								<td>{{$r->calle}} {{$r->numero}} - {{$r->localidad}}</td>
								@endif

								@if($r->importe_debito == 1)
									<td class="text-right" style="color:red; width: 80px;">
									{{number_format($r->importe,2,',','.')}}</td>
								@elseif($r->importe_debito == 2)
									<td class="text-right" style="color:red;">{{number_format($r->importe,2,',','.')}}</td>
								@else
									<td class="text-right" style="color:green;">{{number_format($r->importe,2,',','.')}}</td>
								@endif
								@if($search != '' && $socioId != '')
									<td class="text-center">
										<ul class="table-controls">
											<li>
											@if($r->importe_debito == 1)                                 
												<a href="{{url('pdfFactDel',array($r->debito_id))}}" target="_blank" title="Ver Factura">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
											@elseif($r->importe_debito == 2)                                 
												<a href="{{url('pdfFactDel',array($r->debito_id))}}" target="_blank" title="Ver Factura">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
											@else
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
			<input id=caja_abierta type="hidden" wire:model="caja_abierta">
    	</div> 
    </div>
	@else
	@can('Ctacte_index')
	@include('livewire.ctacteclub.form')		
	@include('livewire.ctacteclub.modal')		
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
	function Cobrar() //ctdo/ctacte
    {   
		if($('#caja_abierta').val() == 0){
			Swal.fire('Oops!','No tenés una Caja Habilitada...')
        }else{
			Swal.fire({
				title: 'CONFIRMAR',
				text: "¿Qué acción deseas realizar con los débitos seleccionados?",
				icon: 'warning',
				showDenyButton: true,
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: `Cobro Total/Saldo`,
				cancelButtonText: `Cancelar`,
				denyButtonText: `Entrega a Cuenta`,
			}).then((result) => {
				if (result.isConfirmed) {
					var cantidad = 0;
					//recupero los Id de las facturas que se cobran
					var arrId = $('[name="checks"]:checked').map(function(){
						return this.id;
					}).get();               
					var data = JSON.stringify(arrId);
					//recupero los Importes de las facturas que se cobran
					var arrImporte = $('[name="checks"]:checked').map(function(){
						return this.value;
					}).get();
					var total = 0;  //calculo el total a cobrar
					for(var i of arrImporte) {
						total = parseInt(total) + parseInt(i);
						cantidad ++; 
					}
					window.livewire.emit('cobrar',data,total.toFixed(2),0,cantidad);
				} else if (result.isDenied) {
						var cantidad =0;
						var arrImporte = $('[name="checks"]:checked').map(function(){
							return this.value;
						}).get();
						for(var i of arrImporte) {
							cantidad ++;
						}
					if(cantidad == 0) Swal.fire('Ops!','Debes regresar y seleccionar el débito al cual deseas hacerle una entrega a cuenta...','info')
					else if(cantidad > 1) Swal.fire('Ops!','Debes regresar y seleccionar solo el débito al cual deseas hacerle una entrega a cuenta...','info')
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
						window.livewire.emit('cobrar',data,total,1,cantidad);
					}
				}
			})
		}
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
		console.log(esVisible);
		return esVisible;
	}
	//selecciona una fila de la tabla
	function ver_id() {
		if (esVisible('#cli')) {
			var rows = document.getElementById('tabla').getElementsByTagName('tr');
			for (i = 0; i < rows.length; i++) {
				rows[i].onclick = function() {
				var idSocio = this.getElementsByTagName('td')[0].innerHTML;
				// var nomCli = this.getElementsByTagName('td')[1].innerHTML;	
				window.livewire.emit('mostrar_debitos', idSocio);
				}
			}
		}
	}
	function mostrarInput(){
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4' || $('[id="formaDePago"]').val() == '5') {
			$('#modalNroComprobanteDePago').modal('show');
		}else{
			guardarDatosPago();
		}	
	}
	function guardarDatosPago(){
        if($('[id="formaDePago"]').val() != 1 && $('[id="nroCompPago"]').val() <= 0){ 
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Faltan datos, se cobrará como efectivo!!',
                showConfirmButton: false,
                timer: 1500
            })
            $('[id="formaDePago"]').val(1)
        }else{
			$('[id="num"]').val($('[id="nroCompPago"]').val())
			if($('[id="num"]').val() != ''){
				var formaDePago = $('[id="formaDePago"]').val();
				var nroCompPago = $('[id="nroCompPago"]').val();
			}else{
				$('[id="formaDePago"]').val(1)           
			}		
			window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago);
		}
	}
	function datos_pago()
    { 
        if($('[id="formaDePago"]').val() != 1 && $('[id="nroCompPago"]').val() <= 0){ 
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Faltan datos, se cobrará como efectivo!!',
                showConfirmButton: false,
                timer: 1500
            })
            $('[id="formaDePago"]').val(1)
        }else{
            window.livewire.emit('StoreOrUpdate')
        }    
    }
    window.onload = function() {
        document.getElementById("search").focus()
    }
</script>