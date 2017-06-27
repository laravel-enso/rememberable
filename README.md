# Rememberable Models
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/36ec1babaf3e413793c41c54088baa86)](https://www.codacy.com/app/laravel-enso/rememberable?utm_source=github.com&utm_medium=referral&utm_content=laravel-enso/rememberable&utm_campaign=badger)
[![StyleCI](https://styleci.io/repos/90758167/shield?branch=master)](https://styleci.io/repos/90758167)
[![Total Downloads](https://poser.pugx.org/laravel-enso/rememberable/downloads)](https://packagist.org/packages/laravel-enso/rememberable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/rememberable/version)](https://packagist.org/packages/laravel-enso/rememberable)

Trait for caching Laravel models

### Use

1. Put `use Rememberable` in the CachedModel that you want to track.

2. The default caching duration is 60 minutes. If you need to change it, create a `protected property $cacheLifetime = 123;` in your CachedModel.

3. In the RemoteModel where you have a `belongsTo` relationship to the CachedModel put `use CacheReader`.

4. Define a method in the RemoteModel as below:

    ```
    public function getCachedModel()
        {
            return $this->getModelFromCache(CachedModel::class, $this->cached_model_id);
        }
    ```

5. You can call the relation like this: `$remoteModel->getCachedModel()->chainOtherRelationsOrMethods`.

6. You can use the `CacheReader` trait in any class where you want to get a cached model like this: `$this->getModelFromCache(CachedModel::class, $cachedModelId)`.

### Note

The [laravel-enso/core](https://github.com/laravel-enso/Core) package comes with this library included.

### Contributions

are welcome