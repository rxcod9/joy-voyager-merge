<?php

namespace Joy\VoyagerMerge\Events;

use Illuminate\Queue\SerializesModels;
use TCG\Voyager\Models\DataType;

class BreadDataMerged
{
    use SerializesModels;

    public $dataType;

    public $data;

    public $merged;

    public function __construct(DataType $dataType, $data, $merged)
    {
        $this->dataType = $dataType;

        $this->data = $data;

        $this->merged = $merged;

        event(new BreadDataChanged($dataType, $data, 'Merged'));
    }
}
