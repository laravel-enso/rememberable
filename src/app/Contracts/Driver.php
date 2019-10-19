<?php

namespace LaravelEnso\Rememberable\app\Contracts;

interface Driver
{
    public static function getInstance();

    public function cachePut(Rememberable $rememberable);

    public function cacheForget(Rememberable $rememberable);

    public function cacheGet($key);
}
