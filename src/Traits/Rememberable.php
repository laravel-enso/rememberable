<?php

namespace LaravelEnso\Rememberable\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Rememberable\Exceptions\Rememberable as Exception;

trait Rememberable
{
    // protected $cacheLifetime = 600 || 'forever'; // optional
    // protected array $rememberableKeys = ['id']; // optional

    public static function bootRememberable()
    {
        self::created(fn (self $model) => $model->cachePut());

        self::updated(fn (self $model) => $model->cachePut());

        self::deleted(fn (self $model) => $model->cacheForget());
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

        $containsKey = $model->remeberableKeys()->contains($key);
        throw_unless($containsKey, Exception::missingKey($key));

        if ($model = Cache::get("{$model->getTable()}:{$key}:{$value}")) {
            return $model;
        }

        $model = static::firstWhere($key, $value);

        return $model ? tap($model)->cachePut() : null;
    }

    public function cachePut()
    {
        return $this->remeberableKeys()
            ->reduce(fn ($carry, $key) => $this->cachePutKey($key));
    }

    public function cacheForget()
    {
        return $this->remeberableKeys()
            ->map(fn ($key) => Cache::forget($this->getCacheKey($key)))
            ->last();
    }

    public function getCacheKey(string $key): string
    {
        return static::class.".:{$key}:{$this->{$key}}";
    }

    protected function cachePutKey(string $key)
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
            ?? Config::get('enso.rememberable.cacheLifetime');
    }

    protected function remeberableKeys(): Collection
    {
        return Collection::wrap(
            $this->rememberableKeys
                ?? Config::get('enso.rememberable.keys')
        );
    }
}
