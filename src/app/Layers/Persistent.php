<?php

namespace LaravelEnso\Rememberable\app\Layers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use LaravelEnso\Rememberable\app\Contracts\Driver;

class Persistent implements Driver
{
    private static $instance;

    public static function getInstance()
    {
        return self::$instance ??= new static();
    }

    public function cachePut(Model $model)
    {
        if ($model->getCacheLifetime() === 'forever') {
            Cache::forever($model->getCacheKey(), $model);

            return;
        }

        Cache::put(
            $model->getCacheKey(),
            $model,
            Carbon::now()->addMinutes($model->getCacheLifetime())
        );
    }

    public function cacheForget(Model $model)
    {
        Cache::forget($model->getCacheKey());
    }

    public function cacheGet($key)
    {
        return Cache::get($key);
    }
}
