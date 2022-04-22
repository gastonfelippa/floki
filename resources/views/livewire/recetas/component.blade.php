<div class="row layout-top-spacing justify-content-center"> 
	@include('common.alerts')
    <div class="col-sm-12 col-md-6 layout-spacing">      
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-9">
                        <h3><b>Receta de {{$prod_receta}}</b></h3>
                    </div> 
                    <div class="col-3 text-right">
                    <button type="button" onclick="volver()" class="btn btn-dark">
                        <i class="mbri-left"></i> Volver
                    </button>
                </div> 
    			</div> 
                @include('common.messages')
                <div class="row mt-2">                    
                    <div class="form-group col-2">
                        <label>Cantidad</label>
                        <input id="cantidad" wire:model.lazy="cantidad" type="text" 
                            class="form-control form-control-sm text-center">
                    </div>

                    <div class="form-group col-3">
                        <label >U. Medida</label>
                        <select wire:model="unidad" class="form-control text-left">
                            <option value="Elegir">Elegir</option>
                            <option value="Un">Un</option>
                            <option value="Kg">Kg</option>
                            <option value="Lt">Lt</option>
                            <option value="Mt">Mt</option>
                        </select>		
                    </div>
                    <div class="form-group col-7">
                        <label >Producto</label>
                        <select wire:model="producto" class="form-control text-left">
                            <option value="Elegir">Elegir</option>
                            @foreach($productos as $t)
                            <option value="{{ $t->id }}">
                                {{$t->descripcion}}                         
                            </option> 
                            @endforeach                         
                        </select>		
                    </div>
                </div>

                <div class="table-responsive scroll">
                    <table class="table table-hover table-checkable table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-center">UNIDAD</th>
                                <th class="text-left">DESCRIPCIÓN</th>
                                <th class="text-center">COSTO</th>
                                <th class="text-center">IMPORTE</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr class="">
                                <td class="text-center">{{number_format($r->cantidad,3,',','.')}}</td>
                                <td class="text-center">{{$r->unidad_de_medida}}</td>
                                <td class="text-left">{{$r->descripcion}}</td>
                                <td class="text-right">{{number_format($r->precio_costo,2,',','.')}}</td>
                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" wire:click="edit({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="Confirm({{$r->id}})"
                                            data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>                   
                </div>  

                <div class="row mt-3">
                    <div class="col-6">
                        <button type="button" wire:click="resetInput" onclick="setfocus('cantidad')" class="btn btn-dark mr-1">
                            <i class="mbri-left"></i> Cancelar
                        </button>
                        <button type="button" 
                            @if($cantidad) enabled @else disabled @endif
                            wire:click="StoreOrUpdate()"  
                            @if($selected_id) class="btn bg-warning" id="btnModificar"
                            @else class="btn bg-primary" id="btnGuardar" 
                            @endif>
                            @if($selected_id) <span style="text-decoration: underline;">M</span>odificar 
                            @else <span style="text-decoration: underline;">G</span>uardar 
                            @endif
                        </button> 
                    </div>
                    <div class="col-6 text-center">
                        <h4 class="bg-danger" style="border-radius: 5px;">Costo Final : $ {{number_format($total,2,',','.')}}</h4> 
                    </div> 
                </div>
            </div>
    	</div> 
    </div>
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 170px;
    margin-top: .3rem;
    overflow: auto;
}
</style>

<script type="text/javascript">
    function Confirm(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Eliminar el registro, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
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
					window.livewire.emit('deleteRow', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }
    function volver()
    {
        window.location.href="{{url('productos')}}";
    }
    window.onkeydown = PulsarTecla;
	function PulsarTecla(e)
    {
        tecla = e.keyCode;
        if(e.altKey == 1 && tecla == 77) document.getElementById("btnModificar").click();
        else if(e.altKey == 1 && tecla == 71) document.getElementById("btnGuardar").click();
        else if(tecla == 27) document.getElementById("btnCancelar").click();
    }

    window.onload = function() {
        $(document).ready(function() {
            document.getElementById("cantidad").focus();
        });
    }
   
    function setfocus($id) {
        document.getElementById($id).focus();
    }

</script>