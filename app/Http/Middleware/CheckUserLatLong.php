<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserLatLong
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $preference = Auth::user()->preference;
        $user_lat = $preference ? $preference->current_lat : null;
        $user_long = $preference ? $preference->current_long : null;

        if ($user_lat == null || $user_long == null) {
            $response = [
                'success' => false,
                'message' => "User latitude and longitude is required",
                'data' => null,
            ];
            $code = 404;

            return response()->json($response, $code);
        }
        return $next($request);
    }
}
