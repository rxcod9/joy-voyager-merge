<?php

namespace Joy\VoyagerMerge\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;

class MergeAction extends AbstractAction
{
    /**
     * Optional rows
     */
    protected $rows;

    public function getTitle()
    {
        return __('joy-voyager-merge::generic.merge');
    }

    public function getIcon()
    {
        return 'fa fa-lock';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'id'     => 'merge_btn',
            'class'  => 'btn btn-info',
            'target' => '_blank',
        ];
    }

    public function getDefaultRoute()
    {
        // return route('my.route');
    }

    public function shouldActionDisplayOnDataType()
    {
        if (config('joy-voyager-merge.enabled', true) !== true) {
            return false;
        }
        if (!isInPatterns(
            $this->dataType->slug,
            config('joy-voyager-merge.allowed_slugs', ['*'])
        )) {
            return false;
        }
        if (isInPatterns(
            $this->dataType->slug,
            config('joy-voyager-merge.not_allowed_slugs', [])
        )) {
            return false;
        }

        $defaultDataRows  = config('joy-voyager-merge.data_rows.default');
        $dataTypeDataRows = config('joy-voyager-merge.data_rows.' . $this->dataType->slug, $defaultDataRows);
        $dataTypeDataRows = $this->rows ?? $dataTypeDataRows;

        $dataTypeRows = $this->dataType->editRows->filter(function ($row) use ($dataTypeDataRows) {
            return in_array($row->field, $dataTypeDataRows) || (
                $row->type === 'relationship' && in_array($row->details->column, $dataTypeDataRows)
            );
        });

        return $dataTypeRows->count() > 0;
    }

    public function massAction($ids, $comingFrom)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug(request());

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Gate::authorize('edit', app($dataType->model_name));

        return redirect()->back()->with([
            'message'    => __('voyager::generic.successfully_updated') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
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

    public function rows()
    {
        if ($this->rows) {
            return $this->rows;
        }

        $defaultDataRows  = config('joy-voyager-merge.data_rows.default');
        $dataTypeDataRows = config('joy-voyager-merge.data_rows.' . $this->dataType->slug, $defaultDataRows);

        $dataTypeRows = $this->dataType->editRows->filter(function ($row) use ($dataTypeDataRows) {
            return in_array($row->field, $dataTypeDataRows) || (
                $row->type === 'relationship' && in_array($row->details->column, $dataTypeDataRows)
            );
        });

        return $dataTypeRows->pluck('field')->toArray();
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
