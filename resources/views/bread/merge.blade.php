@php
    $edit = true;
    $add  = false;
    $mergeDataTypeContent = new $dataType->model_name();
    removeRelationshipField($dataType, 'add');
    removeRelationshipField($dataType, 'edit');
    $defaultDataRows = config('joy-voyager-merge.data_rows.default');
    $dataTypeDataRows = config('joy-voyager-merge.data_rows.' . $dataType->slug, $defaultDataRows);
    $dataTypeDataRows = method_exists($action, 'rows') ? $action->rows() : $dataTypeDataRows;
    $hash = md5(get_class($action) . json_encode($dataTypeDataRows));
@endphp

<!-- <form method="post" action="{{ route('voyager.'.$dataType->slug.'.action') }}" style="display:inline">
    {{ csrf_field() }}
    <button type="submit" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i>  <span class="hidden-xs hidden-sm">{{ $action->getTitle() }}</span></button>
    <input type="hidden" name="action" value="{{ get_class($action) }}">
    <input type="hidden" name="ids" value="" class="selected_ids">
</form> -->
<a class="btn btn-info" id="merge_btn{{ $hash }}" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i> <span>{{ $action->getTitle() }}</span></a>

{{-- Bulk bulk update modal --}}
<div class="modal modal-info fade" tabindex="-1" id="merge_modal{{ $hash }}" role="dialog">
    <form action="{{ route('voyager.'.$dataType->slug.'.merge') }}" id="merge_form{{ $hash }}" method="POST" enctype="multipart/form-data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="voyager-edit"></i> {{ __('joy-voyager-merge::generic.merge_title') }} <span id="merge_count{{ $hash }}"></span> <span id="merge_display_name{{ $hash }}"></span>
                </h4>
            </div>
            <div class="modal-body" id="merge_modal_body{{ $hash }}">
                {{ csrf_field() }}
                <!-- Adding / Editing -->
                @php
                    $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )}->filter(function($row) use($dataTypeDataRows) {
                        return in_array($row->field, $dataTypeDataRows) || (
                            $row->type === 'relationship' && in_array($row->details->column, $dataTypeDataRows)
                        );
                    });
                @endphp

                @foreach($dataTypeRows as $row)
                    <!-- GET THE DISPLAY OPTIONS -->
                    @php
                        $display_options = $row->details->display ?? NULL;
                        if ($mergeDataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                            $mergeDataTypeContent->{$row->field} = $mergeDataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                        } elseif ($mergeDataTypeContent->{$row->field.'_'.($edit ? 'bulk_edit' : 'bulk_add')}) {
                            $mergeDataTypeContent->{$row->field} = $mergeDataTypeContent->{$row->field.'_'.($edit ? 'bulk_edit' : 'bulk_add')};
                        }
                    @endphp
                    @if (isset($row->details->legend) && isset($row->details->legend->text))
                        <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                    @endif

                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                        {{ $row->slugify }}
                        <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                        @include('voyager::multilingual.input-hidden-bread-edit-add', ['dataTypeContent' => $mergeDataTypeContent])
                        @if (isset($row->details->view))
                            @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $mergeDataTypeContent, 'content' => $mergeDataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                        @elseif ($row->type == 'relationship')
                            @include('voyager::formfields.relationship', ['options' => $row->details, 'dataTypeContent' => $mergeDataTypeContent])
                        @else
                            {!! app('voyager')->formField($row, $dataType, $mergeDataTypeContent) !!}
                        @endif

                        @foreach (app('voyager')->afterFormFields($row, $dataType, $mergeDataTypeContent) as $after)
                            {!! $after->handle($row, $dataType, $mergeDataTypeContent) !!}
                        @endforeach
                        @if ($errors->has($row->field))
                            @foreach ($errors->get($row->field) as $error)
                                <span class="help-block">{{ $error }}</span>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <!-- <button type="submit" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i> <span class="hidden-xs hidden-sm">{{ $action->getTitle() }}</span></button> -->
                <input type="hidden" name="action" value="{{ get_class($action) }}">
                <input type="hidden" name="ids" id="merge_input{{ $hash }}" value="">
                @foreach($dataTypeDataRows as $dataTypeDataRow)
                    <input type="hidden" name="rows[]" value="{{ $dataTypeDataRow }}">
                @endforeach
                <input type="submit" class="btn btn-info pull-right merge-confirm"
                            value="{{ __('joy-voyager-merge::generic.merge_confirm') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_plural')) }}">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">
                    {{ __('voyager::generic.cancel') }}
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </form>
</div><!-- /.modal -->

@push('javascript')
<script>
$(function() {
    // Bulk bulk update selectors
    var $mergeBtn = $('#merge_btn{{ $hash }}');
    var $mergeModal = $('#merge_modal{{ $hash }}');
    var $mergeCount = $('#merge_count{{ $hash }}');
    var $mergeDisplayName = $('#merge_display_name{{ $hash }}');
    var $mergeInput = $('#merge_input{{ $hash }}');
    // Reposition modal to prevent z-index issues
    $mergeModal.appendTo('body');
    // Bulk bulk update listener
    $mergeBtn.click(function () {
        var ids = [];
        var $checkedBoxes = $('#dataTable input[type=checkbox]:checked').not('.select_all');
        var count = $checkedBoxes.length;
        if (count) {
            // Reset input value
            $mergeInput.val('');
            // Deletion info
            var displayName = count > 1 ? '{{ $dataType->getTranslatedAttribute('display_name_plural') }}' : '{{ $dataType->getTranslatedAttribute('display_name_singular') }}';
            displayName = displayName.toLowerCase();
            $mergeCount.html(count);
            $mergeDisplayName.html(displayName);
            // Gather IDs
            $.each($checkedBoxes, function () {
                var value = $(this).val();
                ids.push(value);
            })
            // Set input value
            $mergeInput.val(ids);
            // Show modal
            $mergeModal.modal('show');
        } else {
            // No row selected
            toastr.warning('{{ __('joy-voyager-merge::generic.merge_nothing') }}');
        }
    });
});
</script>
@endpush
