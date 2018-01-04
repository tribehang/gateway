<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ResponseCacheService
{
    const VALID_CACHE_METHOD = 'GET';

    /*
     * @var int
     */
    protected $cacheInHours = 1;

    public function store(array $gateway, JsonResponse $response)
    {
        if ($this->shouldStore($gateway)) {
            Cache::put($gateway['target'], serialize($response), Carbon::now()->addMinutes($this->cacheInHours));
        }
    }

    public function fetch(string $target): JsonResponse
    {
        return unserialize(Cache::get($target));
    }

    public function exists(string $target): bool
    {
        return Cache::has($target);
    }

    protected function shouldStore(array $gateway): bool
    {
        return mb_strtoupper($gateway['method']) === self::VALID_CACHE_METHOD &&
           $this->isRouteCacheable($gateway);
    }

    protected function isRouteCacheable(array $gateway): bool
    {
        $config = config('gateway');

        if (! isset($config['services'][$gateway['service']]['cache'])) {
            return false;
        }

        $routes = $config['services'][$gateway['service']]['cache'];

        foreach ($routes as $route => $time) {
            $regex = '/^' . str_replace('/', '\/', $route) . '/';
            if (preg_match($regex, $gateway['target']) === 1) {
                $this->cacheInHours = $time;
                return true;
            }
        }

        return false;
    }
}
