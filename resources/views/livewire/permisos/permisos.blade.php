<div class="tab-pane fade {{$tab == 'permisos' ? 'show active' : ''}}" id="permisos_content" role="tabpanel">
    <div class="row mt-2">
        <div class="col-sm-12 col-md-8">
            <h6 class="text-center"><b>PERMISOS DE SISTEMA</b></h6>
            <div class="table-responsive scroll">
                <div id="tblPermisos" class="row row-cols-1 row-cols-md-3 g-4">
                    <div class="col"> 
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Productos</div>
                            <div class="card-body">
                            @foreach($pProductos as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Categorias</div>
                            <div class="card-body">
                            @foreach($pCategorias as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>                    
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Clientes</div>
                            <div class="card-body">
                            @foreach($pClientes as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Proveedores</div>
                            <div class="card-body">
                            @foreach($pProveedores as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Gastos</div>
                            <div class="card-body">
                            @foreach($pGastos as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Usuarios</div>
                            <div class="card-body">
                            @foreach($pUsuarios as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3" >
                            <div class="card-header">Facturas</div>
                            <div class="card-body">
                            @foreach($pFacturas as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Compras</div>
                            <div class="card-body">
                            @foreach($pCompras as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Caja</div>
                            <div class="card-body">
                            @foreach($pCaja as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Configuraciones</div>
                            <div class="card-body">
                            @foreach($pConfiguraciones as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Reportes</div>
                            <div class="card-body">
                            @foreach($pReportes as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Cuenta Corriente</div>
                            <div class="card-body">
                            @foreach($pCtacte as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Otros Ingresos</div>
                            <div class="card-body">
                            @foreach($pOtroIngreso as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    @if($modViandas == "1")
                    <div class="col">
                        <div class="card border-dark text-dark bg-light mb-3">
                            <div class="card-header">Viandas</div>
                            <div class="card-body">
                            @foreach($pViandas as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="n-check" id="divPermisos">
                                        <label class="new-control new-checkbox checkbox-primary">
                                            <input data-name="{{$p->name}}" 
                                            {{$p->checked == 1 ? 'checked' : ''}}
                                            type="checkbox" class="new-control-input checkbox-rol">
                                            <span class="new-control-indicator"></span>
                                            {{$p->alias}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <h6 class="text-left"><b>Elegir Rol</b></h6>   
            <div class="input-group">
                <select wire:model="roleSelected" id="roleSelected" class="form-control">
                    <option value="Seleccionar">Seleccionar</option>
                    @foreach($roles as $r)
                    <option value="{{$r->id}}">{{$r->alias}}</option>
                    @endforeach
                </select>
            </div> 
            @if($habilitar_botones)  
            <button type="button" onclick="AsignarPermisos()" class="btn btn-primary mt-4" enabled>Asignar Permisos</button>      
            @else
            <button type="button" onclick="AsignarPermisos()" class="btn btn-primary mt-4" disabled>Asignar Permisos</button>      
            @endif
        </div>
    </div>
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 295px;
    margin-top: .5rem;
    overflow: auto;
}
</style>
