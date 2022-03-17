<?php

namespace Joy\VoyagerMerge\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Facades\Voyager;
use Maatwebsite\Excel\Excel;

trait MergeAction
{
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Merge DataTable our Data Type (B)READ
    //
    //****************************************

    public function merge(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $mimes = $this->mimes ?? config('joy-voyager-merge.allowed_mimes');

        $validator = Validator::make(request()->all(), [
            'file' => 'required|mimes:' . $mimes,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with([
                'message'    => $validator->errors()->first(),
                'alert-type' => 'error',
            ]);
        }

        $disk       = $request->get('disk', config('joy-voyager-merge.disk'));
        $readerType = $request->get('readerType', config('joy-voyager-merge.readerType', Excel::XLSX));

        $mergeClass = 'joy-voyager-merge.merge';

        if (app()->bound('joy-voyager-merge.' . $dataType->slug . '.merge')) {
            $mergeClass = 'joy-voyager-merge.' . $dataType->slug . '.merge';
        }

        $merge = app($mergeClass);

        $merge->set(
            $dataType,
            $request->all(),
        )->merge(
            request()->file('file'),
            $disk,
            $readerType
        );

        return redirect()->back()->with([
            'message'    => __('joy-voyager-merge::generic.successfully_mergeed') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }
}
