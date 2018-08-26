<?php

use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\RememberableModels\app\Traits\CacheReader;
use LaravelEnso\RememberableModels\app\Traits\Rememberable;
use Tests\TestCase;

class CacheReaderTest extends TestCase
{
    private $faker;

    public function setUp()
    {
        parent::setUp();

        $this->createCachedModelsTable()
            ->createRemoteModelsTable();

        $this->faker = Factory::create();
    }

    /** @test */
    public function gets_cached_model_from_remote_model()
    {
        $cachedModel = CachedModel::create([
            'name' => $this->faker->word
        ]);

        $remoteModel = RemoteModel::create([
            'name' => $this->faker->word,
            'cached_model_id' => $cachedModel->id
        ]);

        $this->assertTrue(
            cache()->has('CachedModel:'.$cachedModel->id)
        );

        $this->assertEquals(
            $cachedModel,
            $remoteModel->getCachedModel()
        );
    }

    private function createCachedModelsTable()
    {
        Schema::create('cached_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        return $this;
    }

    private function createRemoteModelsTable()
    {
        Schema::create('remote_models', function ($table) {
            $table->increments('id');
            $table->integer('cached_model_id')->unsigned();
            $table->string('name');
            $table->timestamps();
        });
    }
}

class CachedModel extends Model
{
    use Rememberable;

    protected $fillable = ['name'];
}

class RemoteModel extends Model
{
    use CacheReader;

    protected $fillable = ['cached_model_id', 'name'];

    public function cachedModel()
    {
        return $this->belongsTo('RemoteModel');
    }

    public function getCachedModel()
    {
        return $this->getModelFromCache(
            CachedModel::class,
            $this->cached_model_id
        );
    }
}
