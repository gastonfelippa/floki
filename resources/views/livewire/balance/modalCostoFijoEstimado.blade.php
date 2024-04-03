<div class="modal fade" id="modalCostoFijoEstimado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <div class="row col-12">
                <div class="col-10 text-left">
                    <h5 class="modal-title">Costos Fijos Estimados</h5>
                </div>
                <div class="col-2 text-right">
                    <button class="btn btn-dark" data-dismiss="modal">Volver</button>
                </div>
            </div>
        </div>       
        <div class="modal-body">
            <div class="widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="table-responsive scroll-small">
                            <table class="table table-hover table-checkable table-sm">
                                <thead class="encabezado">
                                    <tr>
                                        <th>DESCRIPCION</th>
                                        <th class="text-right">IMPORTE</th>
                                        <th class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody class="contenido">
                                    @foreach($info_cf_estimado as $r)
                                    <tr>
                                        <td>{{$r->descripcion}}</td>
                                        <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                        <td class="text-center">
                                            <ul class="table-controls">
                                                <li>
                                                    <a href="javascript:void(0);" 
                                                    onclick="edit({{$r->id}},'{{$r->descripcion}}',{{$r->importe}})" 
                                                    data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);"          		
                                                    onclick="eliminar('{{$r->id}}')"
                                                    data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                                </li> 
                                            </ul>                                      
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-6">                          
                            <h6>TOTAL: $ {{number_format($total_cf_estimado,2,',','.')}}</h6>
                        </div>
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="row">
                        <input id="id_cf" type="hidden">
                        <div class="form-group col-5">
                            <label>Descripción</label>
                            <input id="desc_cf" type="text" class="form-control form-control-sm">
                        </div>                       
                        <div class="form-group col-3">
                            <label>Importe</label>
                            <input id="importe_cf" type="text" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-4 mt-2">
                            <label for=""></label>
                            <div class="row">
                                <div class="col-4 mr-2">
                                    <button id="btn_edit" class="btn btn-danger" onclick="guardar_costos_fijos(1)"
                                    data-toggle="tooltip" data-placement="top" title="Guardar Cambios"><i class="bi bi-pencil-square"></i></button>
                                    <button id="btn_add" class="btn btn-danger" onclick="guardar_costos_fijos(0)"
                                    data-toggle="tooltip" data-placement="top" title="Guardar"><i class="bi bi-plus-lg"></i></button>                                    
                                </div>
                                <div class="col-6">
                                    <button id="btn_cancel" class="btn btn-info" onclick="cancelar()"
                                    data-toggle="tooltip" data-placement="top" title="Cancelar"><i class="bi bi-arrow-return-left"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="modal-footer">
            
        </div> --}}
    </div>
</div>
</div>