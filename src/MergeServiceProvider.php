<?php

declare(strict_types=1);

namespace Joy\VoyagerMerge;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Joy\VoyagerMerge\Exports\AllDataTypesTemplateExport;
use Joy\VoyagerMerge\Exports\DataTypeTemplateExport;
use Joy\VoyagerMerge\Merges\AllDataTypesMerge;
use Joy\VoyagerMerge\Merges\DataTypeMerge;

/**
 * Class MergeServiceProvider
 *
 * @category  Package
 * @package   JoyVoyagerMerge
 * @author    Ramakant Gangwar <gangwar.ramakant@gmail.com>
 * @copyright 2021 Copyright (c) Ramakant Gangwar (https://github.com/rxcod9)
 * @license   http://github.com/rxcod9/joy-voyager-merge/blob/main/LICENSE New BSD License
 * @link      https://github.com/rxcod9/joy-voyager-merge
 */
class MergeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('joy-voyager-merge.merge', function ($app) {
            return new DataTypeMerge();
        });
        $this->app->bind('joy-voyager-merge.merge-template', function ($app) {
            return new DataTypeTemplateExport();
        });

        $this->app->bind('joy-voyager-merge.merge-all', function ($app) {
            return new AllDataTypesMerge();
        });
        $this->app->bind('joy-voyager-merge.merge-all-template', function ($app) {
            return new AllDataTypesTemplateExport();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'joy-voyager-merge.merge',
            'joy-voyager-merge.merge-template',
            'joy-voyager-merge.merge-all',
            'joy-voyager-merge.merge-all-template'
        ];
    }
}
