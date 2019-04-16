<?php

namespace LaravelEnso\Rememberable\app\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

    public function cachePut()
    {
        $cacheLifetime = $this->cacheLifetime
            ?? config('enso.config.cacheLifetime');

        if ($cacheLifetime === 'forever') {
            Cache::forever($this->getCacheKey(), $this);

            return;
        }

        Cache::put(
            $this->getCacheKey(), $this, Carbon::now()->addMinutes($cacheLifetime)
        );
    }

    private function cacheForget()
    {
        Cache::forget($this->getCacheKey());
    }

    public static function cacheGet($id)
    {
        $key = (new static)->getTable().':'.$id;

        if (! Cache::has($key)) {
            $model = self::findOrFail($id);
            $model->cachePut();

            return $model;
        }

        return Cache::get($key);
    }

    public function getCacheKey()
    {
        return $this->getTable().':'.$this->getKey();
    }
}
