<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\ErrorReport;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            'check.dept.upload' => \App\Http\Middleware\CheckDepartmentUpload::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $exception): void {
            try {
                $user = auth()->user();
                $request = request();

                if (!$user || !$user->hasRole('operator')) {
                    return;
                }

                if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
                    return;
                }

                $errorType = class_basename($exception);
                $errorMessage = $exception instanceof QueryException
                    ? 'Terjadi kesalahan database. Periksa log server untuk detail teknis.'
                    : $errorType . ': ' . $exception->getMessage();
                $errorMessage = preg_replace(
                    '/(?i)\b(password|token|authorization|cookie|secret)\b(\s*[=:]\s*)("[^"]*"|\'[^\']*\'|[^\s,;]+)/',
                    '$1$2[REDACTED]',
                    Str::limit($errorMessage, 5000, '')
                ) ?? $errorMessage;
                $pagePath = '/' . ltrim($request->path(), '/');

                $existingReport = ErrorReport::query()
                    ->where('user_id', $user->getKey())
                    ->where('error_type', $errorType)
                    ->where('error_message', $errorMessage)
                    ->where('page_path', $pagePath)
                    ->where('http_method', $request->method())
                    ->where('occurred_at', '>=', now()->subMinutes(2))
                    ->latest('occurred_at')
                    ->first();

                $report = $existingReport ?? ErrorReport::create([
                        'user_id' => $user->getKey(),
                        'actor_name' => Str::limit((string) $user->name, 255, ''),
                        'role_name' => $user->isActualSuperAdmin() && $user->accessMode()
                            ? 'Master sebagai ' . Str::headline($user->accessMode())
                            : $user->roles->pluck('name')->map(fn (string $name) => Str::headline($name))->implode(', '),
                        'department_name' => $user->department?->nama_dinas,
                        'error_type' => $errorType,
                        'error_message' => $errorMessage,
                        'page_path' => Str::limit($pagePath, 2048, ''),
                        'http_method' => $request->method(),
                        'browser_info' => Str::limit((string) $request->userAgent(), 1000, ''),
                        'occurred_at' => now(),
                    ]);

                $request->attributes->set('error_report_id', $report->getKey());
            } catch (Throwable) {
                // Preserve Laravel's normal exception reporting if automatic capture is unavailable.
            }
        });

        $exceptions->respond(function ($response, Throwable $exception, $request) {
            $reportId = $request->attributes->get('error_report_id');
            if ($reportId) {
                $response->headers->set('X-Error-Report-Id', (string) $reportId);
            }

            return $response;
        });
    })->create();
