<div class="row layout-top-spacing justify-content-center">	
    <div class="col-12 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one">
                <div class="row mb-2">
    				<div class="col-sm-12 col-md-5 text-center">
    					<h3><b>Reservas/Estado de Mesas</b></h3>
                        <button class="btn btn-danger mb-2" wire:click="agregarReserva">Agregar Reserva</button>
                        <button type="button" class="btn btn-success" enabled>
                                <a id="link">
                                Imprimir</a>
                                <!-- <a href="{{url('print/visita',array($factura_id))}}" target="_new">
                                Imprimir</a> -->
                            </button>
                            <input id="idFact" value={{$factura_id}}></input>
                    <div class="col-sm-12 col-md-7 text-left">
                        <div class="form-check form-check-inline" style="font-weight: bold;font-size: 20px;">
                        <input class="form-check-input" type="radio" wire:model="estadoMesa" value="1" checked>
                        Todas</div>
                        <div class="form-check form-check-inline" style="font-weight: bold;font-size: 20px;">
                        <input class="form-check-input" type="radio" wire:model="estadoMesa" value="2">
                        Disponibles</div>
                        <div class="form-check form-check-inline" style="font-weight: bold;font-size: 20px;">
                        <input class="form-check-input" type="radio" wire:model="estadoMesa" value="3">
                        Ocupadas</div>
                        <div class="form-check form-check-inline" style="font-weight: bold;font-size: 20px;">
                        <input class="form-check-input" type="radio" wire:model="estadoMesa" value="4">
                        C/factura</div>
                        <div class="form-check form-check-inline" style="font-weight: bold;font-size: 20px;">
                        <input class="form-check-input" type="radio" wire:model="estadoMesa" value="5">
                        Canceladas</div>
                        <div class="form-check form-check-inline" style="font-weight: bold;font-size: 20px;">
                        <input class="form-check-input" type="radio" wire:model="estadoMesa" value="6">
                        Reservadas</div>
                    </div>
    			</div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a wire:click="cambiarSector" class="nav-link {{$tab == 'Interior' ? 'active' : ''}}" style="color:blue;font-weight:bold;font-size:20px;" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Interior</a>
                    </li>
                    <li class="nav-item">
                        <a wire:click="cambiarSector" class="nav-link {{$tab == 'Exterior' ? 'active' : ''}}" style="color:blue;font-weight:bold;font-size:20px;" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true"><b>Exterior</b></a>
                    </li>
                </ul> 
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show {{$tab == 'Interior' ? 'active' : ''}}" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row mt-3">
                            @foreach($info as $r)
                                @if($r->estado == "Disponible")
                                <div class="cuadrado-verde mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Ocupada")
                                <div class="cuadrado-rojo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Reservada")
                                <div class="cuadrado-azul mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "C/factura")
                                <div class="cuadrado-amarillo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Cancelada")
                                <div class="cuadrado-anaranjado mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade show {{$tab == 'Exterior' ? 'active' : ''}}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row mt-3">
                            @foreach($info as $r)
                                @if($r->estado == "Disponible")
                                <div class="cuadrado-verde mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Ocupada")
                                <div class="cuadrado-rojo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Reservada")
                                <div class="cuadrado-azul mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "C/factura")
                                <div class="cuadrado-amarillo mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                                @if($r->estado == "Cancelada")
                                <div class="cuadrado-anaranjado mr-2 mb-2" onclick="abrirMesa({{$r->id}})"><p>{{$r->descripcion}}</p></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
    		</div>
    	</div>
    </div>
</div>


<style type="text/css" scoped>
    .circulo {
        width: 10px;
        height: 10px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        background: #5cb85c;
    }
    .cuadrado-verde {
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
</style>

<script type="text/javascript">
    function abrirMesa(id)
    {
        var data = JSON.stringify(id)
        window.livewire.emit('abrirMesa', data)
    }
    document.getElementById("link").addEventListener("click", function(){
        var id = document.getElementById("idFact").value
        var ruta = "{{url('print/visita')}}" + "/" + id
        var w = window.open(ruta,"_blank","width=100,height=100")
        // setTimeout(function(){
        //     w.close();
        // }, 1000); /* 1 Segundo*/
    });
</script>