<?php

namespace LaravelEnso\Rememberable\app\Traits;

use LaravelEnso\Rememberable\app\Layers\Persistent as PersistentLayer;
use LaravelEnso\Rememberable\app\Layers\Volatile as VolatileLayer;

trait Rememberable
{
    // protected $cacheLifetime = 600 || 'forever'; // optional

    protected static function bootRememberable()
    {
        self::created(function ($model) {
            $model->cachePut();
        });

        self::updated(function ($model) {
            $model->cachePut();
        });

        self::deleted(function ($model) {
            $model->cacheForget();
        });
    }

    public static function cacheGet($id)
    {
        $key = (new static())->getTable().':'.$id;
        $model = self::getFromCache($key);

        if ($model) {
            return $model;
        }

        $model = static::find($id);

        return $model ? tap($model)->cachePut() : null;
    }

    public function cachePut()
    {
        VolatileLayer::getInstance()->cachePut($this);

        PersistentLayer::getInstance()->cachePut($this);
    }

    private function cacheForget()
    {
        VolatileLayer::getInstance()->cacheForget($this);

        PersistentLayer::getInstance()->cacheForget($this);
    }

    private static function getFromCache($key)
    {
        $model = VolatileLayer::getInstance()->cacheGet($key);

        if ($model !== null) {
            return $model;
        }

        $model = PersistentLayer::getInstance()->cacheGet($key);

        if ($model !== null) {
            VolatileLayer::getInstance()->cachePut($model);
        }

        return $model;
    }

    public function getCacheKey()
    {
        return $this->getTable().':'.$this->getKey();
    }

    public function getCacheLifetime()
    {
        return $this->cacheLifetime
            ?? config('enso.config.cacheLifetime');
    }
}
