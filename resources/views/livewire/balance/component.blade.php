<div class="row layout-top-spacing justify-content-center">
    @include('common.alerts')
	@include('common.messages')
    @if($action == 1)
    <div class="col-sm-12 col-md-8 layout-spacing">
        <div class="widget-content-area ">
            <div class="widget-one">
                <div class="row">
                    <div class="col-8 text-left">
                        <h3><b>Estado de Resultados</b></h3>
                    </div>
                    <div class="col-4 text-right">
                        <button type="button" onclick="salir()" class="btn btn-dark">
                            Volver
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="text-center">
                        <button type="button" wire:click="doAction(2,0)" class="btn btn-outline-primary m-1">
                            Margen de Contribución de Listas Actuales
                        </button>
                        <button type="button" wire:click="doAction(3,0)" class="btn btn-outline-primary m-1">
                            Matriz BCG
                        </button>
                        <button type="button" wire:click="doAction(5,0)" class="btn btn-outline-primary m-1">
                            Punto de Equilibrio
                        </button>
                        <button type="button" onclick="salir()" class="btn btn-outline-primary m-1">
                            Balance
                        </button>
                        <button type="button" wire:click="doAction(5,0)" class="btn btn-outline-primary m-1">
                            Plan de Cuentas
                        </button>
                        <button type="button" wire:click="anularVentas()" class="btn btn-outline-primary m-1">
                            Anular ventas
                        </button>
                    </div>
                </div>
                <div class="row">
                    <p class="col-4 offset-8" style="text-align: right;"><strong>% / Ventas</strong></p>
                    <div class="col-sm-8 col-md-7 text-left">
                        <b>Ventas:</b>
                        <br>
                        <b>Costo de Ventas <i class="bi bi-info-circle ml-2 asterisco"
                            data-toggle="tooltip" data-placement="top"
                            title="Ver datos de la fórmula" onclick="ver_formula_cmv()"></i></b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">Utilidad Bruta (Margen de Contribución)</font>
                        <br>
                        <b>Gastos Operativos <i class="bi bi-info-circle ml-2 asterisco"
                            data-toggle="tooltip" data-placement="top"
                            title="Ver datos de la fórmula" onclick="ver_gastos_operativos()"></i></b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">Utilidad Operacional:</font>
                        <br>
                        {{-- <b>Resultado por Tenencia:</b>
                        <br> --}}
                        <b>Gastos Financieros:</b>
                        <br>
                        <b>Otros Ingresos:</b>
                        <br>
                        <b>Otros Gastos:</b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">Utilidad Neta antes de Impuestos:</font>
                        <br>
                        <b>Impuestos:</b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial"><b>Utilidad Neta:</b></font>
                    </div>
                    <div class="col-sm-2 col-md-3 text-right">
                        <b>{{number_format($ventas,2,',','.')}}</b>
                        <br>
                        <b>({{number_format($cmv,2,',','.')}})</b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">{{number_format($m_c,2,',','.')}}</font>
                        <br>
                        <b>({{number_format($suma_gastos_operativos,2,',','.')}})</b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">{{number_format($util_operativa,2,',','.')}}</font>
                        <br>
                        {{-- <b>{{number_format($r_p_t,2,',','.')}}</b>
                        <br> --}}
                        <b>({{number_format($gastos_financieros,2,',','.')}})</b>
                        <br>
                        <b>{{number_format($ot_ingresos,2,',','.')}}</b>
                        <br>
                        <b>({{number_format($ot_gastos,2,',','.')}})</b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">{{number_format($util_neta_antes_impuestos,2,',','.')}}</font>
                        <br>
                        <b>({{number_format($total_impuestos,2,',','.')}})</b>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">{{number_format($util_neta_antes_impuestos,2,',','.')}}</font>
                    </div>
                    <div class="col-sm-2 col-md-2 text-right">
                        {{-- <div id="espacio"></div> --}}
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  100,00 %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_cmv,2,',','.')}} %</font>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">  {{number_format($p_m_c,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_g_operativos,2,',','.')}} %</font>
                        <br>
                        <font size=4 color="red" face="Comic Sans MS,arial">  {{number_format($p_util_operativa,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_imp,2,',','.')}} %</font>
                        {{-- <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_alq,2,',','.')}} %</font>  --}}
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_gastos_func,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_egresos_varios,2,',','.')}} %</font>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">  {{number_format($p_gan,2,',','.')}} %</font>
                        <br>
                        <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_egresos_varios,2,',','.')}} %</font>
                        <br>
                        <font size=4 color="Red" face="Comic Sans MS,arial">  {{number_format($p_gan,2,',','.')}} %</font>
                        <br>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.balance.modalCostoMercaderiaVendida')
    @include('livewire.balance.modalGastosOperativos')
    @include('livewire.balance.modalDetalleGastosOperativos')
    @elseif($action == 2)
	@include('livewire.balance.margen_de_contribucion')
    @include('livewire.balance.modalActualizarPrecioLista')
    @elseif($action == 3)
    @include('livewire.balance.matriz_bcg')
    @elseif($action == 4)
    @include('livewire.balance.detalle_mpp_por_categoria')
    @include('livewire.balance.modalFormulaPopularidad')
    @include('livewire.balance.modalFormulaMargen')
    @elseif($action == 5)
    @include('livewire.balance.punto_de_equilibrio')
    @include('livewire.balance.modalCostoFijoEstimado')
    @include('livewire.balance.modalAyudaPuntoDeEquilibrio')
    @else
    @include('livewire.balance.detalle_mpp_por_producto')
    @include('livewire.balance.modalFormulaEnPesos')
    @include('livewire.balance.modalFormulaEnPorcentaje')
	@endif
