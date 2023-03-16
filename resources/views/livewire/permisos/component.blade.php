<div class="layout-pc-spacing">
@include('common.alerts')
    <div class="row layout-top-spacing">
        <div class="col-sm-12 layout-spacing">
            <div class="widget-content-area br-4">
                <div class="widget-one mt-2">
                    <ul class="nav nav-pills mt-1 mb-2" role="tablist">
                        <li class=nav-item>
                            <a href="#roles_content" class="nav-link {{$tab == 'roles' ? 'active' : ''}}" 
                            wire:click="$set('tab','roles')"
                            data-toggle="pill" role="tab">
                                <i class="la la-user la-lg"> ROLES</i>
                            </a>
                        </li>
                        <li class=nav-item>
                            <a href="#permisos_content" class="nav-link {{$tab == 'permisos' ? 'active' : ''}}"
                            wire:click="$set('tab','permisos')"
                            data-toggle="pill" role="tab">
                                <i class="la la-user la-lg"> PERMISOS</i>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        @if($tab == 'roles')
                            @include('livewire.permisos.roles')
                            @include('livewire.permisos.modal')
                        @else ($tab == 'permisos')    
                            @include('livewire.permisos.permisos') 
                        @endif   
                    </div>       
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showRole(role)
    {
        var data = JSON.parse(role)
        $('#roleName').val(data['alias'])
        $('#roleId').val(data['id'])
    }

    function showPermiso(permission)
    {
        var data = JSON.parse(permission)
        $('#permisoName').val(data['name'])
        $('#permisoId').val(data['id'])
    }

    function clearRoleSelected()
    {
        document.querySelector('#no').checked = true;
        $('#roleName').val('')
        $('#roleid').val(0)
        $('#roleName').focus()
    }

    function clearPermissionSelected()
    {
        $('#permisoName').val('')
        $('#permisoid').val(0)
        $('#permisoName').focus()
    }

    function Confirm(id, eventName)
    {
    	swal({
    		title: 'CONFIRMAR',
    		text: '¿DESEAS ELIMINAR EL REGISTRO?',
    		type: 'warning',
    		showCancelButton: true,
    		confirmButtonColor: '#3085d6',
    		cancelButtonColor: '#d33',
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false
    	},
    	function() {
    		window.livewire.emit(eventName, id)    //emitimos evento deleteRow
    		toastr.success('info', 'Registro eliminado con éxito') //mostramos mensaje de confirmación 
            $('#roleName').val('')
            $('#roleid').val(0)
            $('#permisoName').val('')
            $('#permisoid').val(0)
    		swal.close()   //cerramos la modal
    	})
    }

    function AsignarRoles()
    {
        var rolesList =[]
        $('#tblRoles').find('input[type=checkbox]:checked').each(function(){
            rolesList.push($(this).attr('data-name'))                
        })

        if(rolesList.length < 1)
        {
            toastr.error('','Selecciona al menos un rol')
            return;
        }
        else if($('#userId option:selected').val() == 'Seleccionar')
        {
            toastr.error('','Selecciona un usuario')
            return;
        }

        window.livewire.emit('AsignarRoles', rolesList)
    }

    function AsignarPermisos()
    {
        var permisosList =[]
        $('#tblPermisos').find('input[type=checkbox]:checked').each(function(){
            permisosList.push($(this).attr('data-name'))                
        })

        if(permisosList.length < 1)
        {
            toastr.error('','Selecciona al menos un permiso')
            return;
        } 
        
        if($('#roleSelected option:selected').val() == 'Seleccionar')
        {
            toastr.error('','Selecciona un rol')
            return;
        }
        
        window.livewire.emit('AsignarPermisos', permisosList, $('#roleSelected option:selected').val())
    }

    function admiteCaja()
    {
        if($('#roleName').val() != ''){
            var roleName = $('#roleName').val()
            $('#admiteCaja').val('0')        
            $('.modal-title').text('A este Nuevo Rol llamado "' + roleName + '"... ¿Se le puede Asignar una Caja?')
            $('#modalAdmiteCaja').modal('show')
        }
    }

    function CrearRol()
    {
        var radio = document.getElementsByName('admiteCaja');
        for(i=0; i<radio.length; i++){
            if(radio[i].checked){
            var seleccion = radio[i].value;
            //  alert(seleccion);
            }
        }
        window.livewire.emit('CrearRole', $('#roleName').val(), $('#roleId').val(), seleccion)
        $('#modalAdmiteCaja').modal('hide')
        clearRoleSelected()     
    }
 
    document.addEventListener('DOMContentLoaded', function(){
        window.livewire.on('activarTab', tabName => {
            var tab = "[href='" + tabName + "']"
            $(tab).tab('show')
        });

        window.livewire.on('msg-ok' , msgText => {
            $('#roleName').val('')
            $('#roleid').val(0)
            $('#permisoName').val('')
            $('#permisoid').val(0)
        });

        $('body').on('click','.check-all', function(){
            var state = $(this).is(':checked') ? true : false

            $('#tblPermisos').find('input[type=checkbox]').each(function(e){
                $(this).prop('checked', state)
            })
        });
    });

</script>
