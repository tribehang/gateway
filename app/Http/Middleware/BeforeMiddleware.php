<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BeforeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->request->add(
            [
                'gateway' =>
                    [
                        'service' => $request->segment(1) ?? '',
                        'target' => mb_strstr($request->fullUrl(), $request->path()),
                        'method' => $request->getMethod(),
                        'content' => $request->getContent(),
                    ],
            ]
        );

        return $next($request);
    }
}
