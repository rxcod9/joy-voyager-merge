@php
    $edit = true;
    $add  = false;
    $mergeDataTypeContent = new $dataType->model_name();
    removeRelationshipField($dataType, 'edit');
    $hash = md5(get_class($action));
@endphp

<!-- <form method="post" action="{{ route('voyager.'.$dataType->slug.'.action') }}" style="display:inline">
    {{ csrf_field() }}
    <button type="submit" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i>  <span class="hidden-xs hidden-sm">{{ $action->getTitle() }}</span></button>
    <input type="hidden" name="action" value="{{ get_class($action) }}">
    <input type="hidden" name="ids" value="" class="selected_ids">
</form> -->
<a class="btn btn-info" id="merge_btn{{ $hash }}" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i> <span>{{ $action->getTitle() }}</span></a>

@push('javascript')
<script>
$(function() {
    // Bulk bulk update selectors
    var $mergeBtn = $('#merge_btn{{ $hash }}');
    // Bulk bulk update listener
    $mergeBtn.click(function () {
        var ids = [];
        var items = [];
        var $checkedBoxes = $('#dataTable input[type=checkbox]:checked').not('.select_all');
        var count = $checkedBoxes.length;
        var i = 0;
        if (count >= 2) {
            // Gather IDs
            // Gather items
            $.each($checkedBoxes, function () {
                var value = $(this).val();
                ids.push(value);
                i++;
            });
            const id = ids.shift();

            window.location.href= '{{ route('voyager.'.$dataType->slug.'.merge', '__id') }}'.replace('__id', id) + '?' + ids.map(function(el, idx) {
                return 'ids[' + idx + ']=' + el;
            }).join('&');
        } else {
            // No row selected
            toastr.warning('{{ __('joy-voyager-merge::generic.merge_atleast', ['number' => 2]) }}');
        }
    });
});
</script>
@endpush
