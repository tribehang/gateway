<?php

namespace App\Http\Middleware;

use Closure;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    const PUBLIC_ROUTES = [
        'signup', 'signin',
    ];

    const AUTH_SERVICE = 'auth';

    const OAUTH_SERVICE = 'oauth';

    /*
     * @var Response
     */
    protected $response;

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, Response $response)
    {
        $this->auth = $auth;
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $service = $request->segments()[0] ?? null;
        $path = $request->segments()[1] ?? null;

        $request->request->add(['gatewayService' => $service, 'gatewayPath' => $path]);

        if (empty($request->segments())) {
            return $next($request);
        }

        if ($service === self::OAUTH_SERVICE) {
            return $next($request);
        }

        if ($service === self::AUTH_SERVICE && in_array($path, self::PUBLIC_ROUTES)) {
            return $next($request);
        }

        if ($this->auth->guard($guard)->guest()) {
            return $this->response->errorUnauthorized('Unauthorized Access');
        }

        return $next($request);
    }
}
