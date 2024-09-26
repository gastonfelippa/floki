<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Mailable;
use App\Mail\Resumen_Diario;
use Illuminate\Support\Facades\Mail;
use App\Models\ArqueoGral;
use App\Models\Factura;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\User;

class ResumenDiario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumen:diario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía por correo un resumen con los datos más importantes al administrador de cada empresa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         $comercios = ArqueoGral::where('estado', '1')
         ->select('id', 'comercio_id')->get();
    
        foreach ($comercios as $i) {    
            $adminId = Role::where('comercio_id', $i->comercio_id)
                ->where('alias', 'Administrador')
                ->select('id')->first(); 
                 
            $userId = ModelHasRole::where('role_id', $adminId->id)
                ->select('model_id')->first();
           
            $email = User::where('id', $userId->model_id)
                ->select('email')->first();
           
            $total = Factura::where('comercio_id', $i->comercio_id)
                ->where('estado', 'contado')
                ->where('estado_pago', '1')
                ->where('arqueo_id', $i->id)
                ->sum('importe');
            $resumen = Factura::where('comercio_id',$i->comercio_id)
                ->where('estado', 'contado')
                ->where('estado_pago', '1')
                ->where('arqueo_id', $i->id)
                ->select('numero', 'importe')->get();

            Mail::to($email->email)->send(new Resumen_Diario($total, $resumen));
        }  
    }
}
