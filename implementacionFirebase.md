# Implementación de Firebase y Notificaciones en Laravel

Este documento describe la trazabilidad de la implementación de Firebase en la aplicación Laravel, abarcando desde la instalación del SDK hasta la integración en la funcionalidad de notificaciones que ya existían en el sistema.

---

## 1. Instalación de Firebase en Laravel

### 1.1. Instalación del SDK de Firebase
El SDK de Firebase para PHP se instala utilizando Composer:

```bash
composer require kreait/firebase-php
```

### 1.2. Configuración Inicial
Se crea un archivo de configuración en `config/firebase.php` para gestionar las credenciales y opciones de Firebase. Este archivo permite centralizar el acceso a las configuraciones necesarias.

---

## 2. Lectura de Credenciales de Firebase

### 2.1. Variables de Entorno
Las credenciales de Firebase se almacenan en el archivo `.env` para evitar exponer información sensible en el repositorio. Las variables clave configuradas son las siguientes:

```env
FIREBASE_TYPE=service_account
FIREBASE_PROJECT_ID=recetagram-d8ba9
FIREBASE_PRIVATE_KEY_ID=e4ac58d3ea402d20211d10a6fbe334dc954463c3
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCeDYFMyVFHdBn4\n..."
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-fbsvc@recetagram-d8ba9.iam.gserviceaccount.com
FIREBASE_CLIENT_ID=107220014506617646728
FIREBASE_AUTH_URI=https://accounts.google.com/o/oauth2/auth
FIREBASE_TOKEN_URI=https://oauth2.googleapis.com/token
FIREBASE_AUTH_PROVIDER_CERT_URL=https://www.googleapis.com/oauth2/v1/certs
FIREBASE_CLIENT_CERT_URL=https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40recetagram-d8ba9.iam.gserviceaccount.com
```

### 2.2. Configuración en `config/firebase.php`
El archivo de configuración utiliza las variables de entorno para construir el arreglo de credenciales:

```php
return [
    'default' => env('FIREBASE_PROJECT', 'app'),
    'projects' => [
        'app' => [
            'credentials' => [
                'type' => env('FIREBASE_TYPE'),
                'project_id' => env('FIREBASE_PROJECT_ID'),
                'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
                'private_key' => str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY')),
                'client_email' => env('FIREBASE_CLIENT_EMAIL'),
                'client_id' => env('FIREBASE_CLIENT_ID'),
                'auth_uri' => env('FIREBASE_AUTH_URI'),
                'token_uri' => env('FIREBASE_TOKEN_URI'),
                'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_CERT_URL'),
                'client_x509_cert_url' => env('FIREBASE_CLIENT_CERT_URL'),
            ],
        ],
    ],
];
```

---

## 3. Funcionalidad: Envío de Notificaciones con Firebase

### 3.1. Servicio de Notificaciones (`FCMService.php`)
El archivo `app/Services/FCMService.php` contiene la lógica para enviar notificaciones mediante Firebase Cloud Messaging (FCM).

#### 3.1.1. Inicialización de Firebase en el Servicio
El constructor del servicio carga las credenciales desde las variables de entorno y crea una instancia de `Messaging`:

```php
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
                'project_id' => env('FIREBASE_PROJECT_ID'),
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
```

#### 3.1.2. Método `send`
El método `send` se encarga de enviar notificaciones a múltiples dispositivos. Se prepara un mensaje que incluye el título, cuerpo, datos adicionales y una lista de tokens de registro.

```php
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

            $this->messaging->sendMulticast($message);

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
```

---

## 4. Integración en la Lógica de Notificaciones Existente

### 4.1. Uso del Servicio en la Creación de Notificaciones
En el sistema de notificaciones existente, se utiliza el servicio `FCMService` para enviar notificaciones push. Por ejemplo, en el `NotificationService`, tras crear la notificación en la base de datos se invoca el método `send`:

```php
$user = User::find($userId);
$tokens = $user->notification_tokens ?? []; // Se asume que es un array

if (!empty($tokens)) {
    $this->fcm->send(
        $tokens,
        'Nueva notificación',
        $message,
        [
            'type' => $type,
            'from_user_id' => $fromUserId,
            'reference_id' => $referenceId,
        ]
    );
}
```

### 4.2. Envío de Notificaciones Personalizadas
De esta forma, las notificaciones personalizadas se integran a la funcionalidad ya existente en la aplicación, permitiendo que múltiples dispositivos de un usuario sean notificados en tiempo real.

---

## 5. Pruebas y Depuración

### 5.1. Limpieza de Caché de Configuración
Si las credenciales no se cargan correctamente, se recomienda limpiar la caché de configuración de Laravel:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5.2. Revisión de Logs
Los errores y la información sobre el envío de notificaciones se registran en los logs (`storage/logs/laravel.log`). Revisar estos logs facilita la depuración de incidencias en la integración con Firebase.

---

## 6. Consideraciones Finales

- **Seguridad:** Las credenciales de Firebase se gestionan a través del archivo `.env` para evitar que se suban al repositorio.
- **Escalabilidad:** Firebase Cloud Messaging permite enviar notificaciones a múltiples dispositivos, lo que mejora la comunicación en aplicaciones con usuarios que tienen varios dispositivos.
- **Mantenimiento:** Cualquier cambio en la configuración de Firebase (por ejemplo, actualización de credenciales) debe reflejarse en el archivo `.env` y, de ser necesario, en el archivo `config/firebase.php`.

---

Este documento sirve como guía de referencia para entender y mantener la implementación de Firebase y las notificaciones en la aplicación Laravel.