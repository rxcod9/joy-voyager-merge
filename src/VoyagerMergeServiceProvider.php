<?php

declare(strict_types=1);

namespace Joy\VoyagerMerge;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Joy\VoyagerMerge\Console\Commands\AllDataTypesMerge;
use Joy\VoyagerMerge\Console\Commands\AllDataTypesTemplateExport;
use Joy\VoyagerMerge\Console\Commands\DataTypeMerge;
use Joy\VoyagerMerge\Console\Commands\DataTypeTemplateExport;
use TCG\Voyager\Facades\Voyager;

/**
 * Class VoyagerMergeServiceProvider
 *
 * @category  Package
 * @package   JoyVoyagerMerge
 * @author    Ramakant Gangwar <gangwar.ramakant@gmail.com>
 * @copyright 2021 Copyright (c) Ramakant Gangwar (https://github.com/rxcod9)
 * @license   http://github.com/rxcod9/joy-voyager-merge/blob/main/LICENSE New BSD License
 * @link      https://github.com/rxcod9/joy-voyager-merge
 */
class VoyagerMergeServiceProvider extends ServiceProvider
{
    /**
     * Boot
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(\Joy\VoyagerMerge\Actions\MergeAction::class);

        $this->registerPublishables();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'joy-voyager-merge');

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'joy-voyager-merge');
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(__DIR__ . '/../routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix(config('joy-voyager-merge.route_prefix', 'api'))
            ->middleware('api')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/voyager-merge.php', 'joy-voyager-merge');

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Register publishables.
     *
     * @return void
     */
    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__ . '/../config/voyager-merge.php' => config_path('joy-voyager-merge.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views'                => resource_path('views/vendor/joy-voyager-merge'),
            __DIR__ . '/../resources/views/bread/partials' => resource_path('views/vendor/voyager/bread/partials'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/views/bread/partials' => resource_path('views/vendor/voyager/bread/partials'),
        ], 'voyager-actions-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/joy-voyager-merge'),
        ], 'translations');
    }

    protected function registerCommands(): void
    {
        $this->app->singleton('command.joy-voyager.merge-template', function () {
            return new DataTypeTemplateExport();
        });

        $this->app->singleton('command.joy-voyager.merge-all-template', function () {
            return new AllDataTypesTemplateExport();
        });

        $this->app->singleton('command.joy-voyager.merge', function () {
            return new DataTypeMerge();
        });

        $this->app->singleton('command.joy-voyager.merge-all', function () {
            return new AllDataTypesMerge();
        });

        $this->commands([
            'command.joy-voyager.merge-template',
            'command.joy-voyager.merge-all-template',
            'command.joy-voyager.merge',
            'command.joy-voyager.merge-all'
        ]);
    }
}