</div>

<style type="text/css" scoped>
   .alinear{
        margin-top: 12px;
        text-align: right;
    }
    .scroll{
        position: relative;
        height: 250px;
        margin-top: .5rem;
        overflow: auto;
    }
    thead tr th {     /* fija la cabecera de la tabla */
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff;
    }
    .margen:hover {
        width: 85px;
        height: 85px;
        cursor:pointer; cursor: hand;
    }
    .verde{
        -moz-border-radius: 3%;
        -webkit-border-radius: 23;
        border-radius: 3%;
        background: rgba(8, 153, 8, 0.973);
        color: #ffffff;
        align-items: center;
        font-size: 15px;
    }
    .verde-claro {
        -moz-border-radius: 2%;
        -webkit-border-radius: 2%;
        border-radius: 2%;
        background: rgb(74, 212, 74);
        color: #ffffff;
        align-items: center;
        font-size: 15px;
    }
    .naranja {
        -moz-border-radius: 2%;
        -webkit-border-radius: 2%;
        border-radius: 2%;
        background: rgb(253, 167, 8);
        color: #ffffff;
        align-items: center;
        font-size: 15px;
    }
    .rojo {
        -moz-border-radius: 2%;
        -webkit-border-radius: 2%;
        border-radius: 2%;
        background: rgb(248, 7, 7);
        color: #ffffff;
        align-items: center;
        font-size: 15px;
    }
    .asterisco {
		color: red;
		cursor: pointer;
		font-weight: bold;
	}
