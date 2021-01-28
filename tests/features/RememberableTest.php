<?php

use Faker\Factory;
use LaravelEnso\Rememberable\Exceptions\Rememberable as Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Rememberable\Traits\Rememberable;

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

    /** @test */
    public function caches_model_when_creating()
    {
        $this->assertTrue($this->model->is(
            Cache::get($this->model->getCacheKey($this->key))
        ));
    }

    /** @test */
    public function updates_cached_model_when_updating()
    {
        $this->model->update(['name' => 'Updated']);

        $this->assertEquals(
            'Updated',
            Cache::get($this->model->getCacheKey($this->key))->name
        );
    }

    /** @test */
    public function removes_cached_model_from_cache_when_deleting()
    {
        $this->model->delete();

        $this->assertFalse(
            Cache::has($this->model->getCacheKey($this->key))
        );
    }

    /** @test */
    public function can_get_by_custom_key()
    {
        $this->assertTrue($this->model->is(
            $this->model->cacheGetBy('name', $this->model->name)
        ));
    }

    /** @test */
    public function when_key_is_not_in_rememberkeys_should_throw_exception()
    {
        $this->expectException(Exception::class);
        $this->model->cacheGetBy('custom_key', 1);
    }

    /** @test */
    public function gets_cached_model()
    {
        $this->assertTrue($this->model->is(
            RememberableModel::cacheGet($this->model->id)
        ));
    }

    private function createTestModel()
    {
        return RememberableModel::create([
            'name' => $this->faker->word
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
}
