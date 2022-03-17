<?php

namespace Joy\VoyagerMerge\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

trait MergeAllTemplateAction
{
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      MergeAllTemplate DataTable our Data Type (B)READ
    //
    //****************************************

    public function mergeTemplateAll(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // Check permission
        $this->authorize('browse_bread');

        $writerType = $this->writerType ?? config('joy-voyager-merge.writerType', Excel::XLSX);
        $fileName   = $this->fileName ?? ('merge-all' . '.' . Str::lower($writerType));

        $exportClass = 'joy-voyager-merge.merge-all-template';

        $export = app($exportClass);

        return $export->set(
            $request->all(),
        )->download(
            $fileName,
            $writerType
        );
    }
}
