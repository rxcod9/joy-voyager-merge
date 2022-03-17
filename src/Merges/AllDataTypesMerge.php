<?php

namespace Joy\VoyagerMerge\Merges;

use Joy\VoyagerMerge\Events\AllBreadDataMergeed;
use Maatwebsite\Excel\Concerns\Mergeable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Events\AfterMerge;
use TCG\Voyager\Facades\Voyager;

class AllDataTypesMerge implements
    WithMultipleSheets,
    WithProgressBar,
    WithEvents
{
    use Mergeable;

    /**
     * The input.
     *
     * @var array
     */
    protected $input = [];

    /**
     * @param array $input
     */
    public function set(
        $input = []
    ) {
        $this->input = $input;
        return $this;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterMerge::class => function (AfterMerge $event) {
                event(new AllBreadDataMergeed($this->input));
            },
        ];
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets    = [];
        $dataTypes = Voyager::model('DataType')->get();

        foreach ($dataTypes as $dataType) {
            $mergeClass = 'joy-voyager-merge.merge';

            if (app()->bound('joy-voyager-merge.' . $dataType->slug . '.merge')) {
                $mergeClass = 'joy-voyager-merge.' . $dataType->slug . '.merge';
            }

            $merge = app($mergeClass);

            $sheets[$dataType->getTranslatedAttribute('display_name_plural')] = $merge->set(
                $dataType,
                [],
                $this->input
            );
        }

        return $sheets;
    }
}
