<?php

namespace App\Http\Middleware;

use App\Services\ResponseCacheService;
use App\Services\GatewayService;
use Closure;
use Illuminate\Http\Request;

class GatewayMiddleware
{
    /*
     * @var GatewayService
     */
    protected $gatewayService;

    /*
     * @var ResponseCacheService
     */
    protected $responseCacheService;

    public function __construct(GatewayService $gatewayService, ResponseCacheService $responseCacheService)
    {
        $this->gatewayService = $gatewayService;
        $this->responseCacheService = $responseCacheService;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->gatewayService === Authenticate::AUTH_SERVICE ||
            $request->gatewayService === Authenticate::OAUTH_SERVICE ||
            empty($request->segments())
        ) {
            return $next($request);
        }

        if (is_array($request->gateway)) {
            $gateway = $request->gateway;
            $target = $gateway['target'];

            if ($this->responseCacheService->exists($target)) {
                return $this->responseCacheService->fetch($target);
            }

            $response = $this->gatewayService->handle($gateway);

            $this->responseCacheService->store($gateway, $response);

            return $response;
        }

        return $next($request);
    }
}
