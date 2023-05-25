<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
	@include('common.messages')
    @if($action == 1) 
    <div class="col-sm-12 col-md-6 layout-spacing"> 
        <div class="widget-content-area ">
            <div class="widget-one">  
                <div class="row">
                    <div class="col-4 text-center">
                        <h3><b>Balance</b></h3>
                    </div>
                    <div class="col-8 text-center">
                        <!-- <button type="button" wire:click="grabarEI()"  class="btn btn-dark mr-1">
                            Grabar Existencia Inicial
                        </button> -->
                        <button type="button" wire:click="doAction(2)"  class="btn btn-dark mr-1">
                            Margen de Contribución
                        </button>
                        <button type="button" onclick="salir()"  class="btn btn-dark mr-1">
                            Volver
                        </button>
                    </div> 
                </div>
                <hr/>
                <!-- <div class="row">
                    <div class="col-sm-8 col-md-7 text-left">
                        <b>Existencia Inicial:</b>
                        <br>
                        <b>Compras de Mercadería:</b>
                        <br>
                        <b>Existencia Final:</b>
                        <br>
                        <font size=4 color="Olive" face="Comic Sans MS,arial">Costo de la Mercadería Vendida:</font>
                    </div>
                    <div class="col-sm-2 col-md-3 text-right">
                        <b>{{number_format($e_i,2,',','.')}}</b> 
                        <br>
                        <b>{{number_format($compras,2,',','.')}}</b> 
                        <br>
                        <b>({{number_format($e_f,2,',','.')}})</b> 
                        <br>
                        <font size=4 color="Olive" face="Comic Sans MS,arial">{{number_format($cmv,2,',','.')}}</font>
                    </div>                     
                </div>                     
                <hr/>
                <div class="row">    
                    <div class="col-sm-8 col-md-7 text-left">
                        <b>Ingresos del Mes:</b>
                        <br>
                        <b>Costo de la Mercadería Vendida:</b>
                        <br>
                        <b>Empleados:</b>
                        <br>
                        <b>Servicios:</b>
                        <br>
                        <b>Impuestos:</b>
                        <br>
                        <b>Alquileres:</b>
                        <br>
                        <b>Gastos de Funcionamiento:</b>
                        <br>
                        <b>Egresos Varios:</b>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">Resultado:</font>                
                    </div>
                    <div class="col-sm-2 col-md-3 text-right">
                        <b>{{number_format($ventas,2,',','.')}}</b>
                        <br>
                        <b>({{number_format($cmv,2,',','.')}})</b> 
                        <br>
                        <b>({{number_format($empleados,2,',','.')}})</b> 
                        <br>
                        <b>({{number_format($servicios,2,',','.')}})</b>
                        <br>
                        <b>({{number_format($impuestos,2,',','.')}})</b> 
                        <br>
                        <b>({{number_format($alquileres,2,',','.')}})</b> 
                        <br>
                        <b>({{number_format($gastosDeFuncionamiento,2,',','.')}})</b>
                        <br>
                        <b>({{number_format($egresosVarios,2,',','.')}})</b>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">{{number_format($ganancia,2,',','.')}}</font>
                    </div>
                    <div class="col-sm-2 col-md-2 text-right">
                        <div id="espacio"></div>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_cmv,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_emp,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_serv,2,',','.')}} %</font>
                        <br> 
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_imp,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_alq,2,',','.')}} %</font> 
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_gastos_func,2,',','.')}} %</font>
                        <br> 
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_egresos_varios,2,',','.')}} %</font>
                        <br> 
                        <font size=2 color="Red" face="Comic Sans MS,arial">  {{number_format($p_gan,2,',','.')}} %</font>
                        <br> 
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-8 col-md-7 text-left">
                        <b>Ventas:</b>
                        <br>
                        <b>Costos Fijos:</b>
                        <br>
                        <b>Costos Variables:</b>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">Ventas en el Punto de Equilibrio:</font>
                    </div>
                    <div class="col-sm-2 col-md-3 text-right">
                        <b>{{number_format($ventas,2,',','.')}}</b> 
                        <br>
                        <b>{{number_format($cFijos,2,',','.')}}</b> 
                        <br>
                        <b>{{number_format($cVariables,2,',','.')}}</b> 
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">{{number_format($ventasPEq,2,',','.')}}</font>
                    </div>
                    <div class="col-sm-2 col-md-2 text-right">
                        <div id="espacio_p_eq"></div>
                        <font size=2 color="Olive" face="Comic Sans MS,arial"> {{number_format($p_cF,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_cV,2,',','.')}} %</font>
                    </div>                    
                </div>
                <hr/>
                <div class="row">                    
                    <div class="col-sm-8 col-md-7 text-left">
                        <b>Ingresos del Mes:</b>
                        <br>
                        <b>Costo de la Mercadería Vendida:</b>
                        <br>
                        <font size=4 color="Olive" face="Comic Sans MS,arial">Margen de Contribución:</font>
                        <br>
                        <b>Gastos Operativos:</b>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">Utilidad Operativa:</font>
                        <br>
                    </div>
                    <div class="col-sm-2 col-md-3 text-right">
                        <b>{{number_format($ventas,2,',','.')}}</b> 
                        <br>
                        <b>({{number_format($cmv,2,',','.')}})</b> 
                        <br>
                        <font size=3 color="Olive" face="Comic Sans MS,arial">{{number_format($m_c,2,',','.')}}</font> 
                        <br>
                        <b>(129.802,00)</b> 
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">75.619,00</font> 
                    </div>
                    <div class="col-sm-2 col-md-2 text-right">
                        <div id="espacio_utilidad"></div>
                        <font size=2 color="Olive" face="Comic Sans MS,arial"> {{number_format($p_m_c,2,',','.')}} %</font>
                    </div> 
                </div>
                <hr/> -->
            </div>
        </div>
    </div>
    @else
	@include('livewire.balance.margen_de_contribucion')	
    @include('livewire.balance.modalActualizarPrecioLista')	
	@endif
</div>

<style type="text/css" scoped>
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
        background-color: #ffffff;
    }
</style>

<script type="text/javascript">
    function salir()
    {
        window.location.href="{{ url('home') }}";
    }
    function openModal(id, producto, precioSugerido, precioLista)
    {       
        $('#productoId').val(id)
        $('#producto').val(producto)
        $('#precio_sugerido_l1').val(precioSugerido)
        $('#precio_venta_l1').val(precioLista)
        $('#modalActualizarPrecioLista').modal('show')
	}
    function guardarPrecio()
    {      
        if($('#precio_venta_l1').val() == '') {
            toastr.error('Ingresa un importe para el Precio de Venta')
            return;
        }
        var data = JSON.stringify({
            'id'     : $('#productoId').val(),
            'precio' : $('#precio_venta_l1').val()
        });
        $('#modalActualizarPrecioLista').modal('hide');
        window.livewire.emit('actualizarPrecioLista', data);
    }


    document.getElementById("espacio").style.height = "22px";
    document.getElementById("espacio_p_eq").style.height = "20px";
    document.getElementById("espacio_utilidad").style.height = "43px";
 

    window.onload = function() {
        Livewire.on('actualizarPrecio',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Precio se actualizó correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
    }
</script>


 