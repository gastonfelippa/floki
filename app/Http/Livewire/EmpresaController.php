<?php

namespace App\Http\Livewire;

use Intervention\Image\Facades\Image;

use Livewire\Component;
use App\Models\Empresa;
use DB;

class EmpresaController extends Component
{
    public $nombre, $telefono, $email, $direccion, $logo, $event;

    public function mount()
    {
        $this->event = false;
        $empresa = Empresa::all();
        if($empresa->count())
        {
            $this->nombre = $empresa[0]->nombre;
            $this->telefono = $empresa[0]->telefono;
            $this->email = $empresa[0]->email;
            $this->direccion = $empresa[0]->direccion;
            $this->logo = $empresa[0]->logo;
        }
    }
    
    public function render()
    { 
        return view('livewire.empresa.component');
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
            'nombre' => 'required',
            'telefono' => 'required',
            'email' => 'required|email',
            'direccion' => 'required'
        ];

        $customMessages = [
            'nombre.required' => 'El campo nombre es requerido',
            'telefono.required' => 'El campo teléfono es requerido',
            'email.required' => 'El campo email es requerido',
            'direccion.required' => 'El campo dirección es requerido',
        ];

        $this->validate($rules, $customMessages);

        DB::table('empresas')->truncate(); //elimina la info de la tabla

        $empresa = Empresa::create([
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion
            ]);

        //programación para subir el logo        
        if($this->logo != null && $this->event)
        {
            //carga cualquier imagen y la guarda en la carpeta public/images/logo       
            $image = $this->logo;   //decodificamos la data de la imagen en Base 64 
            $fileName = time(). '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
    
            $moved = \Image::make($image)->save('images/logo/'. $fileName);
            if($moved)
            {
                $empresa->logo = $fileName;
                $empresa->save();
            }

            //carga solo imágenes precargadas en el sistema desde carpeta public/images/logo
            // $empresa->logo = $this->logo;
            // $empresa->save();
        
        }

        session()->flash('message', 'Información de Empresa registrada');

    }
} 
