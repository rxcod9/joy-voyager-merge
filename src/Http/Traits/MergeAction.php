<?php

namespace Joy\VoyagerMerge\Http\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Joy\VoyagerMerge\Events\BreadDataMerged;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;

trait MergeAction
{
    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Merge an item of our Data Type BR(E)AD
    //
    //****************************************

    public function merge(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $ids  = $request->ids;

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query = $query->{$dataType->scope}();
            }

            $idsQuery         = clone $query;
            $dataTypeContent  = call_user_func([$query, 'findOrFail'], $id);
            $dataTypeContents = call_user_func([$idsQuery, 'findOrFail'], $ids);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent  = DB::table($dataType->name)->where('id', $id)->first();
            $dataTypeContents = DB::table($dataType->name)->whereIn('id', $ids)->get();
        }

        if (method_exists($dataTypeContent, 'preMerge')) {
            $dataTypeContent->preMerge($dataTypeContents);
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $view = 'joy-voyager-merge::bread.merge';

        if (view()->exists("joy-voyager-merge::$slug.merge")) {
            $view = "joy-voyager-merge::$slug.merge";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'dataTypeContents', 'id', 'ids', 'isModelTranslatable'));
    }

    // POST BR(E)AD
    public function updateMerge(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $ids  = $request->ids;

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $idsQeury = clone $query;
        $data     = $query->findOrFail($id);
        $datas    = $idsQeury->findOrFail($ids);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });
        $original_data = clone($data);

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);

        event(new BreadDataUpdated($dataType, $data));

        foreach ($datas as $datasEach) {
            $datasEach->destroy($datasEach->getKey());

            event(new BreadDataDeleted($dataType, $datasEach));
        }

        if (method_exists($data, 'postMerge')) {
            $data->postMerge($datas);
        }

        event(new BreadDataMerged($dataType, $data, $datas));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('joy-voyager-merge::generic.successfully_merged') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }
}
