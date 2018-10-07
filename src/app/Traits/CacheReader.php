<?php

namespace LaravelEnso\RememberableModels\app\Traits;

use LaravelEnso\RememberableModels\app\Exceptions\RememberableException;

trait CacheReader
{
    private function getModelFromCache($class, $id)
    {
        if (!$id) {
            return;
        }

        if (!cache()->has($class.':'.$id)) {
            $model = $class::findOrFail($id);

            if (!method_exists($model, 'addOrUpdateInCache')) {
                throw new RememberableException(__(
                    'You forgot to use the `Rememberable` trait in ":class"',
                    ['class' => $class]
                ));
            }

            $class::addOrUpdateInCache($model);
        }

        return $model ?? cache()->get($class.':'.$id);
    }
}
