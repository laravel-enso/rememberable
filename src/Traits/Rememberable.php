<?php

namespace LaravelEnso\Rememberable\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Rememberable\Exceptions\Rememberable as Exception;

trait Rememberable
{
    // protected $cacheLifetime = 600 || 'forever'; // optional
    // protected array $rememberableKeys = ['id']; // optional

    public static function bootRememberable()
    {
        self::created(fn ($model) => $model->cachePut());

        self::updated(fn ($model) => $model->cachePut());

        self::deleted(fn ($model) => Cache::forget($model->getCacheKey()));
    }

    public static function cacheGet($id)
    {
        return static::cacheGetBy('id', $id);
    }

    public static function cacheGetBy(string $key, $value)
    {
        if ($value === null) {
            return;
        }

        $model = new static();

        throw_unless(
            in_array($key, $model->remeberableKeys()), Exception::missingKey()
        );

        if ($model = Cache::get("{$model->getTable()}:{$key}:{$value}")) {
            return $model;
        }

        $model = static::firstWhere($key, $id);

        return $model ? tap($model)->cachePut() : null;
    }

    public function cachePut()
    {
        return $this->remeberableKeys()
            ->map(fn ($key) => $this->cachePutKey($key))
            ->last();
    }

    public function getCacheKey($key): string
    {
        return "{$this->getTable()}:{$key}:{$this->{$key}}";
    }

    protected function cachePutKey($key)
    {
        $limit = $this->getCacheLifetime();
        $cacheKey = $this->getCacheKey($key);

        return $limit === 'forever'
            ? Cache::forever($cacheKey, $this)
            : Cache::put($cacheKey, $this, Carbon::now()->addMinutes($limit));
    }

    protected function getCacheLifetime()
    {
        return $this->cacheLifetime
            ?? Config::get('enso.config.cacheLifetime');
    }

    protected function remeberableKeys(): Collection
    {
        return Collection::wrap($this->rememberableKeys ?? ['id']);
    }
}
