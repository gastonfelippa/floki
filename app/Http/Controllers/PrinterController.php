<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Renta;
use App\Models\Tarifa;
use Carbon\Carbon;

class PrinterController extends Controller
{
    //método para imprimir ticket de visita
    public function TicketVisita(Request $request)
    {
        $folio = str_pad($request->id, 7, "0", STR_PAD_LEFT);   //funcion que rellena con ceros a la izquierda hasta llegar a 7 cifras
        $nombreImpresora = "HP LaserJet Pro M12w";   //impresora a utilizar. Debe estar instalada y compartida en red. Instalar con drives propios
                                //o instalar como genérica (generic_text) en donde imprime como texto plano
        $connector = new WindowsPrintConnector($nombreImpresora);
        $impresora = new Printer($connector);

        //obtener la info de la db
        $empresa = Empresa::all();
        $renta = Renta::find($request->id);
        $tarifa = Tarifa::find($renta->tarifa_id);

        //header del ticket
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(2,2);      //tamaño nombre empresa
        $impresora->text(strtoupper($empresa[0]->nombre) . "\n");
        $impresora->setTextSize(1,1);      //tamaño por defecto
        $impresora->text("** Recibo de Renta **\n\n");

        //body
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text("=========================\n");
        $impresora->text("Entrada: ". Carbon::parse($renta->created_at)->format('d/m/Y h:m:s') . "\n");
        $impresora->text("Tarifa por hora: $". number_format($tarifa->costo,2) . "\n");
        if(!empty($renta->descripcion)) $impresora->text("Desc: ". $renta->descripcion . "\n");
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
         $impresora->text(strtoupper($empresa[0]->nombre) . "\n");
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
