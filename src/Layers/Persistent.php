<?php

namespace LaravelEnso\Rememberable\Layers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use LaravelEnso\Rememberable\Contracts\Driver;

class Persistent implements Driver
{
    private static Persistent $instance;

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

        $duration = Carbon::now()->addMinutes($model->getCacheLifetime());

        Cache::put($model->getCacheKey(), $model, $duration);
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
