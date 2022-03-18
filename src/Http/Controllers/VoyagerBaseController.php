<?php

namespace Joy\VoyagerMerge\Http\Controllers;

use Joy\VoyagerMerge\Http\Traits\MergeAction;
use TCG\Voyager\Http\Controllers\VoyagerBaseController as TCGVoyagerBaseController;

class VoyagerBaseController extends TCGVoyagerBaseController
{
    use MergeAction;
}
