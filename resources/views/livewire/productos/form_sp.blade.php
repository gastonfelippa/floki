<div class="col-sm-12 col-md-8 layout-spacing"> 
    <div class="widget-content-area ">
        <div class="widget-one">
            @include('common.alerts')  <!--  validación de campos -->
            @include('common.messages')  
            <h5><b>Subproductos de "{{$descripcion}}"</b></h5>
            <div class="row justify-content-between mb-3">
                <div class="col-8 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                        </div>
                        <input id="search_sp" type="text" wire:model="search_sp" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                    </div>
                </div>
                @can('Productos_create')
                <div class="col-4 mt-1">
                    <button id="btnNuevo" type="button" onclick="agregar_sp('{{$descripcion}}')" class="btn btn-danger btn-block">
                        <span style="text-decoration: underline;">N</span>uevo
                    </button>
                </div>
                @endcan
            </div>
            <div id="modalsp" class="table-responsive scroll_sp mb-3">
                <table class="table table-hover table-checkable table-sm mb-4">
                    <thead>
                        <tr>
                            <th class="text-left">DESCRIPCIóN</th>
                            <th class="text-center">STOCK ACTUAL</th>
                            <th class="text-center">STOCK IDEAL</th>
                            <th class="text-center">STOCK MíNIMO</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subproductos as $r)
                        <tr>
                            <td class="text-left">{{$r->descripcion}}</td>
                            <td class="text-center">{{$r->stock_actual}}</td>
                            <td class="text-center">{{$r->stock_ideal}}</td>
                            <td class="text-center">{{$r->stock_minimo}}</td>
                            <td class="text-center">
                                <ul class="table-controls">
                                    @can('Productos_edit')
                                    <li>
                                        <a href="javascript:void(0);" 
                                        onclick="edit_sp('{{$descripcion}}','{{$r->id}}','{{$r->descripcion}}','{{$r->stock_actual}}','{{$r->stock_ideal}}','{{$r->stock_minimo}}')" 
                                        data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                    </li>
                                    @endcan
                                    @can('Productos_destroy')
                                    <li>
                                        <a href="javascript:void(0);"          		
                                        onclick="Confirm_sp('{{$r->id}}')" data-dismiss="modal"
                                        data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                    </li>
                                    @endcan
                                </ul>                                    
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>                   
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" onclick="setfocus('nombre')"  class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Volver
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        document.getElementById("nombre_sp").focus();
    });
</script>


 