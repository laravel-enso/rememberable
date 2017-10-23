<!--h-->
# Rememberable Models
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/2eba208ec82d485786715915ec75f8bf)](https://www.codacy.com/app/laravel-enso/Rememberable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/Rememberable&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/90758167/shield?branch=master)](https://styleci.io/repos/90758167)
[![License](https://poser.pugx.org/laravel-enso/rememberable/license)](https://https://packagist.org/packages/laravel-enso/rememberable)
[![Total Downloads](https://poser.pugx.org/laravel-enso/rememberable/downloads)](https://packagist.org/packages/laravel-enso/rememberable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/rememberable/version)](https://packagist.org/packages/laravel-enso/rememberable)
<!--/h-->

Model caching dependency for [Laravel Enso](https://github.com/laravel-enso/Enso).

### Details

- comes with 2 traits with helper methods for quick and easy caching usage (setting and retrieving)
- the cache lifetime may be set per model, else, if not set, the per-project setting is used, finally falling back to a default of 60 minutes if neither option is available
- uses the Laravel `cache()` helper method so is transparent to the cache mechanism/implementation

### Use

1. Use the `Rememberable` trait in the CachedModel that you want to track

2. The default caching duration is 60 minutes. If you need to change it per model, create a `protected property $cacheLifetime = 123;` in your CachedModel

3. In the RemoteModel where you have a `belongsTo` relationship to the CachedModel put `use CacheReader`

4. Define a method in the RemoteModel as below:

    ```
    public function getCachedModel()
    {
        return $this->getModelFromCache(CachedModel::class, $this->cached_model_id);
    }
    ```

5. You can call the relation like this: `$remoteModel->getCachedModel()->chainOtherRelationsOrMethods`

6. You can use the `CacheReader` trait in any class where you want to get a cached model like this: `$this->getModelFromCache(CachedModel::class, $cachedModelId)`

### Notes

You may set the global cache lifetime in the `config/enso/config.php` file directly or
add/set the `CACHE_LIFETIME` key in you `.env` file (recommended).

The [Laravel Enso Core](https://github.com/laravel-enso/Core) package comes with this package included.

<!--h-->
### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
<!--/h-->