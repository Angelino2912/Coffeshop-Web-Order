<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (session('role') !== $role) {
            return redirect('/admin/login')->with('error', 'Silakan login terlebih dahulu sebagai ' . ucfirst($role) . '.');
        }

        return $next($request);
    }
}
