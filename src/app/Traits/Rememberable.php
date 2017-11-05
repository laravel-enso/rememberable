<?php

namespace LaravelEnso\RememberableModels\app\Traits;

trait Rememberable
{
    // protected static $cacheLifetime = 600; // optional

    protected static function bootRememberable()
    {
        self::created(function ($model) {
            self::addOrUpdateInCache($model);
        });

        self::updated(function ($model) {
            self::addOrUpdateInCache($model);
        });

        self::deleted(function ($model) {
            self::removeFromCache($model);
        });
    }

    public static function addOrUpdateInCache($model)
    {
        $cacheLifetime = $model->cacheLifetime ?: config('enso.config.cacheLifetime');

        cache()->put(get_class($model).':'.$model->id, $model, $cacheLifetime);
    }

    private static function removeFromCache($model)
    {
        cache()->forget(get_class($model).':'.$model->id);
    }
}
