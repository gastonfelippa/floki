<?php

namespace App\Http\Middleware;

use Closure;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckInternetConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Http::isOnline()) {
            return response('Has perdido la conexión a Internet.', 503);
        }
        return $next($request);
    }
}
// <?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Support\Facades\Http;

// class CheckInternetConnection
// {
//     public function handle($request, Closure $next)
//     {
//         if (!Http::isOnline()) {
//             return response('Has perdido la conexión a Internet.', 503);
//         }

//         return $next($request);
//     }
// }

