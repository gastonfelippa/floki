<div class="col-sm-12 col-md-6 layout-spacing">
	<div class="widget-content-area">
        <div class="widget-one">
            <h5><b>@if($selected_id ==0) Nuevo Ingreso  @else Editar Ingreso @endif</b></h5>
            <div class="row mt-3">                               
                <div class="col-12 layout-spacing">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                        </div>
                        <input id="nombre" name="nombre" type="text" class="form-control text-uppercase" placeholder="Descripción" wire:model="descripcion" autofocus autocomplete="off">
                    </div>
                </div>
            </div>              
			@include('common.btnCancelarGuardar')          
        </div>
    </div>	
</div>

