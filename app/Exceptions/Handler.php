<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'User does not have the right roles.'], 403);
            }

            if (Auth::guard('user')->check()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut!');
            }

            if (Auth::guard('karyawan')->check()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut!');
            }

            // Fallback redirection for guests (though 'auth' middleware usually handles this first)
            if (request()->is('panel/*', 'karyawan*', 'departemen*', 'cabang*', 'konfigurasi/*', 'payroll*', 'cuti*', 'presensi/monitoring', 'presensi/laporan', 'presensi/rekap', 'presensi/izinsakit', 'rekap')) {
                return redirect()->route('loginadmin')->with('error', 'Silakan login sebagai admin terlebih dahulu!');
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        });
    }
}
