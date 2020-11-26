<?php

namespace LaravelEnso\Rememberable\Traits;

use Carbon\Carbon;
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
        $limit = $this->getCacheLifetime();
        $key = $this->getCacheKey();

        return $limit === 'forever'
            ? Cache::forever($key, $this)
            : Cache::put($key, $this, Carbon::now()->addMinutes($limit));
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
