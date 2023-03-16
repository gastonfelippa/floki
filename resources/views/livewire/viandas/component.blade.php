<div class="row layout-top-spacing justify-content-center">
@if($action == 1)
<div class="col-12 layout-spacing">
@include('common.alerts') 
<div class="widget-content-area">
<div class="widget-one">
    <h3 class="text-center"><b>Viandas Diarias</b></h3>            
    @include('common.messages') 
    <div class="row"> 
        <div class="btn-group col-12 col-md-4">
            <button type="submit" onclick="cambiarDiv(2)" class="btn btn-info mt-1">Ver Lista Cocina</button>
            <button id="btn_imp_viandas" type="submit" class="btn btn-success mt-1">
                <a href="{{url('pdfViandas')}}" target="_blank">Imprimir</a>
            </button>
        </div>
        <div class="btn-group col-12 col-md-4">
            <button type="submit" onclick="cambiarDiv(3)" class="btn btn-info mt-1">Ver Comentarios</button>
            <!-- <button id="btn_imp_comentarios" type="submit" class="btn btn-success mt-1" disabled>
                <a href="{{url('pdfViandas')}}" target="_blank">Imprimir</a>
            </button> -->
        </div>
        <div class="btn-group col-12 col-md-4 ">
        @if($mostrar_facturas)
            <button type="submit" onclick="cambiarDiv(1)" class="btn btn-info mt-1" enabled>Ver Lista Facturas</button>
            <button id="btn_grabar" name="grabarTodas" type="submit" onclick="ConfirmGrabar()" class="btn btn-danger mt-1" enabled>Grabar todas</button>
        @else
            <button type="submit" onclick="cambiarDiv(1)" class="btn btn-info mt-1" disabled>Ver Lista Facturas</button>
            <button id="btn_grabar" name="grabarTodas" type="submit" onclick="ConfirmGrabar()" class="btn btn-danger mt-1" disabled>Grabar todas</button>
            @endif
        </div>
    </div>
    <div class="row mt-2">        
        <div class="col-sm-12 col-md-4">                     
            <h5 class="text-center py-1" style="background-color:#1A5276;color:white;">Cantidad de Viandas a preparar: <span id="cV_a_preparar">{{$cantidad_a_preparar}}</span></h5>
        <div class="px-2 pt-1" style="background-color:#2471A3;" id="cVFactura">
            <h6 style="color:white;">Cantidad de Viandas a grabar: <span id="cV_a_grabar"></span></h6>
            <h6 style="color:white;">Cantidad de Viandas grabadas: <span id="cV_grabadas">{{$cantidad_grabadas}}</span></h6>
            <h6 style="color:white;">Cantidad de Viandas canceladas: <span id="cV_canceladas"></span></h6>
        </div>
            <h6><b>Fecha de Consulta</b></h6>
            <input id="fecha" onchange="cambiarDiv(2)" type="text" class="form-control flatpickr flatpickr-input sm-control" placeholder="{{\Carbon\Carbon::now()->format('d-m-Y')}}" autocomplete="off">             
            <h6 class="mt-1"><b>Repartidor/Caja Salón</b></h6> 
            <select id="repartidor" wire:model="repartidor" class="form-control text-center">
                <option value="Elegir">Elegir</option>
                @foreach($repartidores as $r)
                <option value="{{ $r->id }}">
                    {{$r->apellido}} {{$r->name}}
                </option>                                       
                @endforeach                              
            </select>          
        </div>   
        <div class="col-sm-12 col-md-8">
            <div id="div1" class="col-sm-12 col-md-10" style="display:none;">
                <div class="table-resposive scroll">
                    <table class="table table-hover table-checkable table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">CLIENTE</th>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-center">PRODUCTO</th>
                                <th class="text-right">PR UNIT</th>
                                <th class="text-right">IMPORTE</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info2 as $r)
                            <tr>
                                @if($r->habilitar_facturas == 1)
                                    <td class="text-left">                                  
                                        <input value="{{$r->cantidad}}" id="{{$r->cliente_id}}" class="name" name="checks" type="checkbox" checked>              
                                    </td>
                                    <td class="text-left">{{$r->apellido}}, {{$r->nombre}}</td>
                                    <td class="text-center">{{$r->cantidad}}</td>
                                    <td class="text-center">{{$r->descripcion}}</td>
                                    <td class="text-right">{{$r->precio_venta_l1}}</td>
                                    <td class="text-right">{{number_format($r->importe,2)}}</td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li><a href="javascript:void(0);" onclick="openModal('{{$r}}')" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a></li>                                        
                                            <li><a href="javascript:void(0);" onclick="Cobrar({{$r->cliente_id}},{{$r->importe}})" data-toggle="tooltip" data-placement="top" title="Cobrar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-dark"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></li>
                                        </ul>
                                    </td>
                                @else
                                    <td class="text-left" style="background-color:#F9E79F;"></td>
                                    <td class="text-left" style="background-color:#F9E79F;">{{$r->apellido}}, {{$r->nombre}}</td>
                                    <td class="text-center" style="background-color:#F9E79F;">{{$r->cantidad}}</td>
                                    <td class="text-center" style="background-color:#F9E79F;">{{$r->descripcion}}</td>
                                    <td class="text-right" style="background-color:#F9E79F;">{{$r->precio_venta_l1}}</td>
                                    <td class="text-right" style="background-color:#F9E79F;">{{number_format($r->importe,2)}}</td>
                                    <td class="text-center" style="background-color:#D4AC0D;font-weight: bold;">{{$r->estado}}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> 
            </div>
            <div id="div2" class="col-sm-12 col-md-6" style="display:block;">
                <div class="table-resposive scroll">
                    <table class="table table-hover table-checkable table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">HORA</th>
                                <th class="text-center">CLIENTE</th>
                                <th class="text-center">CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr> 
                                <td class="text-center">{{\Carbon\Carbon::parse($r->hora)->format('H:i')}}</td>
                                <td class="text-left">{{$r->apellido}}, {{$r->nombre}}</td>                                        
                                <td class="text-center">{{$r->cantidad}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>   
            <div id="div3" class="col-sm-12 col-md-6" style="display:none;">
                <div class="table-resposive scroll">
                    <table class="table table-hover table-checkable table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">CLIENTE</th>
                                <th class="text-center">COMENTARIO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                                @if($r->comentarios != '')
                                    <tr> 
                                        <td class="text-left">{{$r->apellido}}, {{$r->nombre}}</td>                                        
                                        <td class="text-center">{{$r->comentarios}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>    
        </div>
    </div>
    <input type="hidden" id="caja_abierta" wire:model="caja_abierta">
    <input type="hidden" id="forzar_arqueo" wire:model="forzar_arqueo">  
</div>
</div>
</div>
@include('livewire.viandas.modal')
@else    
    @include('livewire.viandas.formaDePago')  
    @include('livewire.viandas.modalNroCompPago')  
@endif 
</div>  


<style type="text/css" scoped>
.scroll{
    height: 270px;
    position: relative;
    margin-top: .5rem;
    overflow: auto;
}
</style>
<!-- probar instalar la sig linea en public->asset -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">
    function ConfirmGrabar() 
    {
        if($('#repartidor option:selected').val() == 'Elegir'){
            Swal.fire('Elige una opción válida para el Repartidor')
            return;
        }               
    	let me = this
    	swal({
    		title: 'CONFIRMAR',
    		text: '¿DESEAS ENVIAR A CUENTA CORRIENTE A TODOS LOS REGISTROS SELECCIONADOS?',
    		type: 'warning',
    		showCancelButton: true,
    		confirmButtonColor: '#3085d6',
    		cancelButtonColor: '#d33',
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false
    	},
    	function() { 
                var arr = $('[name="checks"]:checked').map(function(){
                    return this.id;
                }).get();               
                var data = JSON.stringify(arr);    
                window.livewire.emit('grabar',data);
    		swal.close();
    	})
    }
    function Cobrar(idCli,total)    //fact pendientes
    {
        if($('#repartidor option:selected').val() == 'Elegir'){
            Swal.fire('Elige una opción válida para el Repartidor')
            return; 
        }
        Swal.fire({
            title: 'Elige una opción...',
            showDenyButton: true,
            showCancelButton: true,
            cancelButtonText: `Cancelar`,
            confirmButtonText: `Contado`,
            denyButtonText: `Cuenta Corriente`,
        }).then((result) => {
            if(result.isConfirmed) {
                window.livewire.emit('elegirFormaDePago',idCli, total);
            }else if (result.isDenied) {
                window.livewire.emit('factura_ctacte', idCli)                                  
            }
        })
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
    function openModal(row) 
    {
        if($('#repartidor option:selected').val() == 'Elegir'){
            Swal.fire('Elige una opción válida para el Repartidor')
            return;
        }
        var info = JSON.parse(row)
        $('.modal-title').text('Cliente: '+ info.apellido +' '+ info.nombre)
        $('#cliente_id').val(info.cliente_id)
        $('#cliente_id').hide()
        $('#cantidad').val(info.cantidad)
        $('#producto').val(info.id)
        $('#modalVianda').modal('show')
    }
    function save()
    { 
        if($('#cantidad').val() == '')
        {
            toastr.error('El campo Cantidad no puede estar vacío')
            return;
        }
        if($('#producto option:selected').val() == 'Elegir')
        {
            toastr.error('Elige una opción válida para el Producto')
            return;
        }
        var data = JSON.stringify({
            'cliente_id'   : $('#cliente_id').val(),
            'cantidad'     : $('#cantidad').val(),
            'producto_id'  : $('#producto option:selected').val()
        });
        
        $('#modalVianda').modal('hide')
        window.livewire.emit('createFactFromModal', data)
    } 
    function cambiarDiv(idButton) 
    {  
        switch(idButton) {
            case 1:              //Ver Lista Facturas
                if($('#caja_abierta').val() == '0'){
                    Swal.fire('Oops!','No hay ninguna Caja Habilitada...')
                    return;
                }else{
                    document.getElementById('div1').style.display = 'block';
                    document.getElementById('div2').style.display = 'none';
                    document.getElementById('div3').style.display = 'none';
                    document.getElementById('cVFactura').style.display = 'block';
                    document.getElementById("btn_imp_viandas").setAttribute("disabled",false);
                    document.getElementById("btn_imp_comentarios").setAttribute("disabled",false);
                    if(document.getElementById("btn_grabar").disabled == true){
                        document.getElementById("btn_grabar").removeAttribute("disabled");
                    }
                    // if(document.getElementById("repartidor").disabled == true){
                    //     document.getElementById("repartidor").removeAttribute("disabled");
                    // }
                    contarViandas();
                }                
                break;
            case 2:              //Ver Lista Cocina
                document.getElementById('div1').style.display = 'none';
                document.getElementById('div2').style.display = 'block';
                document.getElementById('div3').style.display = 'none';
                document.getElementById('cVFactura').style.display = 'none';
                //document.getElementById("repartidor").setAttribute("disabled",false);
                document.getElementById("btn_grabar").setAttribute("disabled",false);
                document.getElementById("btn_imp_comentarios").setAttribute("disabled",false);
                if(document.getElementById("btn_imp_viandas").disabled == true){
                    document.getElementById("btn_imp_viandas").removeAttribute("disabled");
                }
                break;
            case 3:               //Ver Comentarios
                document.getElementById('div1').style.display = 'none';
                document.getElementById('div2').style.display = 'none';
                document.getElementById('div3').style.display = 'block';
                document.getElementById('cVFactura').style.display = 'none';
                //document.getElementById("repartidor").setAttribute("disabled",false);
                document.getElementById("btn_grabar").setAttribute("disabled",false);
                document.getElementById("btn_imp_viandas").setAttribute("disabled",false);
                if(document.getElementById("btn_imp_comentarios").disabled == true){
                    document.getElementById("btn_imp_comentarios").removeAttribute("disabled");
                }
                break;
            default:
        }
    }  
    function contarViandas()
    {
        var viandas_a_preparar = $('#cV_a_preparar').text();
        var viandas_grabadas   = $('#cV_grabadas').text();
        var viandas_a_grabar   = 0;
        var viandas_canceladas = 0;

        //verifico las viandas que están chequeadas para grabar
        var arr_a_grabar = $('[name="checks"]:checked').map(function(){ 
            return this.value;          
        }).get();
        //recorro el array para calcular el total de cV_a_grabar
        for(var i of arr_a_grabar) viandas_a_grabar = parseInt(viandas_a_grabar) + parseInt(i);         

        //calculo las viandas canceladas
        viandas_canceladas = viandas_a_preparar - viandas_grabadas - parseInt(viandas_a_grabar); 

        $('#cV_a_grabar').text(viandas_a_grabar);
        $('#cV_canceladas').text(viandas_canceladas);

        //muestra u oculta el btn_grabar
        if($('#cV_a_grabar').text() > 0) $('#btn_grabar').show();
        else $('#btn_grabar').hide(); 
        //cambiarDiv(1);
    }
    $(document).ready(function() {  //esta función se utiliza para ocultar el btn_grabar cuando no haya nada seleccionado
        $('[name="checks"]').click(function() {            
            var arr = $('[name="checks"]:checked').map(function(){            
                return this.value;           
            }).get();
                            
            var total =0;
            for(var i of arr) total = parseInt(total) + parseInt(i);    
            $('#cV_a_grabar').text(total);

            contarViandas();   
        });
    });  
    $(document).ready(function() {
        $('[id="fecha"]').change(function() {
            var data =  $('#fecha').val(); 
            window.livewire.emit('cambiarFecha', data);
        });
    });   
    window.onload = function() { 
        cambiarDiv(2);
        flatpickr("#fecha", {
            minDate: "today",
            dateFormat: "d-m-Y",
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
                }, 
                months: {
                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
                longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                },
            }, 
        }); 
        if($('#forzar_arqueo').val() == 1){		
            swal({
                title: 'Cajas inhabilitadas!',
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
        Livewire.on('facturaCobrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura Cobrada!!',
                showConfirmButton: false,
                timer: 1500
            })
            location.reload();
		})         
        Livewire.on('facturaCtaCte',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura enviada a Cuenta Corriente!!',
                showConfirmButton: false,
                timer: 1500
            })
            location.reload();
		})
        Livewire.on('facturasCreateCtaCte',()=>{
            Swal.fire({
                title: 'Facturas creadas exitosamente!',
                text: "Fueron enviadas a Cuenta Corriente...",
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok!'
                }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })            
		})
        Livewire.on('mostrar_viandas',()=>{
          cambiarDiv(2);           
		})
        Livewire.on('mostrar_vista_facturas',()=>{
          cambiarDiv(1);           
		})
    }
</script>






