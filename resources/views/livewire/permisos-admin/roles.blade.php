<div class="tab-pane fade {{$tab == 'roles' ? 'show active' : ''}}" id="roles_content" role="tabpanel">
    <div class="row mt-2">
        <div class="col-sm-12 col-md-7">
            <h6 class="text-center"><b>LISTADO DE ROLES DE USUARIOS</b></h6>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"
                        onclick="clearRoleSelected()">
                        <i class="la la-remove la-lg"></i>
                    </span>
                </div>
                <!-- <input type="text" id="roleName" wire:model.lazy="agregarRol" class="form-control  text-capitalize" placeholder="Agregar nuevo Rol..." autocomplete="off"> -->
                <input type="text" id="roleName" class="form-control  text-capitalize" placeholder="Agregar nuevo Rol..." autocomplete="off">
                <input type="hidden" id="roleId">
                <div class="input-group-prepend">
                    <span class="input-group-text" 
                        onclick="admiteCaja()">
                        <i class="la la-save la-lg"></i>
                    </span>
                </div>
            </div>
      
            <div class="table-responsive scroll">
                <table id="tblRoles" class="table table-hover table-checkable table-sm">
                    <thead>
                        <tr>
                            <th class="text-center">DESCRIPCIÓN</th>
                            <th class="text-center">USUARIOS <br>con el rol</th>
                            <th class="text-center">ACCIONES</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $r)
                        <tr>
                            <td>{{$r->alias}}</td>
                            <td class="text-center">{{\App\Models\User::role($r->name)->count()}}</td>
                            <td class="text-center">
                                @if($r->alias != 'Administrador' && $r->alias != 'No Usuario' 
                                    && $r->alias != 'Encargado' && $r->alias != 'Cajero' && $r->alias != 'Repartidor')
                                    <span style="cursor:pointer"
                                        onclick="showRole('{{$r}}')"
                                        title="Editar rol">
                                        <i class="la la-edit la-2x text-center"></i>
                                    </span>
                                    @if(\App\Models\User::role($r->name)->count() <= 0)
                                        <a href="javascript:void(0)"
                                            onclick="Confirm('{{$r->id}}', 'destroyRole')"
                                            title="Eliminar rol">
                                            <i class="la la-trash la-2x text-center"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">                            
                                <div class="n-check" id="divRoles">
                                    <label class="new-control new-checkbox checkbox-primary">
                                        <input data-name="{{$r->name}}" 
                                        {{$r->checked == 1 ? 'checked' : ''}}
                                        type="checkbox" class="new-control-input checkbox-rol">
                                        <span class="new-control-indicator"></span>
                                        Asignar
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    
        <div class="col-sm-12 col-md-5">
            <h6 class="text-left"><b>Elegir Usuario</b></h6>   
            <div class="input-group">
                <select wire:model="userSelected" id="userId" class="form-control">
                    <option value="Seleccionar">Seleccionar</option>
                    @foreach($usuarios as $u)
                    <option value="{{$u->id}}">{{$u->apellido}}, {{$u->name}}</option>
                    @endforeach
                </select>
            </div>  
            @if($habilitar_botones) 
            <button type="button" onclick="AsignarRoles()" class="btn btn-primary mt-4" enabled>Asignar Roles</button> 
            @else     
            <button type="button" onclick="AsignarRoles()" class="btn btn-primary mt-4" disabled>Asignar Roles</button>      
            @endif
        </div>
    </div>
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 245px;
    margin-top: .5rem;
    overflow: auto;
}
</style>