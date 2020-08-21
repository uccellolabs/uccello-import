@if (auth()->user()->canCreate($domain, $module))
<div id="import-modal" class="modal">
    <form action="{{ ucroute('uccello.import.prepare', $domain, $module) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
            <h4>
                <i class="material-icons left primary-text">cloud_upload</i>
                {{ trans('import::import.modal.import.title') }}
            </h4>

            <div style="margin-top: 40px">
                <div class="file-field input-field">
                    <div class="btn primary">
                        <span><i class="material-icons">attachment</i></span>
                        <input type="file" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path" type="text" placeholder="{{ trans('import::import.label.file') }}">
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="waves-effect green-text btn-flat"> {{ trans('import::import.button.import') }}</button>
        </div>
    </form>
</div>
@endif
