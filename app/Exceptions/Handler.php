<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * قائمة الاستثناءات التي لن يتم الإبلاغ عنها.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * قائمة المدخلات التي لا يجب تخزينها في الجلسات عند حدوث أخطاء التحقق.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * تسجيل الاستثناءات أو التعامل معها.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // بإمكانك تسجيل الأخطاء هنا أو إرسالها إلى Slack, Bugsnag, إلخ.
        });
    }

    /**
     * التعامل مع استثناء عدم المصادقة (401).
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }

    /**
     * معالجة استثناءات أخرى وتحويلها إلى استجابات JSON.
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // إن كان المستخدم يتوقع JSON (مثل Postman أو Flutter)
        if ($request->expectsJson()) {

            // استثناء Laravel القياسي مثل 404 أو 500
            if ($exception instanceof HttpException) {
                return response()->json([
                    'message' => $exception->getMessage() ?: 'Something went wrong.',
                    'code' => $exception->getStatusCode()
                ], $exception->getStatusCode());
            }

            // أي خطأ آخر
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $exception->getMessage(),
            ], 500);
        }

        // الاستجابة العادية (HTML) في حالة الطلب ليس JSON
        return parent::render($request, $exception);
    }
}
