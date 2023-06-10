<div class="row layout-top-spacing">
  <div class="col-12 layout-spacing">
    <div class="widget-content-area">
      <div class="widget-one"> 
        <div class="row justify-content-center">
          <!-- RESERVAS -->
          @if($infoReservasP->count() || $infoReservasA->count())
            <div class="card">
              <div class="card-header" style="background-color:blue;">
                <div class="row">
                  <div class="col-8">
                    RESERVAS PARA HOY
                  </div>
                  <div class="col-4">
                    <button type="button" class="badge bg-primary"
                      onclick="openModal('reservas')">Ver más...</button> 
                  </div>
                </div>
              </div>
              <div class="card-body" style="background-color:blue;">
                @if($infoReservasP->count())
                <h5>Sin mesa asignada <span class="badge bg ml-3">{{$infoReservasP->count()}}</span></h5>
                @endif
                @if($infoReservasA->count())
                <h5>Con mesa asignada <span class="badge bg ml-2">{{$infoReservasA->count()}}</span></h5>
                @endif
              </div>
            </div>
          @endif
          <!-- PRODUCTOS -->
          @if($infoProductos->count())
            <div class="card">
              <div class="card-header" style="background-color:green;">
                <div class="row">
                  <div class="col-8">
                    PRODUCTOS
                  </div>
                  <div class="col-4">
                    <button type="button" class="badge bg-success"
                      onclick="openModal('productos')">Ver más...</button> 
                  </div>
                </div>
              </div>
              <div class="card-body" style="background-color:green;">
                  <h5>Productos sin Stock <span class="badge bg ml-2">{{$infoProductos->count()}}</span></h5>
                    <!-- <h3 class="card-title">{{$infoProductos->count()}}</h3> -->
              </div>
            </div>
          @endif
          <!-- PEDIDOS -->
          @if($infoPedidos->count())
            <div class="card">
              <div class="card-header" style="background-color:orange;">
                <div class="row">
                  <div class="col-8">
                    PEDIDOS
                  </div>
                  <div class="col-4">
                    <button type="button" class="badge bg-warning"
                      onclick="openModal('pedidos')">Ver más...</button> 
                  </div>
                </div>
              </div>
              <div class="card-body" style="background-color:orange;">
                <h5>Pedidos pendientes <span class="badge bg ml-2">{{$infoPedidos->count()}}</span></h5>
              </div>
            </div>
          @endif
          <!-- MESAS -->
          @if($infoMesas->count() > 0)
            <div class="card">
              <div class="card-header" style="background-color:red;">
                <div class="row">
                  <div class="col-8">
                    MESAS
                  </div>
                  <div class="col-4">
                    <button type="button" class="badge bg-danger"
                      onclick="openModal('mesas')">Ver más...</button> 
                  </div>
                </div>
              </div>
              <div class="card-body" style="background-color:red;">
                <h5>Pendientes c/factura <span class="badge bg ml-1">{{$infoMesas->count()}}</span></h5>
              </div>
            </div>
          @endif
          @include('livewire.cardshome.modal')
        </div>
      </div>
    </div>
  </div>
</div> 


<style type="text/css" scoped>
  h5 {
    color:white;
  }  
  .card{     
    width: 19.5rem; 
    margin-top: 5px;
    margin-right: 2px;
  }
  .card-header{     
    color:white;
    font-weight: bold; 
  }
  .card-body {    
    opacity: 0.8;
  }
  .card-title {
    color:white;
    font-weight: bold;
  }  
  .badge {
    color:white;
  }
  .bg {
    background-color: grey;
    font-size: 25px;
    min-width: 50px;
  }
</style>

<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script type="text/javascript"> 
  function openModal(option)
  {
    if(option == 'reservas'){
        $('#modalReservas').show()
        $('#modalProductos').hide()
        $('#modalPedidos').hide()
        $('#modalMesas').hide()          
        $('.modal-title').text('Reservas para hoy')
    }else if(option == 'productos'){
        $('#modalReservas').hide()
        $('#modalProductos').show()
        $('#modalPedidos').hide()
        $('#modalMesas').hide()  
        $('.modal-title').text('Productos sin stock')
    }else if(option == 'pedidos'){
        $('#modalReservas').hide()
        $('#modalProductos').hide()
        $('#modalPedidos').show()
        $('#modalMesas').hide()  
        $('.modal-title').text('Pedidos')
    }else if(option == 'mesas'){
      $('#modalReservas').hide()
        $('#modalProductos').hide()
        $('#modalPedidos').hide()
        $('#modalMesas').show()  
        $('.modal-title').text('Mesas Pendientes de Cobro')
    }
    $('#modalCardsHome').modal('show')
  }
  async function abrir_mesa_reserva(mesa, cliente){ 
    let data = await Swal.fire({
        title: '<b>Abrir Mesa </b>' + mesa,
        html: `<p>Reservada para <b>` + cliente + `</b></p>
        <br>                
            <select class="form-control selectpicker show-tick" id="lista" data-style="btn-warning" data-live-search="true" >
                <option value="-1">Elige un Mozo</option>
                @foreach($mozos as $m)
                    <option value="{{$m->id}}">
                    {{$m->apellido}} {{$m->name}}
                    </option>                                        
                @endforeach 
            </select>      
        <br>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Guardar',
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
    }else{
      window.location.href="{{ url('home') }}";
    }
  }
  window.onload = function(){
    Livewire.on('abrir_mesa_reserva',(mesa, cliente)=>{
          abrir_mesa_reserva(mesa, cliente);
    })
  }
</script>


