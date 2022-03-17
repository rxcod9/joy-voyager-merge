<?php

namespace Joy\VoyagerMerge\Http\Traits;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

trait MergeTemplateAction
{
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      MergeTemplate DataTable our Data Type (B)READ
    //
    //****************************************

    public function mergeTemplate(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $writerType = $request->get('writerType', $this->writerType ?? config('joy-voyager-merge.writerType', Excel::XLSX));
        $fileName   = $this->fileName ?? ($dataType->slug . '.' . Str::lower($writerType));

        $exportClass = 'joy-voyager-merge.merge-template';

        if (app()->bound('joy-voyager-merge.' . $dataType->slug . '.merge-template')) {
            $exportClass = 'joy-voyager-merge.' . $dataType->slug . '.merge-template';
        }

        $export = app($exportClass);

        return $export->set(
            $dataType,
            [],
            $request->all(),
        )->download(
            $fileName,
            $writerType
        );
    }
}
