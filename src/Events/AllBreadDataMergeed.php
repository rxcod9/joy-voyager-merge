<?php

namespace Joy\VoyagerMerge\Events;

use Illuminate\Queue\SerializesModels;

class AllBreadDataMergeed
{
    use SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
