<?php

namespace HillTech\NovaKeyValueTinyMCE;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;
use HillTech\NovaKeyValueTinyMCE\Console\SupportFileManagerCommand;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__ . '/../dist/tinymce') => public_path('vendor/tinymce'),
        ], 'public');

        $this->publishes([
            __DIR__.'/../config/nova-keyvaluetinymce.php' => config_path('nova-keyvaluetinymce.php'),
        ], 'config');

        $this->publishes([__DIR__.'/../dist/tinymce' => public_path('vendor/tinymce')], ['nova-assets', 'laravel-assets']);

        Nova::serving(function (ServingNova $event) {
            Nova::script('Nova-KeyValueTinyMCE-tinymce', __DIR__.'/../dist/js/tinymce.js');
            Nova::script('Nova-KeyValueTinyMCE', __DIR__.'/../dist/js/field.js');
            Nova::style('Nova-KeyValueTinyMCE', __DIR__.'/../dist/css/field.css');
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                SupportFileManagerCommand::class
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nova-keyvaluetinymce.php', 'nova-keyvaluetinymce');
    }
}
