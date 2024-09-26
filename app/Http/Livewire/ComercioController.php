<?php

namespace App\Http\Livewire;

use DB;

use Livewire\Component;
use App\Models\Comercio;
use App\Models\Localidad;
use App\Models\Provincia;
use Intervention\Image\Facades\Image;

class ComercioController extends Component
{
    public $nombre, $telefono, $email, $calle, $numero, $logo, $logo_nuevo, $event;
    public $comercioId, $localidades, $provincias, $localidad = null;

    public function mount()
    {
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  
        $this->event = false;

        $comercio = Comercio::where('comercios.id', $this->comercioId)->first();
        // if($comercio->localidad_id){
        //     $comercio = Comercio::join('localidades as loc', 'loc.id', 'comercios.localidad_id')
        //                     ->where('comercios.id', $this->comercioId)
        //                     ->select('comercios.*', 'loc.descripcion')->first();
        // } 
        $this->nombre   = $comercio->nombre;
        $this->telefono = $comercio->telefono;
        $this->email    = $comercio->email;
        $this->calle    = $comercio->calle;
        $this->numero   = $comercio->numero;
        $this->localidad = $comercio->localidad_id;
        $this->logo     = $comercio->logo;
      
        $this->provincias = Provincia::all();
    }
    public function render()
    {
        $this->localidades = Localidad::where('comercio_id', $this->comercioId)
                                ->select()->orderBy('descripcion')->get();
 
        return view('livewire.comercios.component');
    }

    protected $listeners = [
        'logoUpload',
        'createFromModal'
    ];

    public function logoUpload($imageData, $nombreLogo)
    {
        $this->logo = $imageData;
        $this->logo_nuevo = $imageData;
       // $this->nombre_logo = $nombreLogo;
        $this->event = true;
    }
    
    public function Guardar()
    {
        $rules = [
            'nombre'    => 'required',
            'telefono'  => 'required',
            'email'     => 'required|email',
            'calle' => 'required',
            'numero'    => 'required',
            'localidad' => 'not_in:Elegir'
        ];

        $customMessages = [
            'nombre.required'   => 'El Nombre es requerido',
            'telefono.required' => 'El Teléfono es requerido',
            'email.required'    => 'El Email es requerido',
            'calle.required'    => 'La Calle es requerida',
            'numero.required'   => 'El Número es requerido',
            'localidad_id.not_in' => 'La Localidad es requerida',
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
                'nombre'       => $this->nombre,
                'telefono'     => $this->telefono,
                'email'        => $this->email,
                'calle'        => $this->calle,
                'numero'       => $this->numero,
                'localidad_id' => $this->localidad,
                'logo'         => $this->logo
            ]);
            $this->emit('grabado');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Los cambios no se grabaron...');
        }
        return;
    }

    public function createFromModal($info)
    {
        $data = json_decode($info);

        $existe = Localidad::where('descripcion', ucwords($data->localidad))
            ->where('provincia_id', $data->provincia_id)
            ->where('comercio_id', $this->comercioId)->get();  
        if($existe->count() > 0 ) {
            session()->flash('info', 'La Localidad ingresada ya existe!!!');
            return;
        }else{
            DB::begintransaction();
            try{   
                Localidad::create([
                    'descripcion'  => ucwords($data->localidad),
                    'provincia_id' => $data->provincia_id,
                    'comercio_id'  => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Localidad creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }
} 
