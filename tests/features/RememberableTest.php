<?php

use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\RememberableModels\app\Traits\Rememberable;
use Tests\TestCase;

class RememberableTest extends TestCase
{
    private $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();
        $this->createRememberableModelsTable();
    }

    /** @test */
    public function adds_model_to_cache_when_creating()
    {
        $rememberableModel = RememberableModel::create(['name' => $this->faker->word]);

        $this->assertEquals($rememberableModel, cache()->get('RememberableModel:'.$rememberableModel->id));
    }

    /** @test */
    public function updated_model_in_cache_when_updating()
    {
        $rememberableModel = RememberableModel::create(['name' => $this->faker->word]);

        $rememberableModel->name = 'Updated';
        $rememberableModel->save();

        $this->assertTrue(cache()->get('RememberableModel:'.$rememberableModel->id)->name === 'Updated');
    }

    /** @test */
    public function remove_model_from_cache_when_deleting()
    {
        $rememberableModel = RememberableModel::create(['name' => $this->faker->word]);

        $rememberableModel->delete();

        $this->assertFalse(cache()->has('RememberableModel'.$rememberableModel->id));
    }

    private function createRememberableModelsTable()
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

    protected $fillable = ['name'];
}
