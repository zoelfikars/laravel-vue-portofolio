<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $api = new class {
                    use \App\Traits\ApiResponse;
                    public function callError($message, $code, $errors = null)
                    {
                        return $this->error($message, $code, $errors);
                    }
                };

                if ($e instanceof ValidationException) {
                    return $api->callError($e->getMessage(), 422, $e->errors());
                }

                if ($e instanceof NotFoundHttpException) {
                    return $api->callError('Resource tidak ditemukan', 404);
                }

                if ($e instanceof AuthenticationException) {
                    return $api->callError('Anda tidak terautentikasi', 401);
                }

                if ($e instanceof UnauthorizedException) {
                    return $api->callError('Anda tidak memiliki hak akses', 403);
                }

                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                $message = config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan';

                if ($statusCode == 500 && !config('app.debug')) {
                    $message = 'Terjadi kesalahan';
                } else {
                    $message = $e->getMessage();
                }

                return $api->callError($message, $statusCode < 100 || $statusCode > 599 ? 500 : $statusCode);
            }
        });
    })->create();
