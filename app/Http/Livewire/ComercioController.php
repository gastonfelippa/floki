<?php

namespace App\Http\Livewire;

use Intervention\Image\Facades\Image;

use Livewire\Component;
use App\Models\Comercio;
use DB;

class ComercioController extends Component
{
    public $nombre, $telefono, $email, $direccion, $logo, $event;
    public $comercioId;

    public function mount()
    {
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  
        $this->event = false;
        $comercio = Comercio::where('id', $this->comercioId)->get();
        if($comercio->count())
        {
            $this->nombre    = $comercio[0]->nombre;
            $this->telefono  = $comercio[0]->telefono;
            $this->email     = $comercio[0]->email;
            $this->direccion = $comercio[0]->direccion;
            $this->logo      = $comercio[0]->logo;
        }
    }
    public function render()
    { 
        return view('livewire.comercios.component');
    }

    protected $listeners = [
        'logoUpload' => 'logoUpload'
    ];

    public function logoUpload($imageData, $nombreLogo)
    {
        $this->logo = $imageData;
       // $this->nombre_logo = $nombreLogo;
        $this->event = true;
    }
    
    public function Guardar()
    {
        $rules = [
            'nombre'    => 'required',
            'telefono'  => 'required',
            'email'     => 'required|email',
            'direccion' => 'required'
        ];

        $customMessages = [
            'nombre.required'    => 'El campo nombre es requerido',
            'telefono.required'  => 'El campo teléfono es requerido',
            'email.required'     => 'El campo email es requerido',
            'direccion.required' => 'El campo dirección es requerido',
        ];

        $this->validate($rules, $customMessages);

        //programación para subir el logo
        if($this->logo != null && $this->event)
        {
            //carga cualquier imagen y la guarda en la carpeta public/images/logo       
            $image = $this->logo;   //decodificamos la data de la imagen en Base 64 
            $fileName = time(). '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            $moved = Image::make($image)->save('images/logo/'. $fileName);
            if($moved)
            {
                $this->logo = $fileName;
            }
            //carga solo imágenes precargadas en el sistema desde carpeta public/images/logo
            // $comercio->logo = $this->logo;
            // $comercio->save();
        } 
        DB::begintransaction();
        try{  
            $comercio = Comercio::find($this->comercioId);
            $comercio->update([
                'nombre'    => $this->nombre,
                'telefono'  => $this->telefono,
                'email'     => $this->email,
                'direccion' => $this->direccion,
                'logo'      => $this->logo
            ]);
            $this->emit('grabado');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Los cambios no se grabaron...');
        }
        return;
    }
} 
