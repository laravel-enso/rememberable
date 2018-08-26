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

        $this->createRememberableModelsTable();

        $this->faker = Factory::create();
    }

    /** @test */
    public function adds_model_to_cache_when_creating()
    {
        $rememberableModel = $this->createRememberableModel();

        $this->assertEquals(
            $rememberableModel,
            cache()->get('RememberableModel:'.$rememberableModel->id)
        );
    }

    /** @test */
    public function updates_model_in_cache_when_updating()
    {
        $rememberableModel = $this->createRememberableModel();

        $rememberableModel->name = 'Updated';

        $rememberableModel->save();

        $this->assertEquals(
            'Updated',
            cache()->get('RememberableModel:'.$rememberableModel->id)->name
        );
    }

    /** @test */
    public function removes_model_from_cache_when_deleting()
    {
        $rememberableModel = $this->createRememberableModel();

        $rememberableModel->delete();

        $this->assertFalse(
            cache()->has('RememberableModel'.$rememberableModel->id)
        );
    }

    private function createRememberableModel()
    {
        return RememberableModel::create([
            'name' => $this->faker->word
        ]);
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
