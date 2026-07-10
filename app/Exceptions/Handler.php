<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
{
    $this->renderable(function (Throwable $e, Request $request) {
        // JSON/API jangan diganggu
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
        $isPartner = $user && method_exists($user, 'hasRole') && $user->hasRole('partner');

        $goAdminHome = fn($msg) => redirect()->route('admin.dashboard')->with('error', $msg);
        $goPartnerHome = fn($msg) => redirect()->route('partner.dashboard')->with('error', $msg);
        $goPublicHome = fn($msg) => redirect()->route('home')->with('error', $msg);

        // 419 Page Expired
        if ($e instanceof TokenMismatchException) {
            $msg = 'Sesi kamu sudah expired. Silakan login ulang / refresh dan coba lagi.';
            if ($isAdmin) return $goAdminHome($msg);
            if ($isPartner) return $goPartnerHome($msg);
            return $goPublicHome($msg);
        }

        // 403 Forbidden
        if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
            $msg = 'Akses ditolak (403). Kamu tidak punya hak untuk membuka halaman itu.';
            if ($isAdmin) return $goAdminHome($msg);
            if ($isPartner) return $goPartnerHome($msg);
            return $goPublicHome($msg);
        }

        // 404 Not Found
        if ($e instanceof NotFoundHttpException) {
            $msg = 'Halaman tidak ditemukan (404).';
            if ($isAdmin) return $goAdminHome($msg);
            if ($isPartner) return $goPartnerHome($msg);
            // buat guest: biarin normal 404 atau redirect home (lu maunya redirect)
            return redirect()->route('home')->with('error', $msg);
        }

        // Role/permission misconfig
        if ($e instanceof RoleDoesNotExist) {
            $msg = 'Role/permission belum lengkap. Hubungi admin.';
            if ($isAdmin) return $goAdminHome($msg);
            if ($isPartner) return $goPartnerHome($msg);
            return $goPublicHome($msg);
        }

        return null;
    });
}


}
