<?php

return [

    /*
     * If enabled for voyager-merge package.
     */
    'enabled' => env('VOYAGER_MERGE_ENABLED', true),

    /*
    | Here you can specify for which data type slugs merge is enabled
    | 
    | Supported: "*", or data type slugs "users", "roles"
    |
    */

    'allowed_slugs' => array_filter(explode(',', env('VOYAGER_MERGE_ALLOWED_SLUGS', '*'))),

    /*
    | Here you can specify for which data type slugs merge is not allowed
    | 
    | Supported: "*", or data type slugs "users", "roles"
    |
    */

    'not_allowed_slugs' => array_filter(explode(',', env('VOYAGER_MERGE_NOT_ALLOWED_SLUGS', ''))),

    /*
     * The config_key for voyager-merge package.
     */
    'config_key' => env('VOYAGER_MERGE_CONFIG_KEY', 'joy-voyager-merge'),

    /*
     * The route_prefix for voyager-merge package.
     */
    'route_prefix' => env('VOYAGER_MERGE_ROUTE_PREFIX', 'joy-voyager-merge'),

    /*
    |--------------------------------------------------------------------------
    | Controllers config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager controller settings
    |
    */

    'controllers' => [
        'namespace' => 'Joy\\VoyagerMerge\\Http\\Controllers',
    ],

    /*
    |--------------------------------------------------------------------------
    | DataRows config
    |--------------------------------------------------------------------------
    |
    | Here you can specify which data rows you want to bulk update
    |
    */

    'data_rows' => [
        'default' => [
            'assigned_to_id',
            'parent_id',
            'status',
        ],
        'users' => [
            'role_id'
        ],
        'posts' => [
            'status',
            'category_id',
            'featured',
        ],
    ],
];
