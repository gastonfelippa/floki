<div class="main-content">
@include('common.alerts') <!-- mensajes -->
    <div class="layout-pc-spacing"> 
		<div class="row layout-top-spacing"> 
			<div class="col-xs-12 col-lg-12 col-md-12 col-sm-12 layout-spacing">      
				<div class="widget-content-area br-4">
					<div class="widget-one">
                        <div class="row mb-3 text-center my-2">
                            <div class="col-sm-12 col-md-4 mb-1">
								@if($btnPPC)
								<button type="button" wire:click="planDePruebaFinalizado" class="btn btn-outline-danger btn-block" enabled>
									COMPROBAR PLANES DE PRUEBA FINALIZADOS <br>(Día 1 de c/mes)
								</button>
								@else
								<button type="button" wire:click="planDePruebaFinalizado" class="btn btn-outline-danger btn-block" disabled>
									COMPROBAR PLANES DE PRUEBA FINALIZADOS <br>(Día 1 de c/mes)
								</button>
								@endif
							</div>

                            <div class="col-sm-12 col-md-4 mb-1">
								@if($btnPAM)
									<button type="button" wire:click="planActivoEnMora" class="btn btn-outline-danger btn-block" enabled>
										COMPROBAR PLANES ACTIVOS EN MORA <br>(Día 11 de c/mes)
									</button>
								@else
									<button type="button" wire:click="planActivoEnMora" class="btn btn-outline-danger btn-block" disabled>
										COMPROBAR PLANES ACTIVOS EN MORA <br>(Día 11 de c/mes)
									</button>
								@endif
							</div>
                            <div class="col-sm-12 col-md-4">
							@if($btnPAI)
								<button type="button" wire:click="planActivoImpago" class="btn btn-outline-danger btn-block" enabled>
									COMPROBAR PLANES ACTIVOS VENCIDOS E IMPAGOS <br>(Día 16 de c/mes)
								</button>
							@else
								<button type="button" wire:click="planActivoImpago" class="btn btn-outline-danger btn-block" disabled>
									COMPROBAR PLANES ACTIVOS VENCIDOS E IMPAGOS <br>(Día 16 de c/mes)
								</button>
							@endif
							</div>    
							                     
						</div>
						<div class="row mb-3 text-center my-2">
							<div class="col-sm-12 col-md-4">
							@if($btnRAP)
								<button type="button" wire:click="renovacionAutomaticaPlanes" class="btn btn-outline-danger btn-block" enabled>
									RENOVACIÓN AUTOMÁTICA DE PLANES <br>(Día 1 de c/mes)
								</button>
							@else
								<button type="button" wire:click="renovacionAutomaticaPlanes" class="btn btn-outline-danger btn-block" disabled>
									RENOVACIÓN AUTOMÁTICA DE PLANES <br>(Día 1 de c/mes)
								</button>
							@endif
							</div>                         
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>