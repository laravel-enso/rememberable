<?php

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Rememberable\Exceptions\Rememberable as Exception;
use LaravelEnso\Rememberable\Traits\Rememberable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RememberableTest extends TestCase
{
    private $faker;
    private $model;
    private $key;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->createTestModelsTable();

        $this->model = $this->createTestModel();

        $this->key = 'id';
    }

    #[Test]
    public function caches_model_when_creating()
    {
        $this->assertTrue($this->model->is(
            Cache::get($this->model->getCacheKey($this->key))
        ));
    }

    #[Test]
    public function updates_cached_model_when_updating()
    {
        $this->model->update(['name' => 'Updated']);

        $this->assertEquals(
            'Updated',
            Cache::get($this->model->getCacheKey($this->key))->name
        );
    }

    #[Test]
    public function removes_cached_model_from_cache_when_deleting()
    {
        $this->model->delete();

        $this->assertFalse(
            Cache::has($this->model->getCacheKey($this->key))
        );
    }

    #[Test]
    public function can_get_by_custom_key()
    {
        $this->assertTrue($this->model->is(
            $this->model->cacheGetBy('name', $this->model->name)
        ));
    }

    #[Test]
    public function returns_null_when_cache_get_by_value_is_null()
    {
        $this->assertNull(RememberableModel::cacheGetBy('name', null));
    }

    #[Test]
    public function when_key_is_not_in_rememberkeys_should_throw_exception()
    {
        $this->expectException(Exception::class);
        $this->model->cacheGetBy('custom_key', 1);
    }

    #[Test]
    public function gets_cached_model()
    {
        $this->assertTrue($this->model->is(
            RememberableModel::cacheGet($this->model->id)
        ));
    }

    #[Test]
    public function rehydrates_model_from_database_and_caches_it_when_cache_misses()
    {
        Cache::forget($this->model->getCacheKey('id'));
        Cache::forget($this->model->getCacheKey('name'));

        $model = RememberableModel::cacheGetBy('name', $this->model->name);

        $this->assertTrue($this->model->is($model));
        $this->assertTrue(Cache::has($this->model->getCacheKey('id')));
        $this->assertTrue(Cache::has($this->model->getCacheKey('name')));
    }

    #[Test]
    public function can_get_polymorphism()
    {
        $class = ChildRememberableModel::cacheGet($this->model->id)::class;
        $this->assertEquals(ChildRememberableModel::class, $class);
        $class = RememberableModel::cacheGet($this->model->id)::class;
        $this->assertEquals(RememberableModel::class, $class);
    }

    #[Test]
    public function uses_global_cache_lifetime_when_model_does_not_define_one()
    {
        Carbon::setTestNow($now = Carbon::parse('2026-04-18 12:00:00'));
        config()->set('enso.rememberable.cacheLifetime', 45);

        Cache::shouldReceive('put')
            ->twice()
            ->withArgs(fn ($key, $value, $expiresAt) => $expiresAt->equalTo($now->copy()->addMinutes(45)))
            ->andReturnTrue();

        $model = new ConfigLifetimeRememberableModel();
        $model->forceFill(['id' => 1, 'name' => 'config-lifetime']);
        $model->cachePut();

        Carbon::setTestNow();
    }

    #[Test]
    public function caches_models_forever_when_cache_lifetime_is_forever()
    {
        Cache::shouldReceive('forever')
            ->twice()
            ->withArgs(fn ($key, $value) => $value instanceof ForeverRememberableModel)
            ->andReturnTrue();

        $model = new ForeverRememberableModel();
        $model->forceFill(['id' => 1, 'name' => 'forever']);
        $model->cachePut();
    }

    #[Test]
    public function stores_and_forgets_all_configured_rememberable_keys()
    {
        $this->assertTrue(Cache::has($this->model->getCacheKey('id')));
        $this->assertTrue(Cache::has($this->model->getCacheKey('name')));

        $this->model->delete();

        $this->assertFalse(Cache::has($this->model->getCacheKey('id')));
        $this->assertFalse(Cache::has($this->model->getCacheKey('name')));
    }

    #[Test]
    public function uses_global_rememberable_keys_when_model_does_not_define_them()
    {
        config()->set('enso.rememberable.keys', ['id', 'name']);

        $model = ConfigKeysRememberableModel::create([
            'name' => 'global-keys',
        ]);

        $this->assertTrue(Cache::has($model->getCacheKey('id')));
        $this->assertTrue(Cache::has($model->getCacheKey('name')));
    }

    #[Test]
    public function builds_cache_key_for_explicit_value()
    {
        $this->assertSame(
            RememberableModel::class.':name:explicit',
            $this->model->getCacheKey('name', 'explicit')
        );
    }

    private function createTestModel()
    {
        return RememberableModel::create([
            'name' => $this->faker->word,
        ]);
    }

    private function createTestModelsTable()
    {
        Schema::create('rememberable_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }
}

class RememberableModel extends Model
{
    use Rememberable;

    protected $cacheLifetime = 100;

    protected $rememberableKeys = ['id', 'name'];

    protected $fillable = ['name'];

    protected $table = 'rememberable_models';
}

class ChildRememberableModel extends RememberableModel
{
}

class ConfigLifetimeRememberableModel extends Model
{
    use Rememberable;

    protected $rememberableKeys = ['id', 'name'];

    protected $fillable = ['name'];

    protected $table = 'rememberable_models';
}

class ForeverRememberableModel extends Model
{
    use Rememberable;

    protected $cacheLifetime = 'forever';

    protected $rememberableKeys = ['id', 'name'];

    protected $fillable = ['name'];

    protected $table = 'rememberable_models';
}

class ConfigKeysRememberableModel extends Model
{
    use Rememberable;

    protected $fillable = ['name'];

    protected $table = 'rememberable_models';
}
