<?php

namespace LaravelEnso\Rememberable\Layers;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Rememberable\Contracts\Driver;

class Volatile implements Driver
{
    private static Volatile $instance;

    private static array $cache = [];

    public static function getInstance()
    {
        return self::$instance ??= new static();
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
