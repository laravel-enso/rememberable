<?php

namespace LaravelEnso\Rememberable\App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Driver
{
    public static function getInstance();

    public function cachePut(Model $model);

    public function cacheForget(Model $model);

    public function cacheGet($key);
}
