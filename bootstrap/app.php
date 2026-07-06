<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // CSRF token expired / mismatch (419) — form biasa maupun fetch/AJAX
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Sesi Anda telah berakhir, silakan login kembali.',
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        });

        // Session habis / belum login sama sekali saat akses halaman yang butuh auth
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Sesi Anda telah berakhir, silakan login kembali.',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        });
    })->create();