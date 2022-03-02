<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
           <h5 class="modal-title">Crear Encabezado</h5> 
        </div>       
        <div class="modal-body">
            <input id="facturaId">
            <div class="row">
                <div class="col-4 mt-1">
                    <h4>Factura:</h4>
                </div>
                <div class="col-3">
                    <select id="letra" class="form-control text-left">
                        <option value="A">A</option>
                        <option value="B">B</option>                                                            
                        <option value="C">C</option>    
                    </select> 
                </div>
                <div class="col-2">
                    <input id="sucursal" type="text" class="form-control form-control-sm text-center">
                </div>
                <div class= "col-3">
                    <input id="numFact" type="text" class="form-control form-control-sm text-center">
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-4 mt-1">
                    <h4>Fecha:</h4>
                </div>
                <div class="col-8 text-right">
                    <input id="fecha" type="text" class="form-control flatpickr flatpickr-input sm-control" placeholder="{{\Carbon\Carbon::now()->format('d-m-Y')}}" autocomplete="off">             
                </div>                    
            </div> 
            <div class="row mt-1">
                <div class="col-4 mt-1">
                    <h4>Proveedor:</h4>
                </div>
                <div class="form-group col-8">
                    <select id="proveedor" class="form-control text-left">
                        <option value="Elegir">Elegir</option>
                        @foreach($proveedores as $c)
                        <option value="{{ $c->id }}">
                            {{$c->nombre_empresa}}
                        </option>                                       
                        @endforeach 
                    </select>
                </div>
            </div>    
        </div>
        <div class="modal-footer">
            <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
            <button class="btn btn-primary" type="button" onclick="save()">Guardar</button>
        </div>
    </div>
</div>
</div>