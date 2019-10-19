<?php

namespace LaravelEnso\Rememberable\app\Traits;

use LaravelEnso\Rememberable\app\Layers\Cache;
use LaravelEnso\Rememberable\app\Layers\Memory;

trait IsRememberable
{
    private static $layers = [Memory::class, Cache::class];

    // protected $cacheLifetime = 600 || 'forever'; // optional

    protected static function bootIsRememberable()
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

    public function cachePut()
    {
        collect(static::$layers)->each(function ($layer) {
            $layer::getInstance()->cachePut($this);
        });
    }

    private function cacheForget()
    {
        collect(static::$layers)->each(function ($layer) {
            $layer::getInstance()->cacheForget($this);
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

    private static function getFromCache($key, $index = 0)
    {
        if ($index >= count(self::$layers)) {
            return;
        }

        $model = self::$layers[$index]::getInstance()->cacheGet($key);

        if ($model !== null) {
            return $model;
        }

        $model = self::getFromCache($key, $index + 1);

        if ($model !== null) {
            self::$layers[$index]::getInstance()->cachePut($model);
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
