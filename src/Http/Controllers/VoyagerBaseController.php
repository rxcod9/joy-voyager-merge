<?php

namespace Joy\VoyagerMerge\Http\Controllers;

use Joy\VoyagerMerge\Http\Traits\MergeAction;
use Joy\VoyagerMerge\Http\Traits\MergeAllAction;
use Joy\VoyagerMerge\Http\Traits\MergeAllTemplateAction;
use Joy\VoyagerMerge\Http\Traits\MergeTemplateAction;
use TCG\Voyager\Http\Controllers\VoyagerBaseController as TCGVoyagerBaseController;

class VoyagerBaseController extends TCGVoyagerBaseController
{
    use MergeAction;
    use MergeAllAction;
    use MergeTemplateAction;
    use MergeAllTemplateAction;
}
