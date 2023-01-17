<?php

namespace Joy\VoyagerMerge\Http\Controllers;

use Joy\VoyagerMerge\Http\Traits\MergeAction;
use Joy\VoyagerCore\Http\Controllers\VoyagerBaseController as BaseVoyagerBaseController;

class VoyagerBaseController extends BaseVoyagerBaseController
{
    use MergeAction;
}
