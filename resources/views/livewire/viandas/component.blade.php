<div class="row layout-top-spacing">
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
            <button id="btn_imp_comentarios" type="submit" class="btn btn-success mt-1" disabled>
                <a href="{{url('pdfViandas')}}" target="_blank">Imprimir</a>
            </button>
        </div>
        <div class="btn-group col-12 col-md-4 ">
        @if($mostrar_facturas)
            <button type="submit" onclick="cambiarDiv(1)" class="btn btn-info mt-1" enabled>Ver Lista Facturas</button>
        @else
            <button type="submit" onclick="cambiarDiv(1)" class="btn btn-info mt-1" disabled>Ver Lista Facturas</button>
        @endif
            <button id="btn_grabar" name="grabarTodas" type="submit" onclick="ConfirmGrabar()" class="btn btn-danger mt-1" disabled>Grabar todas</button>
        </div>
    </div>
    <div class="row mt-3">        
        <div class="col-sm-12 col-md-4">                     
            <h4><b>Cantidad Viandas: </b><span id="cViandas"></span></h4>
            <br>
            <h5><b>Fecha de Consulta</b></h5>          
            <input id="fecha" onchange="cambiarDiv(2)" type="text" class="form-control flatpickr flatpickr-input sm-control" placeholder="{{\Carbon\Carbon::now()->format('d-m-Y')}}" autocomplete="off">             
            <br>
            <h5><b>Repartidor</b></h5>           
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
            <div id="div1" class="col-sm-12 col-md-10">
            <!-- style="display:none;"  -->
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
                            @foreach($info as $r)
                            <tr>
                                <td class="text-left">
                                    <input value="{{$r->cantidad}}" id="{{$r->cliente_id}}" class="name" name="checks" type="checkbox" checked>                                                                         
                                </td>
                                <td class="text-left">{{$r->apellido}}, {{$r->nombre}}</td>
                                <td class="text-center">{{$r->cantidad}}</td>
                                <td class="text-center">{{$r->descripcion}}</td>
                                <td class="text-right">{{$r->precio_venta}}</td>
                                <td class="text-right">{{number_format($r->importe,2)}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>                                                                                     
                                            <a href="javascript:void(0);" onclick="openModal('{{$r}}')" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        
                                        <li>
                                            <a href="javascript:void(0);" onclick="Cobrar({{$r->cliente_id}})" data-toggle="tooltip" data-placement="top" title="Cobrar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-dark"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> 
            </div>
            <div id="div2" class="col-sm-12 col-md-6">
            <!-- style="display:block;"  -->
                <div  class="table-resposive scroll">
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
            <div id="div3" class="col-sm-12 col-md-6">
            <!-- style="display:none;"  -->
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
    <input type="hidden" id="vista_facturas" wire:model="vista_facturas">
</div></div>@include('livewire.viandas.modal')</div></div>  


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
    function ConfirmGrabar() {
        if($('#repartidor option:selected').val() == 'Elegir'){
            Swal.fire('Elige una opción válida para el Repartidor')
            return;
        }               
    	let me = this
    	swal({
    		title: 'CONFIRMAR',
    		text: '¿DESEAS ENVIAR A CUENTA CORRIENTE A TODOS LOS REGISTROS?',
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
    function Cobrar(id)
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
            if (result.isConfirmed) {
                    window.livewire.emit('factura_contado', id)
                Swal.fire('Factura Cobrada!', '', 'success')
            } else if (result.isDenied) {
                   window.livewire.emit('factura_ctacte', id)
                Swal.fire('Factura Cuenta Corriente', '', 'success')                 
            }
        })
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
        $('#cantidad').val('')
        $('#producto').val('Elegir')
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
                    Swal.fire('info','Primero debes habilitar alguna Caja')
                    return;
                }else{
                    div1.style.display = 'block';
                    div2.style.display = 'none';
                    div3.style.display = 'none';
                    document.getElementById("btn_imp_viandas").setAttribute("disabled",false);
                    document.getElementById("btn_imp_comentarios").setAttribute("disabled",false);
                    if(document.getElementById("btn_grabar").disabled == true){
                        document.getElementById("btn_grabar").removeAttribute("disabled");
                    }
                }
                break;
            case 2:              //Ver Lista Cocina
                div1.style.display = 'none';
                div2.style.display = 'block';
                div3.style.display = 'none';
                document.getElementById("btn_grabar").setAttribute("disabled",false);
                document.getElementById("btn_imp_comentarios").setAttribute("disabled",false);
                if(document.getElementById("btn_imp_viandas").disabled == true){
                    document.getElementById("btn_imp_viandas").removeAttribute("disabled");
                }
                break;
            case 3:               //Ver Comentarios
                div1.style.display = 'none';
                div2.style.display = 'none';
                div3.style.display = 'block';
                document.getElementById("btn_grabar").setAttribute("disabled",false);
                document.getElementById("btn_imp_viandas").setAttribute("disabled",false);
                if(document.getElementById("btn_imp_comentarios").disabled == true){
                    document.getElementById("btn_imp_comentarios").removeAttribute("disabled");
                }
                break;
            default:
        }
    }             
    window.onload = function() {  
        //if($('#vista_facturas').val() == 'true'){
          //  cambiarDiv(1);
       // }             
        var arr = $('[name="checks"]:checked').map(function(){            
            return this.value;           
        }).get();

        var total =0;
        for(var i of arr) total = parseInt(total) + parseInt(i);    
        $('#cViandas').text(total);
    };  
    $(document).ready(function() {
        $('[name="checks"]').click(function() {            
            var arr = $('[name="checks"]:checked').map(function(){            
                return this.value;           
            }).get();
                            
            var total =0;
            for(var i of arr) total = parseInt(total) + parseInt(i);    
            $('#cViandas').text(total);
        });
    });  
    $(document).ready(function() {
        $('[id="fecha"]').change(function() {
            var data =  $('#fecha').val();         
            $('#fechaConsulta').text(data);
            window.livewire.emit('cambiarFecha', data);
        });
    });
    $(document).ready(function() {
        $('[id="repartidor"]').change(function() {
            if($('[id="repartidor"]').val() == 'Elegir'){
                cambiarDiv(2);
            }
        });
    });

</script>






