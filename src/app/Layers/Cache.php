<?php

namespace LaravelEnso\Rememberable\app\Layers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Rememberable\app\Contracts\Driver;
use Illuminate\Support\Facades\Cache as CacheFacade;

class Cache implements Driver
{
    private static $instance;

    public static function getInstance()
    {
        return self::$instance
            ?? self::$instance = new static();
    }

    public function cachePut(Model $model)
    {
        if ($model->getCacheLifetime() === 'forever') {
            CacheFacade::forever($this->getCacheKey(), $this);

            return;
        }

        CacheFacade::put(
            $model->getCacheKey(),
            $model,
            Carbon::now()->addMinutes($model->getCacheLifetime())
        );
    }

    public function cacheForget(Model $model)
    {
        CacheFacade::forget($model->getCacheKey());
    }

    public function cacheGet($key)
    {
        return CacheFacade::get($key);
    }
}
