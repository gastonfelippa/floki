<div class="col-12"> 
	<div class="widget-content-area">
        <div class="widget-one">
        @include('common.messages')
            <!-- <h6 class="text-center"><b>VIANDAS</b></h6> -->
            <div class="row row-cols-1 row-cols-lg-4">
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Lunes</div>
                        <div class="card-body">
                            <tr>
                                <td class="text-right">
                                    <div class="row">
                                         <div class="col-xs-5 ml-2">       
                                             <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_lunes_m > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_lunes_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_lunes_m">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_lunes_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                            <input class="form-control" type="time" wire:model="h_lunes_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_lunes_n">
                                           </div>
                                    </div>
                                   </td>
                            </tr>
                        </div>
                    </div>
                </div> 
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Martes</div>
                        <div class="card-body">
                            <tr>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_martes_m > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_martes_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_martes_m">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_martes_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                            <input class="form-control" type="time" wire:model="h_martes_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_martes_n">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div> 
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Miércoles</div>
                        <div class="card-body">
                            <tr>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_miercoles_m > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                       <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_miercoles_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_miercoles_m">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_miercoles_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                            <input class="form-control" type="time" wire:model="h_miercoles_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_miercoles_n">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div> 
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Jueves</div>
                        <div class="card-body">
                            <tr>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_jueves_m > 0 ? 'checked' : ''}}
                                                       type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                            </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_jueves_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                               <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_jueves_m">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                             <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_jueves_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                            <input class="form-control" type="time" wire:model="h_jueves_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_jueves_n">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div>  
            </div> 
            <div class="row row-cols-1 row-cols-lg-4">    
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Viernes</div>
                        <div class="card-body">
                            <tr>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                    <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_viernes_m > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_viernes_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_viernes_m">
                                        </div>
                                     </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_viernes_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                                <input class="form-control" type="time" wire:model="h_viernes_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_viernes_n">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div> 
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Sábado</div>
                        <div class="card-body">
                            <tr>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_sabado_m > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_sabado_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_sabado_m">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                                <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_sabado_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                            <input class="form-control" type="time" wire:model="h_sabado_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_sabado_n">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div> 
                <div class="col">
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Domingo</div>
                        <div class="card-body">
                                <tr>
                                <td class="text-left">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_domingo_m > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-1" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5 mb-1">                
                                            <input class="form-control" type="time" wire:model="h_domingo_m">
                                        </div>
                                        <div class="text-center ml-1 mb-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_domingo_m">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-xs-5 ml-2">       
                                            <div class="n-check">
                                                <label class="new-control new-checkbox checkbox-danger">
                                                    <input 
                                                    {{$c_domingo_n > 0 ? 'checked' : ''}}
                                                    type="checkbox" class="new-control-input checkbox-rol">
                                                    <span class="new-control-indicator"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/></svg>
                                                </label>
                                            </div>   
                                        </div>
                                        <div class="col-xs-5">                
                                               <input class="form-control" type="time" wire:model="h_domingo_n">
                                        </div>
                                        <div class="text-center ml-1" id="num">       
                                            <input class="form-control" type="number" min="0" placeholder="0" wire:model="c_domingo_n">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div> 
                <div class="col">
                    <h6>{{$apeCliV}}, {{$nomCliV}}</h6>
                    <div class="mb-1">
                        <select wire:model="producto" class="form-control form-control-sm">
                                <option value="Elegir" >Elegir producto</option>
                                @foreach($productos as $t)
                                <option value="{{ $t->id }}">{{$t->descripcion}}</option> 
                                @endforeach                               
                            </select>
                            </div>
                    <div class="md-form mb-3">
                        <textarea id="form1" rows="2" class="md-textarea form-control" wire:model="comentarios" placeholder="Agrega un comentario..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                                <i class="mbri-left"></i> Cancelar
                            </button>
                            <button type="button"
                                wire:click="grabarViandas()"   
                                class="btn btn-primary">
                                <i class="mbri-success"></i> Guardar
                            </button>
                        </div>
                    </div> 
                </div> 
                </div> 
        </div>
    </div>
</div>

<style type="text/css" scoped>
#num{
    width: 70px;
}
</style>
