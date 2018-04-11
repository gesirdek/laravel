<?php

namespace Gesirdek\Coders;

use Gesirdek\Support\Classify;
use Gesirdek\Coders\Model\Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Gesirdek\Coders\Console\CodeModelsCommand;
use Gesirdek\Coders\Model\Factory as ModelFactory;

class CodersServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../publish/config/models.php' => config_path('models.php'),
            ], 'gesirdek-models');
            $this->publishes([
                __DIR__ . '/../../publish/js/' => resource_path('assets/js'),
            ], 'gesirdek-vue-base');
            $this->publishes([
                __DIR__ . '/../../publish/views/admin.blade.php' => resource_path('views'),
            ], 'gesirdek-views');

            $this->commands([
                CodeModelsCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerModelFactory();
    }

    /**
     * Register Model Factory.
     *
     * @return void
     */
    protected function registerModelFactory()
    {
        $this->app->singleton(ModelFactory::class, function ($app) {
            return new ModelFactory(
                $app->make('db'),
                $app->make(Filesystem::class),
                new Classify(),
                new Config($app->make('config')->get('models'))
            );
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [ModelFactory::class];
    }
}
