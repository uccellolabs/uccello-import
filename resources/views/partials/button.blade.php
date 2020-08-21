@if (auth()->user()->canCreate($domain, $module))
<a class="btn-floating btn-small waves-effect green modal-trigger"
    href="#import-modal"
    data-tooltip="{{ trans('import::import.button.import') }}"
    data-position="top">
    <i class="material-icons">cloud_upload</i>
</a>
@endif
