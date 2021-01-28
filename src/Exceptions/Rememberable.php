<?php

namespace LaravelEnso\Rememberable\Exceptions;

class Rememberable extends InvalidArgumentException
{
    public static function missingKey(string $key)
    {
        return new self("The provided key '{$key}' is in rememberableKeys");
    }
}
