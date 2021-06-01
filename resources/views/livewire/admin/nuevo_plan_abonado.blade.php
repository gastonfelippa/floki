<div class="row layout-top-spacing">
    <div class="col-sm-12 mb-3"> 
        <div class="widget-content-area">
            <div class="widget-one">  
                <div class="row">
    				<div class="col-8 text-right">
                        <h3><b>Renovaciones de Planes</b></h3>
                    </div>
                    <div class="col-4 text-right">
                        <button type="button" wire:click="doAction(1,1)" class="btn btn-sm btn-dark">
                                REGRESAR
                        </button>                      
    				</div> 
    			</div> 
    		</div> 
    	</div> 
    </div>      
    <div class="col-sm-12 col-md-4 layout-spacing"> 
        <div class="widget-content-area">
            <div class="widget-one">
    			<div class="col-xl-12 text-center">
    				<h4><b>Plan Vigente</b></h4>
    			</div>            
                <div class="col-sm-12">
                    <b>Abonado:</b> {{$nombre}}
                    <br>
                    <p><b>Comercio:</b> {{$nombreComercio}} </p>  
                    <b>Plan Actual:</b> {{$planActual}}
                    <br>
                    <b>Estado Plan:</b> {{$estadoPlan}}
                    <br>
                    @if($estadoPago == 'en mora')
                        <p style="color: #FFB533;"><b>Estado Pago: {{$estadoPago}}</b></p>
                    @elseif($estadoPago == 'impago')
                        <p style="color: red;"><b>Estado Pago: {{$estadoPago}}</b></p>
                    @else
                        <p><b>Estado Pago:</b> {{$estadoPago}}</p>
                    @endif
                    <b>Fecha Inicio:</b> {{\Carbon\Carbon::parse($fechaInicio)->format('d-m-Y')}}
                    <br>
                    <b>Fecha Fin:</b> {{\Carbon\Carbon::parse($fechaFin)->format('d-m-Y')}}
                    <br>
                    <b>Fecha Vencimiento:</b> {{\Carbon\Carbon::parse($fechaVto)->format('d-m-Y')}}
                </div>
                <div class="text-center mt-2">
                @if($estadoPago == 'no corresponde' || $estadoPago == 'pagado')
                    <button type="button" class="btn btn-sm btn-warning"
                    onclick="ConfirmCobro()" disabled>
                    COBRAR
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-warning"
                    onclick="ConfirmCobro()" enabled>
                    COBRAR
                    </button>
                @endif
                </div>                
            </div>
        </div>
    </div> 

    <div class="col-sm-12 col-md-8 layout-spacing">  
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-sm-4 text-right">
                        <h4><b>Nuevo Plan</b></h4>
                    </div> 
                    @include('common.messages')
                    @include('common.alerts')
                    <div class="col-sm-8">
                        <select wire:model="plan" class="form-control form-control-sm col-md-6 col-sm-12">
                            <option value="Elegir">Elegir</option>
                            @foreach($planes as $p)
                            <option value="{{ $p->id }}">
                                {{$p->descripcion}}                         
                            </option> 
                             @endforeach                               
                        </select> 
                    </div>
                </div>
                <div class="row">                  
                    <div class="col-sm-4">
                        <b>Importe:</b> {{$importe}}
                        <br>
                        <b>Duración:</b> {{$duracion}}                  
                        <br>
                        <b>Fecha Inicio:</b> {{\Carbon\Carbon::parse($fi)->format('d-m-Y')}}
                        <br>
                        <b>Fecha Fin:</b> {{\Carbon\Carbon::parse($ff)->format('d-m-Y')}}
                        <br>
                        <b>Fecha Vencimiento:</b> {{\Carbon\Carbon::parse($fvto)->format('d-m-Y')}}                    
                    </div>
                    <div class="col-sm-8 mt-1">                  
                        <textarea class="form-control form-control-sm" wire:model="comentarios" rows="3" placeholder="Comentarios..."></textarea>                    
                        <label class="new-control new-checkbox checkbox-primary mt-1">
                            <input data-name="pagar" wire:model="pagado"
                            type="checkbox" class="new-control-input checkbox-rol">
                            <span class="new-control-indicator"></span>
                             Pagado
                        </label>                   
                    </div> 
                </div>                   
                    
                @if($estadoPlan == 'finalizado')    
                <div class="text-center mt-2">							
                    <button type="button"
                        wire:click.prevent="StoreOrUpdate()" 
                        class="btn btn-sm btn-primary" enabled>
                        GUARDAR
                    </button>
                </div>
                @else
                <div class="text-center mt-2">							
                    <button type="button"
                        wire:click.prevent="StoreOrUpdate()" 
                        class="btn btn-sm btn-primary" disabled>
                        GUARDAR
                    </button>
                </div>
				@endif
            </div>                   
        </div>                    
    </div> 
</div>
