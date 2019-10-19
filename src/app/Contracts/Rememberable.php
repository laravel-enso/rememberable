<?php

namespace LaravelEnso\Rememberable\app\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Rememberable
{
    public function getCacheKey();

    public function getCacheLifetime();
}
