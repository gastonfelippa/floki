<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function enviar()
    {
        // $telefono = '543564416940';
        // $url = 'https://graph.facebook.com/v20.0/463910756795777/messages';
        try {
            $token = 'EAAMdWVewkXsBO8UinsBjZAlZCVJIpQHEydjUtnV0SGKXekTFt8mWSaZC3pXOMmseImodUYhQ8P1uPOZBeEZCuF7ePSmoOkaf8C2yxB5jtJ22LJ4GvZAAfMzcocjZAR3OTDlbaES8cm8KZCSE4PP7KnfRpGwNOlCTBVR7EuaT0cNB90Qm9HxyTBwRfobdaoCIoCQtZBm2E5oNOX3qZC5qZArcPj3GdoO7eBjZByRu74QZD';
            $phone_id = '463910756795777';
            $phone_to = '543564416940';
            $version = 'v20.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone_to,
                'type' => 'template',
                'template' => [
                    'name' => 'hello_world',
                    'language' => [
                        'code' => 'en_US',
                    ]
                ],
            ];

            // $message = Http::withToken($token)->post('https://graph.facebook.com/'. $version . '/' . $phone_id . '/messages' . $payload)->throw()->json();
            $message = Http::withToken($token)->post('https://graph.facebook.com/v20.0/463910756795777/messages', $payload)->throw()->json();
          
            return response()->json([
                'success' => true,
                'data' => $message
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage()
            ], 500);
        }
       

        
    }
}
