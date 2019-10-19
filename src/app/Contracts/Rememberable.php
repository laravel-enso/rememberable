<?php

namespace LaravelEnso\Rememberable\app\Contracts;

interface Rememberable
{
    public function getCacheKey();

    public function getCacheLifetime();
}
