<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SppGateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
     public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if ($user && $user->hasRole('ortu')) {
            $orangTua = $user->orangTua;

            if ($orangTua && $orangTua->siswa && !$orangTua->siswa->isSppLunas()) {
                return redirect()->route('ortu.spp.index')
                    ->with('spp_locked', true);
            }
        }

        return $next($request);
    }
}
