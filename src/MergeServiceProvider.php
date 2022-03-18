<?php

declare(strict_types=1);

namespace Joy\VoyagerMerge;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

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
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            //
        ];
    }
}
