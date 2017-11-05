<?php

namespace LaravelEnso\RememberableModels\app\Traits;

trait CacheReader
{
    private function getModelFromCache($class, $id)
    {
        if (!$id) {
            return;
        }

        $model = null;

        if (!cache()->has($class.':'.$id)) {
            $model = $class::find($id);
            $class::addOrUpdateInCache($model);
        }

        return $model ?: cache()->get($class.':'.$id);
    }
}
