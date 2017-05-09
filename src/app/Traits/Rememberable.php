<?php

namespace LaravelEnso\RememberableModels\app\Traits;

trait Rememberable
{
    // protected static $cacheLifetime = 60; // optional

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

    private static function addOrUpdateInCache($model)
    {
        $cacheLifetime = $model->cacheLifetime ?: config('laravel-enso.cacheLifetime');
        $cacheLifetime = $cacheLifetime ?: 60;

        \Cache::put(get_class($model).$model->id, $model, $cacheLifetime);
    }

    private function removeFromCache($model)
    {
        \Cache::forget(get_class($model).$model->id);
    }
}
