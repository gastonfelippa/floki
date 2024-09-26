<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

use App\Models\User;
use App\Models\Comercio;
use App\Models\Factura;
use App\Models\Detfactura;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Subproducto;
use Carbon\Carbon;

use DB;

class PrinterController extends Controller
{

    public $name, $price, $dollarSign;
    //método para imprimir ticket de visita
    public function TicketVisita(Request $request)
    {
        $comercioId = session('idComercio');

        $folio = str_pad($request->id, 7, "0", STR_PAD_LEFT);   //funcion que rellena con ceros a la izquierda hasta llegar a 7 cifras
        $nombreImpresora = "80 Printer";   //impresora a utilizar. Debe estar instalada y compartida en red. Instalar con drives propios
                                //o instalar como genérica (generic_text) en donde imprime como texto plano
        $connector = new WindowsPrintConnector($nombreImpresora);
        $impresora = new Printer($connector);

        try{
            //obtener la info de la db
            $empresa = Comercio::find($comercioId);
            $leyenda_factura = $empresa->leyenda_factura;
            $factura = Factura::find($request->id);
            $mesa = Mesa::find($factura->mesa_id);
            $mozo = User::find($factura->mozo_id);

            $infoDetalle = Detfactura::select('*')->where('comercio_id', $comercioId)->get();
            if($infoDetalle->count() > 0){ 
                $infoDetalle = Detfactura::join('facturas as r','r.id','detfacturas.factura_id')
                    ->select('detfacturas.*', DB::RAW("'' as p_id"), 
                    DB::RAW("'' as codigo"), DB::RAW("'' as producto"), DB::RAW("'' as es_producto"))
                    ->where('detfacturas.factura_id', $factura->id)
                    ->where('detfacturas.comercio_id', $comercioId)
                    ->orderBy('detfacturas.id')->get();  
                $total = 0;
                foreach ($infoDetalle as $i){
                    $i->importe=$i->cantidad * $i->precio;
                    $total += $i->importe;
                    if($i->producto_id){
                        $producto = Producto::find($i->producto_id);
                        $i->p_id        = $producto->id;
                        $i->codigo      = $producto->codigo;
                        $i->producto    = $producto->descripcion;
                        $i->es_producto = 1;
                    }else{
                        $subproducto = Subproducto::find($i->subproducto_id);
                        $i->p_id        = $subproducto->id;
                        $i->codigo      = $subproducto->id;
                        $i->producto    = $subproducto->descripcion;
                        $i->es_producto = 0;             
                    }
                }
            }
            
            //header del ticket
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->setTextSize(2,2);      //tamaño nombre empresa
            $impresora->text(mb_strtoupper($empresa->nombre) . "\n");
            $impresora->setTextSize(1,1);      //tamaño por defecto
            $impresora->text("** Comprobante no válido como factura **\n\n");
            
            //body
            $impresora->setJustification(Printer::JUSTIFY_LEFT);
            $impresora->feed();
            $impresora->text("Fecha: ". Carbon::parse($factura->created_at)->format('d/m/Y h:m:s') . "\n");
            $impresora->text("Mozo: " . $mozo->name. "\n\n");
            $impresora->setTextSize(2,2);
            $impresora->text("Mesa N°" . $mesa->descripcion);
            $impresora->feed();
            $impresora->setTextSize(1,1);
            $impresora->text("=========================\n");
           
            $total = 0;
            foreach ($infoDetalle as $i) {
                $total += $i->cantidad * $i->importe;
                $impresora->setJustification(Printer::JUSTIFY_LEFT);
                $impresora->text($i->cantidad . " " . substr($i->producto, 0,30) . "   " . number_format(substr($i->importe, 0, 10),2));
                $impresora->feed();
            }        
            $impresora->text("=========================\n");
            $impresora->setTextSize(2,2); 
            $impresora->text("Total: $ ". number_format($factura->importe,2) . "\n\n");
            $impresora->setTextSize(1,1);
  
        //footer
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->text("Por favor conservar el ticket para hacer algún" . "\n" . "tipo de reclamo\n");
            $impresora->feed(1);
            //especificar alto y ancho del código de barras
            $impresora->selectPrintMode();
            $impresora->setBarcodeHeight(80);   //alto y ancho
            $impresora->barcode($folio, Printer::BARCODE_CODE39);   //especificamos estandar de código
            $impresora->feed(1); //agregamos 2 saltos de línea
            
            $impresora->text($leyenda_factura . "\n");
            $logo = EscposImage::load("images/floki.png", false);
            $impresora->bitImage($logo);
            // $impresora->text("www.floki.com\n");
            $impresora->feed(2);
            $impresora->cut();  //establecemos el corte de papel
            $impresora->close();
        }catch(Exception $e){/*No hacemos nada si hay error*/}
    }
    
    
    
    public function ticketPension(Request $request)
    {
        $folio = str_pad($request->id, 7, "0", STR_PAD_LEFT);   //funcion que rellena con ceros a la izquierda hasta llegar a 7 cifras
        $nombreImpresora = "HP LaserJet Pro M12w";   //impresora a utilizar. Debe estar instalada y compartida en red. Instalar con drives propios
        //o instalar como genérica (generic_text) en donde imprime como texto plano
        $connector = new WindowsPrintConnector($nombreImpresora);
        $impresora = new Printer($connector);

        //obtener la info de la db
        $empresa = Empresa::all();
        $renta = Renta::find($request->id);
        $tarifa = Tarifa::where('tarifa', 'Mes')->select('costo')->first();
        $cliente = Renta::leftjoin('cliente_vehiculo as cv', 'cv.vehiculo','rentas.vehiculo_id')
                    ->leftjoin('users as u','u.id', 'cv.user_id'->select('u.nombre'))
                    ->where('rentas.id', $renta_id)->first();

         //header del ticket
         $impresora->setJustification(Printer::JUSTIFY_CENTER);
         $impresora->setTextSize(2,2);      //tamaño nombre empresa
         $impresora->text(mb_strtoupper($empresa[0]->nombre) . "\n");
         $impresora->setTextSize(1,1);      //tamaño por defecto
         $impresora->text("** Recibo de Pensión **\n\n");

        //body
        //info cliente, fechas y total
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text("=========================\n");
        $impresora->text("Cliente: ". $cliente->nombre . "\n");
        $impresora->text("Entrada: ". Carbon::parse($renta->created_at)->format('d/m/Y h:m:s') . "\n");
        $impresora->text("Salida: ". Carbon::parse($renta->salida)->format('d/m/Y h:m:s') . "\n");
        $impresora->text("Tiempo: ". $renta->hours . 'MES(ES)' . "\n");
        $impresora->text("Tarifa: $" . number_format($tarifa->costo,2). "\n");
        $impresora->text("TOTAL: $" . number_format($tarifa->costo,2). "\n");
        $impresora->text("Placa: " . $renta->placa . " Marca: " . $renta->marca ." Color: " . $renta->color ."\n");
        $impresora->text("=========================\n");
        
        //footer
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->text("Por favor conservar el ticket hasta el pago, en caso de extravío se pagará una multa de $ 50,00\n");
    
        //especificar alto y ancho del código de barras
        $impresora->selectPrintMode();
        $impresora->setBarcodeHeight(80);   //alto y ancho
        $impresora->barcode($folio, Printer::BARCODE_CODE39);   //especificamos estandar de código
        $impresora->feed(2); //agregamos 2 saltos de línea

        $impresora->text("Gracias por su preferencia\n");
        $impresora->text("www.gnf.com\n");
        $impresora->feed(3);
        $impresora->cut();  //establecemos el corte de papel
        $impresora->close();   

    }
}
