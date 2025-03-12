<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class CheckTokenExpiration
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Verificación inicial para confirmar la invocación
        Log::info("CheckTokenExpiration middleware invoked.", [
            'user_id' => $request->user() ? $request->user()->id : 'no user'
        ]);

        if ($request->user() && $request->user()->currentAccessToken()) {
            $token = $request->user()->currentAccessToken();
            $now = Carbon::now();

            // Copia la fecha de creación para realizar los cálculos sin modificar el token
            $createdAt = $token->created_at->copy();
            $expirationTime = $createdAt->copy()->addMinutes(config('sanctum.expiration'));

            // Calculamos el punto medio para la notificación
            $halfTime = $createdAt->copy()->addMinutes(config('sanctum.expiration') / 2);

            if ($now->greaterThan($halfTime) && !$token->warning_sent) {
                // Se calcula el tiempo restante hasta que expire el token
                $timeLeft = $now->diffInSeconds($expirationTime);
                Log::info("Enviando notificación de expiración de token para el usuario ID: " . $request->user()->id, [
                    'Tiempo restante (segundos)' => $timeLeft
                ]);

                $this->notificationService->createNotification(
                    $request->user()->id,
                    'token_expiration',
                    null,
                    null,
                    "Como medida de seguridad ante el robo de su cuenta, su sesión será reestablecida en {$timeLeft} segundos. Por favor, inicie sesión nuevamente."
                );

                $token->warning_sent = true;
                $token->save();
            }

            if ($now->greaterThan($expirationTime)) {
                $token->delete();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token expirado',
                    'code' => 'token_expired'
                ], 401);
            }
        }

        return $next($request);
    }
} 