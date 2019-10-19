<?php

namespace LaravelEnso\Rememberable\app\Layers;

use Carbon\Carbon;
use LaravelEnso\Rememberable\app\Contracts\Driver;
use Illuminate\Support\Facades\Cache as LaravelCache;
use LaravelEnso\Rememberable\app\Contracts\Rememberable;

class Cache implements Driver
{
    private static $instance;

    public static function getInstance()
    {
        self::$instance = self::$instance ?: new static();

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function cachePut(Rememberable $rememberable)
    {
        if ($rememberable->getCacheLifetime() === 'forever') {
            LaravelCache::forever($this->getCacheKey(), $this);

            return;
        }

        LaravelCache::put(
            $rememberable->getCacheKey(),
            $rememberable,
            Carbon::now()->addMinutes($rememberable->getCacheLifetime())
        );
    }

    public function cacheForget(Rememberable $rememberable)
    {
        LaravelCache::forget($rememberable->getCacheKey());
    }

    public function cacheGet($key)
    {
        return LaravelCache::get($key);
    }
}
