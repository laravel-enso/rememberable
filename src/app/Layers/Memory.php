<?php

namespace LaravelEnso\Rememberable\app\Layers;

use LaravelEnso\Rememberable\app\Contracts\Driver;
use LaravelEnso\Rememberable\app\Contracts\Rememberable;

class Memory implements Driver
{
    private static $instance;

    private static $cache = [];

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
        self::$cache[$rememberable->getCacheKey()] = $rememberable;
    }

    public function cacheForget(Rememberable $rememberable)
    {
        unset(self::$cache[$rememberable->getCacheKey()]);
    }

    public function cacheGet($key)
    {
        return self::$cache[$key] ?? null;
    }
}
