<?php

namespace Joy\VoyagerMerge\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;
use Maatwebsite\Excel\Excel;

class MergeAction extends AbstractAction
{
    /**
     * Optional mimes
     */
    protected $mimes;

    /**
     * Optional File Path
     */
    protected $filePath;

    /**
     * Optional Disk
     */
    protected $disk;

    /**
     * Optional Reader Type
     */
    protected $readerType;

    public function getTitle()
    {
        return __('joy-voyager-merge::generic.bulk_merge');
    }

    public function getIcon()
    {
        return 'voyager-upload';
    }

    public function getPolicy()
    {
        return 'browse';
    }

    public function getAttributes()
    {
        return [
            'id'    => 'bulk_merge_btn',
            'class' => 'btn btn-primary',
        ];
    }

    public function getDefaultRoute()
    {
        // return route('my.route');
    }

    public function shouldActionDisplayOnDataType()
    {
        return config('joy-voyager-merge.enabled', true) !== false
            && isInPatterns(
                $this->dataType->slug,
                config('joy-voyager-merge.allowed_slugs', ['*'])
            )
            && !isInPatterns(
                $this->dataType->slug,
                config('joy-voyager-merge.not_allowed_slugs', [])
            );
    }

    public function massAction($ids, $comingFrom)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug(request());

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Gate::authorize('browse', app($dataType->model_name));

        $mimes = $this->mimes ?? config('joy-voyager-merge.allowed_mimes');

        $validator = Validator::make(request()->all(), [
            'file' => 'required|mimes:' . $mimes,
        ]);

        if ($validator->fails()) {
            return redirect($comingFrom)->with([
                'message'    => $validator->errors()->first(),
                'alert-type' => 'error',
            ]);
        }

        $disk = $this->disk ?? config('joy-voyager-merge.disk');
        // @FIXME let me auto detect OR NOT??
        $readerType = null; //$this->readerType ?? config('joy-voyager-merge.readerType', Excel::XLSX);

        $mergeClass = 'joy-voyager-merge.merge';

        if (app()->bound("joy-voyager-merge.$slug.merge")) {
            $mergeClass = "joy-voyager-merge.$slug.merge";
        }

        $merge = app($mergeClass);

        $merge->set(
            $dataType,
            request()->all(),
        )->merge(
            request()->file('file'),
            $disk,
            $readerType
        );

        return redirect($comingFrom)->with([
            'message'    => __('joy-voyager-merge::generic.successfully_mergeed') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    public function view()
    {
        $view = 'joy-voyager-merge::bread.merge';

        if (view()->exists('joy-voyager-merge::' . $this->dataType->slug . '.merge')) {
            $view = 'joy-voyager-merge::' . $this->dataType->slug . '.merge';
        }
        return $view;
    }

    protected function getSlug(Request $request)
    {
        if (isset($this->slug)) {
            $slug = $this->slug;
        } else {
            $slug = explode('.', $request->route()->getName())[1];
        }

        return $slug;
    }
}
