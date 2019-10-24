<?php

namespace LaravelEnso\Rememberable\app\Layers;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Rememberable\app\Contracts\Driver;

class Volatile implements Driver
{
    private static $instance;

    private static $cache = [];

    public static function getInstance()
    {
        return self::$instance
            ?? self::$instance = new static();
    }

    public function cachePut(Model $model)
    {
        self::$cache[$model->getCacheKey()] = $model;
    }

    public function cacheForget(Model $model)
    {
        unset(self::$cache[$model->getCacheKey()]);
    }

    public function cacheGet($key)
    {
        return self::$cache[$key] ?? null;
    }
}
