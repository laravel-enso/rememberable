<?php

namespace LaravelEnso\Rememberable;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->load()
            ->publish();
    }

    private function load()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rememberable.php', 'enso.rememberable');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path('enso'),
        ], ['rememberable-config', 'enso-config']);
    }
}
