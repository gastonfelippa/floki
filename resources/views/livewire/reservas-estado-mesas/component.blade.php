<div class="row layout-top-spacing justify-content-center">	
    @if($action == 1)
    <div class="col-12 layout-spacing"> 
		<div class="widget-content-area br-4">
			<div class="widget-one">
                <div class="row mb-4">
    				<div class="col-sm-12 col-md-5 text-center">
    					<h3><b>Reservas/Estado de Mesas</b></h3>
                        <button class="btn btn-primary mb-2" wire:click="verReservas()">Reservas</button>
                        <button class="btn btn-primary mb-2" onclick="abrirMesa('D')">Delivery</button>
                        <button class="btn btn-primary mb-2" onclick="agregarMesa()">Agregar Mesa</button>
                    </div>
                    <!-- <div>
                        <img height="150px" width="200px" src="https://images.unsplash.com/photo-1653398597887-5005619e8cdc?ixlib=rb-1.2.1&raw_url=true&q=80&fm=jpg&crop=entropy&cs=tinysrgb&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=774" alt="" class="home">
                    </div> -->
                    <div class="col-sm-12 col-md-7 text-left">
                        <div class="form-check form-check-inline mb-1 p-1" style="color: #787270;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="1" checked>
                            Todas</div>
                        <div class="form-check form-check-inline mb-1 p-1" style="background-color: #13BE05;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="2">
                            Disponibles</div>
                        <div class="form-check form-check-inline mb-1 p-1" style="background-color: #F02902;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="3">
                            Ocupadas</div>
                        <!-- <div class="form-check form-check-inline mb-1 p-1" style="background-color: #E3EA0B;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="4">
                            C/factura</div>
                        <div class="form-check form-check-inline mb-1 p-1" style="background-color: #EE9007;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="5">
                            Canceladas</div> -->
                        <div class="form-check form-check-inline mb-1 p-1" style="background-color: #428bca;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="6">
                            Reservadas</div>
                        <div class="form-check form-check-inline mb-1 p-1" style="background-color: #9B9492;">
                            <input class="form-check-input" type="radio" wire:model="estadoMesa" value="7">
                            Deshabilitadas</div>
                    </div>
    			</div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a wire:click="cambiarSector('Interior')" class="nav-link {{$tab == 'Interior' ? 'active' : ''}}" style="color:blue;font-weight:bold;font-size:20px;" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="interior" aria-selected="true">Interior</a>
                    </li>
                    <li class="nav-item">
                        <a wire:click="cambiarSector('Exterior')" class="nav-link {{$tab == 'Exterior' ? 'active' : ''}}" style="color:blue;font-weight:bold;font-size:20px;" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="exterior" aria-selected="false"><b>Exterior</b></a>
                    </li>
                    <!-- <li class="nav-item">
                        <a wire:click="cambiarSector('Patio')" class="nav-link {{$tab == 'Patio' ? 'active' : ''}}" style="color:blue;font-weight:bold;font-size:20px;" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="patio" aria-selected="false"><b>Patio</b></a>
                    </li>
                    <li class="nav-item">
                        <a wire:click="cambiarSector('Entrepiso')" class="nav-link {{$tab == 'Entrepiso' ? 'active' : ''}}" style="color:blue;font-weight:bold;font-size:20px;" id="profi-tab" data-toggle="tab" href="#prole" role="tab" aria-controls="entrepiso" aria-selected="false"><b>Entrepiso</b></a>
                    </li> -->
                </ul> 
                <div class="tab-content ml-3" id="myTabContent">
                    <div class="tab-pane fade show {{$tab == 'Interior' ? 'active' : ''}}" id="interior" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row mt-3">
                            @foreach($info as $r)
                                @if($r->estado == "Disponible")
                                <div class="mesa cuadrado-verde mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Ocupada")
                                <div class="mesa cuadrado-rojo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Reservada")
                                <div class="mesa cuadrado-azul mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "C/factura")
                                <div class="mesa cuadrado-amarillo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Cancelada")
                                <div class="mesa cuadrado-anaranjado mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Deshabilitada")
                                <div class="mesa cuadrado-gris mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade show {{$tab == 'Exterior' ? 'active' : ''}}" id="exterior" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row mt-3">
                            @foreach($info as $r)
                                @if($r->estado == "Disponible")
                                <div class="mesa cuadrado-verde mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Ocupada")
                                <div class="mesa cuadrado-rojo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Reservada")
                                <div class="mesa cuadrado-azul mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "C/factura")
                                <div class="mesa cuadrado-amarillo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Cancelada")
                                <div class="mesa cuadrado-anaranjado mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Deshabilitada")
                                <div class="mesa cuadrado-gris mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
    		</div>
    	</div>
        <input type="hidden" id="mesa">
    </div>
    @elseif($action == 2)
	@include('livewire.reservas-estado-mesas.listado')
    @else
	@include('livewire.reservas-estado-mesas.form')	
	@endif
