<div class="row layout-top-spacing justify-content-center">    
<div class="col-sm-6 md-12">    
@include('common.alerts')  
@include('common.messages')  
<div class="widget-content-area">
    <div class="widget-one">
        <h3 class="text-center mb-4"><b>ARQUEO GENERAL</b></h3>
        <!-- <hr> -->
        <div class="row">
            <div class="col-6 ">
                <div class="color-box mb-1">
                    <span onclick="openModal(1)" class="cl-example text-center" style="background-color: #394BA1; font-size: 3rem;color: white;cursor:pointer">#</span>
                    <div class="cl-info">
                        <h1 class="cl-title">Caja Inicial</h1>
                        <span>$ {{number_format($cajaInicial,2,',','.')}}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 ">
                <div>
                @if($caja_abierta == 0)
                    <button onclick="cerrarArqueoGral()" style="height: 80px;" class="btn btn-danger btn-block" enabled>TERMINAR ARQUEO GENERAL</button> 
                @elseif($caja_abierta == 1)
                    <button onclick="cerrarCajaGral()" style="height: 80px;" class="btn btn-danger btn-block" disabled>
                        <b>¡ATENCIÓN!...</b><br>EXISTEN CAJAS ABIERTAS</button> 
                @else
                    <button onclick="cerrarCajaGral()" style="height: 80px;" class="btn btn-danger btn-block" disabled>TERMINAR ARQUEO GENERAL</button> 
                @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="color-box mb-1">
                    <span onclick="openModal(2)" class="cl-example text-center" style="background-color: #8dbf42; font-size: 3rem;color: white;cursor:pointer">+</span>
                    <div class="cl-info">
                        <h1 class="cl-title">Ingresos</h1>
                        <span>$ {{number_format($totalIngresos,2,',','.')}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="color-box mb-1">
                    <span onclick="openModal(5)" class="cl-example text-center" style="background-color: #F2351F; font-size: 3rem;color: white;cursor:pointer">-</span>
                    <div class="cl-info">
                        <h1 class="cl-title">Egresos</h1>
                        <span>$ {{number_format($egresos,2,',','.')}}</span>
                    </div>
                </div>   
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="color-box">
                    <span onclick="openModal(6)" class="cl-example text-center" style="background-color: #394BA1; font-size: 3rem;color: white;cursor:pointer">$</span>
                    <div class="cl-info">
                        <div class="row">
                            <div class="col-6">
                                <h1 class="cl-title">Caja Final Según Sistema</h1>
                            </div>
                            <div class="col-6 text-right">
                                <span>$ {{number_format($cajaFinal,2,',','.')}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h1 class="cl-title">Caja Final Según Usuarios</h1>
                            </div>
                            <div class="col-6 text-right">
                                <span><u>$ {{number_format($cajaFinalUsuarios,2,',','.')}}</u></span>
                            </div>
                        </div>
                        <div class="row" >
                            @if($diferencia >= 0)
                                <div class="col-6">
                                    <h1 class="cl-title" style="color: #8dbf42;">Diferencia</h1>
                                </div>
                                <div class="col-6 text-right">
                                    <span style="color: #8dbf42;">$ {{number_format($diferencia,2,',','.')}}</span>
                                </div>
                            @else
                                <div class="col-6">
                                    <h1 class="cl-title" style="color: #F2351F;">Diferencia</h1>
                                </div>
                                <div class="col-6 text-right">
                                    <span style="color: #F2351F;">$ {{number_format($diferencia,2,',','.')}}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="usuario_habilitado" wire:model="usuario_habilitado">  
        </div>
    </div>    
</div>
@include('livewire.arqueo_general.modal')
    <input type="hidden" id="id" value="0">	
@can('ArqueoDeCajaDeOtros_index')
    <input type="hidden" id="verArqueoDeOtros" value="1">	
    <input id="user" wire:model="user">
    <input id="user" wire:model="nro_arqueo">
@else
    <input type="hidden" id="verArqueoDeOtros" value="0">
@endif	
</div>
</div>

<style type="text/css" scoped>
.scrollmodal{
    position: relative;
    height: 270px;
    margin-top: .5rem;
    overflow: auto;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">   
    function cerrarArqueoGral()
    {
        Swal.fire({
            title: 'CONFIRMAR',
            text: 'Deseas hacer el Arqueo General? No podrás deshacer esta acción...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    cajaChica()
                    
                }
            }else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelado',
                    'El cierre no se concretó...',
                    'error'
                )
            }
        })
	}	
    function cajaChica()
    {
        Swal.fire({
                title: 'ANTES DE IRTE A DESCANSAR...',
                text: 'Ingresa el importe que dejas para la próxima Caja Diaria',
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Ingresa el Monto...',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false,
                inputValidator: proximaCajaChica => {
				// Si el valor es válido, debes regresar undefined. Si no, una cadena
				if (!proximaCajaChica) {
					return "Debes ingresar el Monto que corresponde a la próxima Caja Diaria...";
				} else {
					return undefined;
				}
			}
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        let proximaCajaChica = result.value;
                        var dato_a_comprobar = proximaCajaChica;
                        var valoresAceptados = /^[0-9]+$/;
                            if (dato_a_comprobar.match(valoresAceptados)){
                                window.livewire.emit('cerrarArqueoGral',proximaCajaChica)
                                // window.livewire.emit('cerrarCaja',proximaCajaChica)
                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: 'Ingresa solo números!!',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            }
                    }
                }else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'El cierre no se concretó...',
                        'error'
                    )
                }
            })
    }
    function edit(row)
    {
        var info = JSON.parse(row)
        $('#id').val(info.id)
        $('#importe').val(info.importe)
        $('.modal-title').text('Editar Caja')
    }
    function openModal(id)
    {
        if(id == 1){
            $('#modalCajaInicial').show()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()           
            $('#modalCajaFinal').hide()           
            $('.modal-title').text('Cajas Iniciales')
        }else if(id == 2){
            $('#modalCajaInicial').hide()
            $('#modalVentas').show()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide() 
            $('.modal-title').text('Ingresos Totales por Caja')
        }else if(id == 3){
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').show()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide() 
            $('.modal-title').text('Listado de Cobros de Cuenta Corriente')
        }else if(id == 4){
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').show()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide() 
            $('.modal-title').text('Listado de Otros Ingresos')
        }else if(id == 5){
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').show()
            $('#modalCajaFinal').hide() 
            $('.modal-title').text('Listado de Egresos')
        }else{
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').show() 
            $('.modal-title').text('Cajas Finales')
        }
        $('#modalCajaRep').modal('show')
    }
    window.onload = function(){
        if($('#usuario_habilitado').val() == 0){
            swal({
                title: 'Oops!',
                text: 'Hoy no estás autorizado para realizar el Arqueo General...',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('notify') }}";
                swal.close()   
            })
        };
		Livewire.on('arqueoCerrado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Caja Cerrada!!',
                text:'El Cierre se efectuó correctamente...',
                showConfirmButton: false,
                timer: 1500
            })
            window.location.href="{{ route('home') }}";
		})
        Livewire.on('usuarioNoAutorizado',()=>{   //no funciona
            Swal.fire(
                'Oops!',
                'Hoy no estás autorizado para realizar el Arqueo General...',
                'success'
            ).then((result) => {
                if (result.isConfirmed) {
                    window.location.href="{{ route('home') }}";
                }
            });
		})
    }
</script>
