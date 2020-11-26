<?php

namespace LaravelEnso\Rememberable\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

trait Rememberable
{
    // protected $cacheLifetime = 600 || 'forever'; // optional

    public static function bootRememberable()
    {
        self::created(fn ($model) => $model->cachePut());

        self::updated(fn ($model) => $model->cachePut());

        self::deleted(fn ($model) => Cache::forget($model->getCacheKey()));
    }

    public static function cacheGet($id)
    {
        if ($id === null) {
            return;
        }

        $table = (new static())->getTable();

        if ($model = Cache::get("{$table}:{$id}")) {
            return $model;
        }

        $model = static::find($id);

        return $model ? tap($model)->cachePut() : null;
    }

    public function cachePut()
    {
        Cache::put($this->getCacheKey(), $this);
    }

    public function getCacheKey()
    {
        return "{$this->getTable()}:{$this->getKey()}";
    }

    protected function getCacheLifetime()
    {
        return $this->cacheLifetime
            ?? Config::get('enso.config.cacheLifetime');
    }
}
