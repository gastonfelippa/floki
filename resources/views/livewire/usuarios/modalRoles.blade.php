<div class="modal fade" id="modalRolUsuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Asignación de Roles</h5>
        </div>
       
        <div class="modal-body">
            <div class="widget-content-area">
                <div class="widget-one">
                    <form>
                        <div class="row">                          
                            <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                <label>Empleado</label>
                                <select id="empleado" class="form-control text-center">
                                    <option value="Elegir">Elegir</option>
                                    @foreach($empleados as $e)
                                        <option value="{{ $e->id }}">
                                            {{ $e->apellido }}, {{ $e->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                <label>Roles</label>
                                <select id="rol" class="form-control text-center">
                                    <option value="Elegir">Elegir</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}">
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
            <button class="btn btn-primary saveTarifa" type="button" onclick="save()">Guardar</button>
        </div>
    </div>
</div>
</div>