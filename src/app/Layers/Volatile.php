<?php

namespace LaravelEnso\Rememberable\App\Layers;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Rememberable\App\Contracts\Driver;

class Volatile implements Driver
{
    private static $instance;

    private static $cache = [];

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
