<?php

namespace LaravelEnso\RememberableModels\app\Traits;

trait CacheReader
{
    public function getModelFromCache($class, $id)
    {
        $model = null;

        if (!cache()->has($class.$id)) {
            $model = $class::find($id);

            if (!$model) {
                throw new \LogicException(
                    __(sprintf('No model of class: %s having the id: %s found', $class, $id))
                );
            }

            $class::addOrUpdateInCache($model);
        }

        return $model ?: cache()->get($class.$id);
    }
}
