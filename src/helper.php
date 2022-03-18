<?php

use Illuminate\Support\Str;
use TCG\Voyager\Models\DataType;

// if (! function_exists('joyVoyagerMerge')) {
//     /**
//      * Helper
//      */
//     function joyVoyagerMerge($argument1 = null)
//     {
//         //
//     }
// }

if (!function_exists('isInPatterns')) {
    /**
     * Helper
     */
    function isInPatterns($key, $patterns)
    {
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $key)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('removeRelationshipField')) {
    function removeRelationshipField(DataType $dataType, $bread_type = 'browse')
    {
        $forget_keys = [];
        foreach ($dataType->{$bread_type . 'Rows'} as $key => $row) {
            if ($row->type == 'relationship') {
                if ($row->details->type == 'belongsTo') {
                    $relationshipField = @$row->details->column;
                    $keyInCollection   = key($dataType->{$bread_type . 'Rows'}->where('field', '=', $relationshipField)->toArray());
                    array_push($forget_keys, $keyInCollection);
                }
            }
        }

        foreach ($forget_keys as $forget_key) {
            $dataType->{$bread_type . 'Rows'}->forget($forget_key);
        }

        // Reindex collection
        $dataType->{$bread_type . 'Rows'} = $dataType->{$bread_type . 'Rows'}->values();
    }
}
