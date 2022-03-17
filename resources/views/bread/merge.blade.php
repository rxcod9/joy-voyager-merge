<a class="btn btn-info" id="bulk_merge_btn"><i class="voyager-upload"></i> <span>{{ __('joy-voyager-merge::generic.bulk_merge') }}</span></a>

{{-- Bulk merge modal --}}
<div class="modal modal-info fade" tabindex="-1" id="bulk_merge_modal" role="dialog">
    <form action="{{ route('voyager.'.$dataType->slug.'.action') }}" id="bulk_merge_form" method="POST" enctype="multipart/form-data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="voyager-upload"></i> {{ __('joy-voyager-merge::generic.bulk_merge_title') }} <span id="bulk_merge_count"></span> <span id="bulk_merge_display_name"></span>
                </h4>
            </div>
            <div class="modal-body" id="bulk_merge_modal_body">
                {{ csrf_field() }}
                <input type="file" name="file">
            </div>
            <div class="modal-footer">
                <div class="btn-group pull-left" role="group">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="voyager-download"></i> <span>{{ __('joy-voyager-merge::generic.bulk_merge_template') }}</span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a
                                    class='export-template-by-writer'
                                    data-writer-type="Xls"
                                    href="{{ route('voyager.'.$dataType->slug.'.merge-template') }}?writerType=Xls"
                                    title="{{ __('joy-voyager-merge::generic.bulk_merge_template') }}"
                                    target="_blank"
                                >Xls</a>
                            </li>
                            <li>
                                <a
                                    class='export-template-by-writer'
                                    data-writer-type="Xlsx"
                                    href="{{ route('voyager.'.$dataType->slug.'.merge-template') }}?writerType=Xlsx"
                                    title="{{ __('joy-voyager-merge::generic.bulk_merge_template') }}"
                                    target="_blank"
                                >Xlsx</a>
                            </li>
                            <li>
                                <a
                                    class='export-template-by-writer'
                                    data-writer-type="Ods"
                                    href="{{ route('voyager.'.$dataType->slug.'.merge-template') }}?writerType=Ods"
                                    title="{{ __('joy-voyager-merge::generic.bulk_merge_template') }}"
                                    target="_blank"
                                >Ods</a>
                            </li>
                            <li>
                                <a
                                    class='export-template-by-writer'
                                    data-writer-type="Csv"
                                    href="{{ route('voyager.'.$dataType->slug.'.merge-template') }}?writerType=Csv"
                                    title="{{ __('joy-voyager-merge::generic.bulk_merge_template') }}"
                                    target="_blank"
                                >Csv</a>
                            </li>
                            <li>
                                <a
                                    class='export-template-by-writer'
                                    data-writer-type="Html"
                                    href="{{ route('voyager.'.$dataType->slug.'.merge-template') }}?writerType=Html"
                                    title="{{ __('joy-voyager-merge::generic.bulk_merge_template') }}"
                                    target="_blank"
                                >Html</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- <button type="submit" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i> {{ $action->getTitle() }}</button> -->
                <input type="hidden" name="action" value="{{ get_class($action) }}">
                <!-- <input type="hidden" name="ids" value="" class="selected_ids"> -->
                <!-- <input type="hidden" name="ids" id="bulk_merge_input" value=""> -->
                <input type="submit" class="btn btn-info pull-right merge-confirm"
                            value="{{ __('joy-voyager-merge::generic.bulk_merge_confirm') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_plural')) }}">
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
    // Bulk merge selectors
    var $bulkMergeBtn = $('#bulk_merge_btn');
    var $bulkMergeModal = $('#bulk_merge_modal');
    var $bulkMergeCount = $('#bulk_merge_count');
    var $bulkMergeDisplayName = $('#bulk_merge_display_name');
    var $bulkMergeInput = $('#bulk_merge_input');
    // Reposition modal to prevent z-index issues
    $bulkMergeModal.appendTo('body');
    // Bulk merge listener
    $bulkMergeBtn.click(function () {
        var ids = [];
        var $checkedBoxes = $('#dataTable input[type=checkbox]:checked').not('.select_all');
        var count = $checkedBoxes.length;
        // if (count) {
            // Reset input value
            $bulkMergeInput.val('');
            // Deletion info
            var displayName = count > 1 ? '{{ $dataType->getTranslatedAttribute('display_name_plural') }}' : '{{ $dataType->getTranslatedAttribute('display_name_singular') }}';
            displayName = displayName.toLowerCase();
            // $bulkMergeCount.html(count);
            $bulkMergeDisplayName.html(displayName);
            // Gather IDs
            $.each($checkedBoxes, function () {
                var value = $(this).val();
                ids.push(value);
            })
            // Set input value
            $bulkMergeInput.val(ids);
            // Show modal
            $bulkMergeModal.modal('show');
        // } else {
        //     // No row selected
        //     toastr.warning('{{ __('joy-voyager-merge::generic.bulk_merge_nothing') }}');
        // }
    });
});
</script>
@endpush
