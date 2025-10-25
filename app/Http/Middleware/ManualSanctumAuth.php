<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ManualSanctumAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if (! $authorization || ! preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            return response()->json(['message' => 'Token not provided'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $matches[1];

        try {
            $accessToken = PersonalAccessToken::findToken($token);

            if (! $accessToken) {
                return response()->json(['message' => 'Invalid or expired token'], Response::HTTP_UNAUTHORIZED);
            }

            // $user = $accessToken->tokenable;

            // // ✅ ربط المستخدم مع الطلب
            // $request->setUserResolver(fn() => $user);

            // // ✅ ربط المستخدم مع النظام العام للمصادقة Laravel
            // Auth::setUser($user);


                 $user = $accessToken->tokenable;

                    if (! $user) {
                   return response()->json(['message' => 'User not found for token'], Response::HTTP_UNAUTHORIZED);
                }

              // ✅ ربط التوكن بالمستخدم ليدعمه currentAccessToken()
                $user->withAccessToken($accessToken);

                  // ✅ ربط المستخدم مع الطلب
                $request->setUserResolver(fn() => $user);

               // ✅ ربط المستخدم مع النظام العام للمصادقة Laravel
                Auth::setUser($user);

            return $next($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Authentication error',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
