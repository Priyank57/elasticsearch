<?php

namespace Basemkhirat\Elasticsearch;

use Elasticsearch\ClientBuilder as ElasticBuilder;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class ElasticsearchServiceProvider extends ServiceProvider
{

    function __construct()
    {
        $this->path = dirname(__FILE__);
        $this->app = app();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->mergeConfigFrom(
            $this->path . '/config/es.php', 'es'
        );

        $this->publishes([
            $this->path . '/config/' => config_path(),
        ], "es.config");


        // Resolve Laravel Scout engine.

        resolve(EngineManager::class)->extend('es', function () {

            $config = config('es.connections.' . config('scout.es.connection'));

            return new ScoutEngine(
                ElasticBuilder::create()->setHosts($config["servers"])->build(),
                $config["index"]
            );

        });


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // Register laravel scout service provider.

        $this->app->register("Laravel\\Scout\\ScoutServiceProvider");

        $this->app->bind('es', function () {
            return new Connection();
        });
    }
}
