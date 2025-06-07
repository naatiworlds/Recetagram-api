<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;

class FCMService
{
    protected Messaging $messaging;

    public function __construct()
    {
        // Cargar las credenciales desde las variables de entorno
        $firebase = (new Factory)
            ->withServiceAccount([
                'type' => env('FIREBASE_TYPE'),
                'project_id' => 'recetagram-d8ba9',
                'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
                'private_key' => str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY')),
                'client_email' => env('FIREBASE_CLIENT_EMAIL'),
                'client_id' => env('FIREBASE_CLIENT_ID'),
                'auth_uri' => env('FIREBASE_AUTH_URI'),
                'token_uri' => env('FIREBASE_TOKEN_URI'),
                'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_CERT_URL'),
                'client_x509_cert_url' => env('FIREBASE_CLIENT_CERT_URL'),
            ]);

        $this->messaging = $firebase->createMessaging();
    }

    public function send(array $tokens, string $title, string $body, array $data = [])
    {
        try {
            $message = [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
                'tokens' => $tokens,
            ];

            // Se agrega 'false' como segundo argumento para indicar que no es una validación solamente
            $this->messaging->sendMulticast($message, false);

            Log::info('Notificación enviada con éxito a través de FCM.', [
                'tokens' => $tokens,
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando notificación FCM: ' . $e->getMessage());
            throw $e;
        }
    }
}
