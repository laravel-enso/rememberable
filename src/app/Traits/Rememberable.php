<?php

namespace LaravelEnso\Rememberable\app\Traits;

use LaravelEnso\Rememberable\app\Layers\Cache;
use LaravelEnso\Rememberable\app\Layers\Memory;

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
        $key = (new static)->getTable().':'.$id;

        if ($model = self::getFromCache($key)) {
            return $model;
        }

        $model = static::find($id);

        return $model ? tap($model)->cachePut() : null;
    }

    public function cachePut()
    {
        Memory::getInstance()->cachePut($this);

        Cache::getInstance()->cachePut($this);
    }

    private function cacheForget()
    {
        Memory::getInstance()->cacheForget($this);

        Cache::getInstance()->cacheForget($this);
    }

    private static function getFromCache($key)
    {
        $model = Memory::getInstance()->cacheGet($key);

        if ($model !== null) {
            return $model;
        }

        $model = Cache::getInstance()->cacheGet($key);

        if ($model !== null) {
            Memory::getInstance()->cachePut($model);
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