</div>


<style type="text/css" scoped>
    .form-check {
        border-radius: 5%;
        color: white;
        font-weight: bold;
        font-size: 15px;
        cursor: pointer;
    }
    .circulo {
        width: 10px;
        height: 10px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        background: #5cb85c;
    }
    .mesa:hover {
        width: 65px; 
        height: 65px;
        cursor: hand;
        transition: all 0.6s ease    
    }
    .cuadrado-verde{
        width: 55px; 
        height: 55px; 
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        background: #13BE05;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 25px;
    }
    .cuadrado-rojo {
        width: 55px; 
        height: 55px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%; 
        background: #F02902;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 25px;
    }
    .cuadrado-azul {
        width: 55px; 
        height: 55px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%; 
        background: #428bca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 25px;
    }
    .cuadrado-amarillo {
        width: 55px; 
        height: 55px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%; 
        background: #E3EA0B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 25px;
    }
    .cuadrado-anaranjado {
        width: 55px; 
        height: 55px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%; 
        background: #EE9007;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 25px;
    }
    .cuadrado-gris {
        width: 55px; 
        height: 55px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%; 
        background: #9B9492;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 25px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script type='text/javascript'>
    function cancelarReserva()
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Cancelar la Reserva, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
    		icon: 'warning',
			input: 'text',
    		showCancelButton: true,
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false,
			inputValidator: comentario => {
				if (!comentario) return "Por favor escribe un breve comentario";
				else return undefined;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				if (result.value) {
					let comentario = result.value;
					window.livewire.emit('cancelarReserva', comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }

    function abrirMesa(id)
    {
        //var data = JSON.stringify(id)
        window.livewire.emit('abrirMesa', id)
    }
    function agregarMesa()
    {
        window.location.href="{{ url('mesas') }}";
    }
    
    async function abrir_mesa(mesa){ 
        let data = await Swal.fire({
            title: '<b>Abrir Mesa </b>' + mesa + `<button class="btn btn-primary mt-1 ml-4" style="font-size: 17px;" onclick="deshabilitarMesa()">Deshabilitar Mesa</button>`,
            html: `<br>
            <select class="form-control selectpicker show-tick" id="lista" data-style="btn-warning" data-live-search="true" >
                <option value="-1">Elige un Mozo</option>
                @foreach($mozos as $m)
                <option value="{{ $m->id }}">
                    {{$m->apellido}} {{$m->name}}
                </option>                                        
                @endforeach 
                </select>      
            <br>`,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: `Guardar`,
            cancelButtonText: 'Cancelar',
            didOpen: async () =>{ 
                Swal.showLoading();
            // Aqui cargas la informacion que necesites en tu select
                Swal.hideLoading();
            },
            preConfirm: () => {
                try{
                    let data = {
                        lista: document.getElementById('lista').value,
                    };
                    if(data.lista == '-1')
                        throw new Error('Tienes que seleccionar  un elemento de la lista');
                        return data;
                    }catch(error){
                        Swal.showValidationMessage(error);
                    }
                }
        });

        // si tiene value es que el usuario le dio  en el boton de confirmacion
        // tu proceso , data tiene  la informacion que se capturo en el select
        if(data.value){ 
            window.livewire.emit('agregaMozo', data.value);
        }
    }
    async function abrir_mesa_reserva(mesa, cliente){ 
        let data = await Swal.fire({
            title: '<b>Abrir Mesa </b>' + mesa,
            html: `<p>Reservada para <b>` + cliente + `</b></p>
            <br>                
                <select class="form-control selectpicker show-tick" id="lista" data-style="btn-warning" data-live-search="true" >
                    <option value="-1">Elige un Mozo</option>
                    @foreach($mozos as $m)
                        <option value="{{ $m->id }}">
                            {{$m->apellido}} {{$m->name}}
                        </option>                                        
                    @endforeach 
                </select>      
            <br>`,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: `Guardar`,
            cancelButtonText: 'Cancelar',
            didOpen: async () =>{ 
                Swal.showLoading();
            // Aqui cargas la informacion que necesites en tu select
                Swal.hideLoading();
            },
            preConfirm: () => {
                try{
                    let data = {
                        lista: document.getElementById('lista').value,
                    };
                    if(data.lista == '-1')
                        throw new Error('Tienes que seleccionar  un elemento de la lista');
                        return data;
                    }catch(error){
                        Swal.showValidationMessage(error);
                    }
                }
        });

        // si tiene value es que el usuario le dio  en el boton de confirmacion
        // tu proceso , data tiene  la informacion que se capturo en el select
        if(data.value){ 
            window.livewire.emit('agregaMozo', data.value);
        }
    }
    function habilitarMesa()
    {
        var mesa = $('#mesa').val();
        Swal.fire({
                title: 'Confirmar',
                text: '¿Deseas Habilitar la Mesa ' + mesa + '?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: `Aceptar`,
                cancelButtonText: `Cancelar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('habilitarMesa', mesa);
                    swal.close();
                }
            })
    }
    function deshabilitarMesa()
    {
        var mesa = $('#mesa').val();

        Swal.fire({
            title: 'Confirmar',
            text: '¿Deseas Deshabilitar la Mesa ' + mesa + '?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: `Aceptar`,
            cancelButtonText: `Cancelar`,
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('deshabilitarMesa', mesa);
                swal.close();
            }
        })
    }
    ///abrir mesa
    window.onkeydown=PulsarTecla;
    function PulsarTecla(e)
    {
        tecla = event.keyCode;  //redirige con 'enter'
        numero = event.key;
        
        window.livewire.emit('abrirMesa', numero);
    }
    /////código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 120000);
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    window.onload = function(){
        Livewire.on('agregarMozo',(mesa)=>{
            $('#mesa').val(mesa);
            abrir_mesa(mesa);
        })
        Livewire.on('abrir_mesa_reserva',(mesa, cliente)=>{
            $('#mesa').val(mesa);
            abrir_mesa_reserva(mesa, cliente);
        })
        Livewire.on('mesa_deshabilitada',(mesa)=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Mesa ' + mesa + ' deshabilitada!!',
                showConfirmButton: false,
                timer: 1500
            })
        })
        Livewire.on('habilitar_mesa',(mesa)=>{
            $('#mesa').val(mesa);
            habilitarMesa(mesa);
        })
        Livewire.on('mesa_habilitada',(mesa)=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Mesa ' + mesa + ' habilitada!!',
                showConfirmButton: false,
                timer: 1500
            })
        })
        Livewire.on('crearReserva',(accion)=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: accion,
                showConfirmButton: false,
                timer: 1500
            })
        })
    }
</script>