</style>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    function salir()
    {
        window.location.href="{{ url('home') }}";
    }
    function openModal(id, producto, precioSugeridoL1, precioLista1, precioSugeridoL2, precioLista2)
    {
        $('#productoId').val(id)
        $('#producto').val(producto)
        $('#precio_sugerido_l1').val(precioSugeridoL1)
        $('#precio_venta_l1').val(precioLista1)
        $('#precio_sugerido_l2').val(precioSugeridoL2)
        $('#precio_venta_l2').val(precioLista2)
        $('#modalActualizarPrecioLista').modal('show')
	}
    function guardarPrecio()
    {
        if($('#precio_venta_l1').val() == '') {
            toastr.error('Ingresa un importe para el Precio de Venta de Lista 1')
            return;
        }
        if($('#precio_venta_l2').val() == '') {
            toastr.error('Ingresa un importe para el Precio de Venta de Lista 2')
            return;
        }
        var data = JSON.stringify({
            'id'     : $('#productoId').val(),
            'precio_l1' : $('#precio_venta_l1').val(),
            'precio_l2' : $('#precio_venta_l2').val()
        });
        $('#modalActualizarPrecioLista').modal('hide');
        window.livewire.emit('actualizarPrecioLista', data);
    }
    function ayudaPE()
    {
        $('#modalAyudaPuntoDeEquilibrio').modal('show');
    }
    function ver_formula_en_pesos()
    {
        var dividendo = Number.parseFloat($('#total_dividendo').val()).toFixed(2).replace(".", ",");
        var divisor = Number.parseFloat($('#total_divisor').val()).toFixed(2).replace(".", ",");
        var resultado = Number.parseFloat($('#total_resultado').val()).toFixed(2).replace(".", ",");
        $('#dividendo').val(dividendo)
        $('#divisor').val(divisor)
        $('#resultado').val(resultado)
        $('#modalFormulaEnPesos').modal('show');
    }
    function ver_formula_en_porcentaje()
    {
        var dividendo = Number.parseFloat($('#total_dividendo').val()).toFixed(2).replace(".", ",");
        var divisor = Number.parseFloat($('#total_divisor_porcentaje').val()).toFixed(2).replace(".", ",");
        var resultado = Number.parseFloat($('#total_resultado_porcentaje').val()).toFixed(2).replace(".", ",") + " %";
        $('#dividendo_porcentaje').val(dividendo)
        $('#divisor_porcentaje').val(divisor)
        $('#resultado_porcentaje').val(resultado)
        $('#modalFormulaEnPorcentaje').modal('show');
    }
    function ver_formula_popularidad()
    {
        var cantidad_productos = Number.parseFloat($('#cantidadProductos').val()).toFixed(2).replace(".", ",");
        var mix_ideal = Number.parseFloat($('#mixIdeal').val()).toFixed(2).replace(".", ",") + ' %';
        var mix_ideal_corregido = Number.parseFloat($('#mixIdealCorregido').val()).toFixed(2).replace(".", ",") + ' %';
        $('#cantidad_productos').val(cantidad_productos)
        $('#mix_ideal').val(mix_ideal)
        $('#mix_ideal_corregido').val(mix_ideal_corregido)
        $('#modalFormulaPopularidad').modal('show');
    }
    function ver_formula_cmv()
    {
        $('#modalCostoMercaderiaVendida').modal('show');
    }
    function ver_gastos_operativos()
    {
        $('#modalGastosOperativos').modal('show');
    }
    function ver_detalle_gastos_op(id,descripcion) {
        $('#modalGastosOperativos').modal('hide');
        window.livewire.emit('detalle_egresos',id, descripcion);
    }
    function ver_detalle_gastos_operativos(data)
    {
        var data = data;
        $('.modal-title2').text('Detalle de ' + data);
        $('#modalDetalleGastosOperativos').modal('show');
    }
    function ver_formula_margen()
    {
        var total_mcpp = Number.parseFloat($('#totalMcpp').val()).toFixed(2).replace(".", ",") + ' %';
        $('#total_mcpp').val(total_mcpp);
        $('#modalFormulaMargen').modal('show');
    }
    function OcultarButton(btn)
    {
        if(btn == 1){
            $('#btn_add').fadeOut();
            $('#btn_edit').fadeIn();
        }else{
            $('#btn_edit').fadeOut();
            $('#btn_add').fadeIn();
        }
        $('#desc_cf').trigger("focus")
    }
    function edit(id, descripcion, importe)
    {
        $('#id_cf').val(id);
        $('#desc_cf').val(descripcion);
        $('#importe_cf').val(importe);
        OcultarButton(1);
    }
    function ver_costos_fijos_estimados()
    {
        $('#id_cf').val('')
        $('#desc_cf').val('')
        $('#importe_cf').val('')
        OcultarButton(0)
        $('#modalCostoFijoEstimado').modal('show')

    }
    function guardar_costos_fijos()
    {
        if($('#desc_cf').val() == '' || $('#importe_cf').val() == ''){
            $('#desc_cf').trigger("focus");
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Incompleto!',
                text: 'Falta información...',
                showConfirmButton: false,
                timer: 1500
            });
        }else{
            var data = JSON.stringify({
                'id'          : $('#id_cf').val(),
                'descripcion' : $('#desc_cf').val(),
                'importe'     : $('#importe_cf').val()
            });
            $('#modalCostoFijoEstimado').modal('hide');
            window.livewire.emit('guardarCostosFijosEstimados', data);
            calcular_total_a_cubrir_estimado();
        }
    }
    function cancelar()
    {
        $('#id_cf').val('')
        $('#desc_cf').val('')
        $('#importe_cf').val('')
        $('#desc_cf').trigger("focus")
        OcultarButton(0)
    }
    function eliminar(id)
    {
        if(id){
            Swal.fire({
                title: 'Desea eliminar el registro seleccionado?',
                text: "No podrá deshacer esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modalCostoFijoEstimado').modal('hide');
                    window.livewire.emit('eliminarCostoFijoEstimado', id)
                }
            })
        }
    }
    function doAction(action,id)
    {
        window.livewire.emit('doAction',action,id);
    }
    function calcular_total_a_cubrir()
    {
        var deudas = $('#deudas').val()
        var ganancia_deseada = $('#ganancia_deseada').val()
        window.livewire.emit('calcularTotalACubrir', deudas, ganancia_deseada)
    }
    function calcular_total_a_cubrir_estimado()
    {
        var deudas = $('#deudas_estimadas').val()
        var ganancia_deseada = $('#ganancia_deseada_estimada').val()
        window.livewire.emit('calcularTotalACubrirEstimado', deudas, ganancia_deseada)
    }

    window.onload = function() {
        OcultarButton(0);
        Livewire.on('actualizarPrecio',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Precio se actualizó correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('precio_inexistente',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Hay Productos cuyo Precio de Costo o Precio de Venta es cero...',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('agregarCF',(info)=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Costo Fijo ' + info + ' correctamente!!',
                showConfirmButton: false,
                timer: 1000
            });
            ver_costos_fijos_estimados();
        })
        Livewire.on('registroEliminado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro Eliminado!',
                text: 'Tu registro se eliminó correctamente...',
                showConfirmButton: false,
                timer: 1000
            });
            ver_costos_fijos_estimados();
        })
        Livewire.on('verDetalleGastosOperativos',(data)=>{
            ver_detalle_gastos_operativos(data);
        })
        Livewire.on('hola',()=>{
            alert('Ventas eliminadas');
        })

    }
</script>


