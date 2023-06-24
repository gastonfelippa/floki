<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Mailable;
use App\Mail\Resumen_Diario;
use Illuminate\Support\Facades\Mail;
use App\Models\Factura;

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
    protected $description = 'Envía por correo un resumen con los datos más importantes al dueño de la empresa';

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
        $comercioId = session('idComercio');
        $arqueoGralId = session('idArqueoGral');
  
        $total = Factura::where('facturas.comercio_id', $comercioId)
            ->where('facturas.estado', 'contado')
            ->where('facturas.estado_pago', '1')
            ->sum('facturas.importe');
        $resumen = Factura::where('facturas.comercio_id',$comercioId)
            ->where('facturas.estado', 'contado')
            ->where('facturas.estado_pago', '1')
            ->select('facturas.numero', 'facturas.importe')->get();

        Mail::to('gnfelippa@gmail.com')->send(new Resumen_Diario($total, $resumen));
    }
}
