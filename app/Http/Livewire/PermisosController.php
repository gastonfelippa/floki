<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\ModelHasRole;
use App\Models\User;
use DB;

class PermisosController extends Component
{
    public $agregarRol, $userSelected;
    public $tab = 'roles', $roleSelected = 'Seleccionar', $habilitar_botones = true;
    public $comercioId, $adminId, $noUsuarioId;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');   
        
        $usuarios = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)
            ->where('users.name', '<>', '...')
            ->select('users.*')->get();

        $roles = Role::select('*', DB::RAW("0 as checked"))
            ->where('comercio_id', $this->comercioId)->get();

        $pProductos = Permission::where('name', 'Productos_index')
            ->orWhere('name', 'Productos_create')
            ->orWhere('name', 'Productos_edit')
            ->orWhere('name', 'Productos_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pClientes = Permission::where('name', 'Clientes_index')
            ->orWhere('name', 'Clientes_create')
            ->orWhere('name', 'Clientes_edit')
            ->orWhere('name', 'Clientes_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pEmpleados = Permission::where('name', 'Empleados_index')
            ->orWhere('name', 'Empleados_create')
            ->orWhere('name', 'Empleados_edit')
            ->orWhere('name', 'Empleados_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pFacturas = Permission::where('name', 'Facturas_index')
            ->orWhere('name', 'Facturas_edit_item')
            ->orWhere('name', 'Facturas_destroy_item')
            ->orWhere('name', 'Facturas_create_producto')
            ->orWhere('name', 'Facturas_imp')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pCategorias = Permission::where('name', 'Categorias_index')
            ->orWhere('name', 'Categorias_create')
            ->orWhere('name', 'Categorias_edit')
            ->orWhere('name', 'Categorias_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pGastos = Permission::where('name', 'Gastos_index')
            ->orWhere('name', 'Gastos_create')
            ->orWhere('name', 'Gastos_edit')
            ->orWhere('name', 'Gastos_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pUsuarios = Permission::where('name', 'Usuarios_index')
            ->orWhere('name', 'Usuarios_create')
            ->orWhere('name', 'Usuarios_edit')
            ->orWhere('name', 'Usuarios_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pCaja = Permission::where('name', 'HabilitarCaja_index')
            ->orWhere('name', 'ArqueoDeCaja_index')
            ->orWhere('name', 'MovimientosDiarios_index')
            ->orWhere('name', 'CajaRepartidor_index')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pReportes = Permission::where('name', 'Reportes_index')
            ->orWhere('name', 'VentasDiarias_index')
            ->orWhere('name', 'VentasPorFechas_index')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pConfiguraciones = Permission::where('name', 'Config_index')
            ->orWhere('name', 'Empresa_index')
            ->orWhere('name', 'Auditorias_index')
            ->orWhere('name', 'Permisos_index')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pMovDeCaja = Permission::where('name', 'Movimientos_index')
            ->orWhere('name', 'Movimientos_create')
            ->orWhere('name', 'Movimientos_edit')
            ->orWhere('name', 'Movimientos_destroy')
            ->select('*', DB::RAW("0 as checked"))->get();
        
        $pViandas = Permission::where('name', 'Viandas_index')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pCtacte = Permission::where('name', 'Ctacte_index')
            ->select('*', DB::RAW("0 as checked"))->get();

        $pOtroIngreso = Permission::where('name', 'OtroIngreso_index')
            ->select('*', DB::RAW("0 as checked"))->get();

        if($this->userSelected != '' && $this->userSelected != 'Seleccionar')
        {
            //habilita o deshabilita el botón 'Asignar Roles'
            $userRol = ModelHasRole::join('roles as r', 'r.id', 'model_has_roles.role_id')
                ->where('model_has_roles.model_id', $this->userSelected)
                ->where('r.alias', 'Admin')->select('r.id')->get();
            if($userRol->count() > 0) $this->habilitar_botones = false;
            else $this->habilitar_botones = true;
                    
            foreach($roles as $r){
                $user = User::find($this->userSelected);
                $tieneRole = $user->hasRole($r->name);
                if($tieneRole) $r->checked = 1;
            }
        }      
        
        if($this->roleSelected != 'Seleccionar')
        {
            //habilita o deshabilita el botón 'Asignar Permisos'
            foreach($roles as $r){            
                if($r->alias == 'Admin') $this->adminId = $r->id;
                if($r->alias == 'No Usuario') $this->noUsuarioId = $r->id;
            }
            if($this->roleSelected == $this->adminId || $this->roleSelected == $this->noUsuarioId) $this->habilitar_botones = false;
            else $this->habilitar_botones = true;         
            ////

            foreach($pProductos as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pClientes as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pEmpleados as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pFacturas as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pCategorias as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pGastos as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pUsuarios as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pCaja as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pReportes as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pConfiguraciones as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pMovDeCaja as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pViandas as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pCtacte as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
            foreach($pOtroIngreso as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
        }

        return view('livewire.permisos.component',[
            'roles' => $roles,
            'pProductos' => $pProductos,
            'pClientes' => $pClientes,
            'pEmpleados' => $pEmpleados,
            'pFacturas' => $pFacturas,
            'pCategorias' => $pCategorias,
            'pGastos' => $pGastos,
            'pUsuarios' => $pUsuarios,
            'pCaja' => $pCaja,
            'pReportes' => $pReportes,
            'pConfiguraciones' => $pConfiguraciones,
            'pMovDeCaja' => $pMovDeCaja,
            'pViandas' => $pViandas,
            'pCtacte' => $pCtacte,
            'pOtroIngreso' => $pOtroIngreso,
            'usuarios' => $usuarios
            ]);        
    }    
        //sección de roles
    public function resetInput()
    {
        $this->agregarRol = '';
        $this->userSelected = '';
        $this->roleSelected = 'Seleccionar';
    }
        
    protected $listeners = [
        'destroyRole' => 'destroyRole',
        'destroyPermiso' => 'destroyPermiso',
        'CrearRole' => 'CrearRole',
        'CrearPermiso' => 'CrearPermiso',
        'AsignarRoles' => 'AsignarRoles',
        'AsignarPermisos' => 'AsignarPermisos'
    ];        

    public function CrearRole($roleName, $roleId)
    {
        if($roleId) $this->UpdateRole($roleName, $roleId);
        else $this->SaveRole($roleName);
    }

    public function SaveRole($roleName)
    {
        $role = Role::where('name', $roleName . $this->comercioId)
            ->where('comercio_id', $this->comercioId)->select('name')->get();
        if($role->count() > 0){
            session()->flash('msg-ops', 'El rol que intentas registrar ya existe en el sistema');
            $this->resetInput();
            return;
        }else {
            Role::create([
                'name' => ucwords($roleName . $this->comercioId),
                'comercio_id' => $this->comercioId,
                'alias' => ucwords($roleName)
            ]);
            session()->flash('msg-ok', 'El rol se registró correctamente');
        }
        $this->resetInput();
        return;
    }

    public function UpdateRole($roleName, $roleId)
    {
        $role = Role::where('name', $roleName . $this->comercioId)
            ->where('id', '<>', $roleId)->first();
        if($role){
            session()->flash('msg-ops', 'El rol que intentas registrar ya existe en el sistema!!!');
            return;
        }

        $role = Role::find($roleId);
        $role->name = ucwords($roleName . $this->comercioId);
        $role->alias = ucwords($roleName);
        $role->save();

        session()->flash('msg-ok', 'Rol actualizado correctamente');
        $this->resetInput();
    }

    public function destroyRole($roleId)
    {
        Role::find($roleId)->delete();

        session()->flash('msg-ok', 'Se eliminó el rol correctamente');
    }

    public function AsignarRoles($rolesList)
    {
        if($this->userSelected){
            $user = User::find($this->userSelected);
            if($user)
            {
                $user->syncRoles($rolesList);
                session()->flash('msg-ok', 'Roles asignados correctamente');
                $this->resetInput();
            }
        }
    }

    //permisos
    public function CrearPermiso($permisoName, $permisoId)
    {
       // dd($permisoName, $permisoId);
        if($permisoId)
            $this->UpdatePermiso($permisoName, $permisoId);
        else
            $this->SavePermiso($permisoName);
    }

    public function SavePermiso($permisoName)
    {
        $permiso = Permission::where('name', $permisoName)->first();
        if($permiso){
            session()->flash('msg-ops', 'El permiso que intentas registrar ya existe en el sistema');
            return;
        }

        Permission::create([
            'name' => $permisoName
        ]);
        session()->flash('msg-ok', 'El permiso se registró correctamente');
        $this->resetInput();
    }

    public function UpdatePermiso($permisoName, $permisoId)
    {
        $permiso = Permission::where('name', $permisoName)->where('id', '<>', $permisoId)->first();
        if($permiso){
            session()->flash('msg-ops', 'El permiso que intentas registrar ya existe en el sistema');
            return;
        }

        $permiso = Permission::find($permisoId);
        $permiso->name = $permisoName;
        $permiso->save();

        session()->flash('msg-ok', 'Permiso actualizado correctamente');
        $this->resetInput();
    }

    public function destroyPermiso($permisoId)
    {
        Permission::find($permisoId)->delete();

        session()->flash('msg-ok', 'Se eliminó el permiso correctamente');
    }

    public function AsignarPermisos($permisosList, $roleId)
    {
        if($roleId > 0){
            $role = Role::find($roleId);
            if($role)
            {
                $role->syncPermissions($permisosList);
                session()->flash('msg-ok', 'Permisos asignados correctamente');
                $this->resetInput();
            }
        }
    }

}


