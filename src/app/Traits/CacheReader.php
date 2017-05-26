<?php

namespace LaravelEnso\RememberableModels\app\Traits;

trait CacheReader
{
    private function getModelFromCache($class, $id)
    {
    	if (!$id) {
    		return null;
    	}

        $model = null;

        if (!\Cache::has($class.$id)) {
            $model = $class::find($id);
            $class::addOrUpdateInCache($model);
        }

        return $model ?: \Cache::get($class.$id);
    }
}
