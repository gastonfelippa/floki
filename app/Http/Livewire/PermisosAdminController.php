<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\ModelHasRole;
use App\Models\User;
use DB;

class PermisosAdminController extends Component
{
    public $agregarRol, $userSelected = 'Seleccionar';
    public $tab = 'roles', $roleSelected = 'Seleccionar', $habilitar_botones = true;
    public $comercioId, $adminId, $noUsuarioId;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');  
        
        // $usuarios = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
        //     ->where('uc.comercio_id', $this->comercioId)
        //     ->where('users.name', '<>', '...')
        //     ->select('users.*')->get();
        $usuarios = User::where('abonado', 'Si')->get();

        // $roles = Role::select('*', DB::RAW("0 as checked"))
        //     ->where('comercio_id', $this->comercioId)->get();
        $roles = Role::select('*', DB::RAW("0 as checked"))
            ->where('alias', 'Administrador')->get();


        $pViandas = Permission::where('name', 'Viandas_index')
            ->select('*', DB::RAW("0 as checked"))->get();


        if($this->userSelected != 'Seleccionar')
        {
            //habilita o deshabilita el botón 'Asignar Roles'
            //para que no se pueda modificar el rol del usuario Administrador
            $userRol = ModelHasRole::join('roles as r', 'r.id', 'model_has_roles.role_id')
                ->where('model_has_roles.model_id', $this->userSelected)
                ->where('r.alias', 'Administrador')->select('r.id')->get();
            if($userRol->count() > 0) $this->habilitar_botones = false;
            else $this->habilitar_botones = true;
                    
            foreach($roles as $r){
                $r->checked = '';
                $user = User::find($this->userSelected);
                $tieneRole = $user->hasRole($r->name);
                if($tieneRole) $r->checked = 1;
            }
        }      
        
        if($this->roleSelected != 'Seleccionar')
        {
            //habilita o deshabilita el botón 'Asignar Permisos'
            //para que no se puedan modificar los permisos de los usuarios Administrador y No Usuario
            // foreach($roles as $r){            
            //     if($r->alias == 'Administrador') $this->adminId = $r->id;
            //     if($r->alias == 'No Usuario') $this->noUsuarioId = $r->id;
            // }
            // if($this->roleSelected == $this->adminId || $this->roleSelected == $this->noUsuarioId) $this->habilitar_botones = false;
            // else $this->habilitar_botones = true;  

            //////          
            foreach($pViandas as $p){
                $role = Role::find($this->roleSelected);
                $tienePermiso = $role->hasPermissionTo($p->name);
                if($tienePermiso){
                        $p->checked = 1;
                }
            }
        }

        return view('livewire.permisos-admin.component',[
            'roles'            => $roles,
            'pViandas'         => $pViandas,
            'usuarios'         => $usuarios
            ]);        
    }    
        //sección de roles
    public function resetInput()
    {
        $this->agregarRol   = '';
        $this->userSelected = 'Seleccionar';
        $this->roleSelected = 'Seleccionar';
    } 
    protected $listeners = [
        'destroyRole'     => 'destroyRole',
        'destroyPermiso'  => 'destroyPermiso',
        'CrearRole'       => 'CrearRole',
        'CrearPermiso'    => 'CrearPermiso',
        'AsignarRoles'    => 'AsignarRoles',
        'AsignarPermisos' => 'AsignarPermisos'
    ];     
    public function CrearRole($roleName, $roleId, $admiteCaja)
    {
        if($roleId) $this->UpdateRole($roleName, $roleId, $admiteCaja);
        else $this->SaveRole($roleName, $admiteCaja);
    }
    public function SaveRole($roleName, $admiteCaja)
    {
        $role = Role::where('name', $roleName . $this->comercioId)
            ->where('comercio_id', $this->comercioId)->select('name')->get();
        if($role->count() > 0){
            session()->flash('msg-ops', 'El rol que intentas registrar ya existe en el sistema');
            $this->resetInput();
            return;
        }else {
            Role::create([
                'name'        => ucwords($roleName . $this->comercioId),
                'comercio_id' => $this->comercioId,
                'alias'       => ucwords($roleName),
                'admite_caja' => $admiteCaja
            ]);
            session()->flash('msg-ok', 'El rol se registró correctamente');
        }
        $this->resetInput();
        return;
    }
    public function UpdateRole($roleName, $roleId, $admiteCaja)
    {
        $role = Role::where('name', $roleName . $this->comercioId)
            ->where('id', '<>', $roleId)->first();
        if($role){
            session()->flash('msg-ops', 'El rol que intentas registrar ya existe en el sistema!!!');
            return;
        }

        $role = Role::find($roleId);
        $role->name        = ucwords($roleName . $this->comercioId);
        $role->alias       = ucwords($roleName);
        $role->admite_caja = $admiteCaja;
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
        if(count($rolesList,1) > 1){
            session()->flash('msg-ops', 'Solo puede haber un Rol seleccionado');
            return;
        }
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


