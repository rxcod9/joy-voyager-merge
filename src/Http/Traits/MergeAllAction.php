<?php

namespace Joy\VoyagerMerge\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;

trait MergeAllAction
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

    public function mergeAll(Request $request)
    {
        // Check permission
        $this->authorize('browse_bread');

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

        $mergeClass = 'joy-voyager-merge.merge-all';

        $merge = app($mergeClass);

        $merge->set(
            $request->all(),
        )->merge(
            request()->file('file'),
            $disk,
            $readerType
        );

        return redirect()->back()->with([
            'message'    => __('joy-voyager-merge::generic.successfully_mergeed_all'),
            'alert-type' => 'success',
        ]);
    }
}
