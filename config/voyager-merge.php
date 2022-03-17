<?php

return [

    /*
     * If enabled for voyager-merge package.
     */
    'enabled' => env('VOYAGER_MERGE_ENABLED', true),

    /*
     * If validation enabled for voyager-merge package.
     */
    'validation' => env('VOYAGER_MERGE_VALIDATION_ENABLED', false),

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
    | The default merge disk.
    */
    'disk' => env('VOYAGER_MERGE_DISK', null),

    /*
    | The default merge readerType.
    | 
    | Supported: "Xlsx", "Csv", "Ods", "Xls",
    |   "Slk", "Xml", "Gnumeric", "Html"
    */
    'readerType' => env('VOYAGER_MERGE_READER_TYPE', 'Xlsx'),

    /*
    | The default merge writerType.
    | 
    | Supported: "Xlsx", "Csv", "Csv", "Ods", "Xls",
    |   "Slk", "Xml", "Gnumeric", "Html"
    */
    'writerType' => env('VOYAGER_MERGE_WRITER_TYPE', 'Xlsx'),

    /*
    | Here you can specify which mimes are allowed to upload
    | 
    | Supported: "xlsx","csv","tsv","ods","xls","slk","xml","gnumeric","html"
    |
    */

    'allowed_mimes' => env('VOYAGER_MERGE_ALLOWED_MIMES', 'xlsx,txt,csv,tsv,ods,xls,slk,xml,gnumeric,html'),

    /*
    |--------------------------------------------------------------------------
    | Unique column config
    |--------------------------------------------------------------------------
    |
    | Here you can specify unique column settings
    | Make sure db also has unique index or primary index on that column
    | Leave null for primary key
    |
    */

    'unique_column' => [
        // 'users' => 'email',
        // 'YOUR_DATATYPE_SLUG' => 'MODEL_UNIQUE_KEY',
    ],
];
