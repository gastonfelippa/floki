<div class="row layout-top-spacing">
<div class="col-sm-12">
@include('common.alerts')
@include('common.messages')
<div class="widget-content-area">
<div class="widget-one">
    <h3 class="text-center"><b>Arqueo de Caja</b></h3>
    @if($usuario_habilitado == 1)
        @can('HabilitarCaja_index')
        <div class="row">
            <!-- <div class="col-sm-12 col-md-2 col-lg-2">
                Elige Fecha
                <div class="form-group">
                    <input wire:model.lazy="fecha" type="text" class="form-control flatpickr flatpickr-input active"
                    placeholder="{{\Carbon\Carbon::now()->format('d-m-Y')}}">
                </div>
            </div> -->
            
            <div class="col-sm-12 col md-3 col-lg-3">
                <div class="form-group">
                    <select wire:model="user" class="form-control">
                        <option value="0">Elige Operador</option>
                        @foreach($users as $u)
                            <option value="{{$u->id}}">{{$u->apellido}} {{$u->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                @if($user > 0)
                    @if($factPendiente == 1)
                    <button onclick="cerrarCaja(1,{{$repartidor}})" class="btn btn-danger btn-block mb-2">Existen Facturas Abiertas...</button>
                    @elseif($factPendiente == 2)
                    <button onclick="cerrarCaja(2,{{$repartidor}})" class="btn btn-danger btn-block mb-2">Existen Facturas Pendientes...</button>
                    @else
                    <button onclick="cerrarCaja(0,{{$repartidor}})" class="btn btn-dark btn-block mb-2">Cerrar Caja</button>
                    @endif
                @endif
            </div>
        </div>
        @else
        <div class="row mb-4">
            <div class="col-12">
            @if($caja_abierta > 0)
            <h6><b>Fecha/Hora Inicio: </b> {{$fecha_inicio->format('d-m-Y')}} - {{$fecha_inicio->format('H:i')}} hs.</h6>
            @endif
            </div>
        </div>
        @endcan
    @else
        <hr>
    @endif
    <div class="row">
        <div class="col-sm-4 col-md-4 col-lg-3 layout-spacing">
            <div class="color-box">
                <span onclick="openModal(1)" class="cl-example text-center" style="background-color: #394BA1; font-size: 3rem;color: white;cursor:pointer">#</span>
                <div class="cl-info">
                    <h1 class="cl-title">Caja Inicial</h1>
                    <span>$ {{number_format($cajaInicial,2,',','.')}}</span>
                    <!-- <span>Efectivo<br>$ {{number_format($cajaInicial,2,',','.')}}</span> -->
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-3 layout-spacing">
            <div class="color-box mb-1">
                <span onclick="openModal(2)" class="cl-example text-center" style="background-color: #8dbf42; font-size: 3rem;color: white;cursor:pointer">+</span>
                <div class="cl-info">
                    <h1 class="cl-title">Ventas Contado</h1>
                    <span>$ {{number_format($ventas,2,',','.')}}</span>
                </div>
            </div>
            <div class="color-box mb-1">
                <span onclick="openModal(3)" class="cl-example text-center" style="background-color: #8dbf42; font-size: 3rem;color: white;cursor:pointer">+</span>
                <div class="cl-info">
                    <h1 class="cl-title">Cobros Cta Cte</h1>
                    <span>$ {{number_format($cobrosCtaCte,2,',','.')}}</span>
                </div>
            </div>
            <div class="color-box">
                <span onclick="openModal(4)" class="cl-example text-center" style="background-color: #8dbf42; font-size: 3rem;color: white;cursor:pointer">+</span>
                <div class="cl-info">
                    <h1 class="cl-title">Otros Ingresos</h1>
                    <span>$ {{number_format($otrosIngresos,2,',','.')}}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-3 layout-spacing">
            <div class="color-box">
                <span onclick="openModal(5)" class="cl-example text-center" style="background-color: #F2351F; font-size: 3rem;color: white;cursor:pointer">-</span>
                <div class="cl-info">
                    <h1 class="cl-title">Egresos</h1>
                    <span>$ {{number_format($egresos,2,',','.')}}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-3 layout-spacing">
            <div class="color-box">
                <span onclick="openModal(6)" class="cl-example text-center" style="background-color: #394BA1; font-size: 3rem;color: white;cursor:pointer">#</span>
                <div class="cl-info">
                    <h1 class="cl-title">Caja Final</h1>
                       <span>$ {{number_format($cajaFinal,2,',','.')}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@include('livewire.cortes.modal')
    <input type="hidden" id="caja_abierta" wire:model="caja_abierta">
    <input type="hidden" id="usuario_habilitado" wire:model="usuario_habilitado">
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
    function cerrarCaja(factPendiente,repartidor)
    {
        if(factPendiente == 1){
            window.location = "{{url('facturas')}}";
        }else if(factPendiente == 2){    
            if(repartidor == true)
            window.location = "{{url('cajarepartidor')}}";
            else
            window.location = "{{url('facturasacobrar')}}";
        }else{
            Swal.fire({
                title: 'CONFIRMAR',
                text: 'Deseas cerrar esta Caja? No podr??s deshacer esta acci??n...',
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Ingresa el Monto que contaste...',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false,
                inputValidator: cajaFinalSegunUsuario => {
				// Si el valor es v??lido, debes regresar undefined. Si no, una cadena
				if (!cajaFinalSegunUsuario) {
					return "Deb??s ingresar el Monto que contaste...";
				} else {
					return undefined;
				}
			}
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        let cajaFinalSegunUsuario = result.value;

                        var dato_a_comprobar = cajaFinalSegunUsuario;
                        var valoresAceptados = /^[0-9]+$/;
                            if (dato_a_comprobar.match(valoresAceptados)){
                                window.livewire.emit('cerrarCaja',cajaFinalSegunUsuario)
                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: 'Ingresa solo n??meros!!',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            }
                    }
                }else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'El cierre no se concret??...',
                        'error'
                    )
                }
            })
        }
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
            $('#id').show()
            $('#importe').show()
            $('#labelImporte').show()
            $('#btnGuardar').show()
            $('#id').val(0)
            $('#importe').val('')
            $('#modalCajaInicial').show()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide()
            $('.modal-title').text('Caja Inicial')
        }else if(id == 2){
            $('#id').hide()
            $('#importe').hide()
            $('#labelImporte').hide()
            $('#btnGuardar').hide()
            $('#modalCajaInicial').hide()
            $('#modalVentas').show()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide()
            $('.modal-title').text('Listado de Ventas Diarias')
        }else if(id == 3){
            $('#id').hide()
            $('#importe').hide()
            $('#labelImporte').hide()
            $('#btnGuardar').hide()
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').show()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide()
            $('.modal-title').text('Listado de Cobros de Cuenta Corriente')
        }else if(id == 4){
            $('#id').hide()
            $('#importe').hide()
            $('#labelImporte').hide()
            $('#btnGuardar').hide()
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').show()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').hide()
            $('.modal-title').text('Listado de Otros Ingresos')
        }else if(id == 5){
            $('#id').hide()
            $('#importe').hide()
            $('#labelImporte').hide()
            $('#btnGuardar').hide()
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').show()
            $('#modalCajaFinal').hide()
            $('.modal-title').text('Listado de Egresos')
        }else{
            $('#id').hide()
            $('#importe').hide()
            $('#labelImporte').hide()
            $('#btnGuardar').hide()
            $('#modalCajaInicial').hide()
            $('#modalVentas').hide()
            $('#modalCobros').hide()
            $('#modalIngresos').hide()
            $('#modalEgresos').hide()
            $('#modalCajaFinal').show()
            $('.modal-title').text('Caja Final')
        }
        $('#modalCajaRep').modal('show')
    }
    window.onload = function() {
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
        Livewire.on('cajaCerrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Caja Cerrada!!',
                text:'El Cierre se efectu?? correctamente...',
                showConfirmButton: false,
                timer: 1500
            })
		})
    }
</script>
