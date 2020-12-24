<div>
    <div class="config">
        <script>
            function setConfigForField{{ $field->id }}() {
                var relatedField = document.getElementById("{{ $field->name }}_related_field").value;
                document.getElementById("{{ $field->name }}_config").value = JSON.stringify({related_field: relatedField});
            }
        </script>

        @php($relatedModule = ($field->data->module ?? false) ? ucmodule($field->data->module) : null)
        @if ($relatedModule)
        <select id="{{ $field->name }}_related_field"
            onchange="setConfigForField{{ $field->id }}()">
            <option value=""></option>
            @foreach ($relatedModule->fields as $relatedField)
            <option value="{{ $relatedField->name }}">{{ uctrans('field.'.$relatedField->name, $relatedModule) }}</option>
            @endforeach
        </select>
        @endif
        <input type="hidden" name="config[]" id="{{ $field->name }}_config">
    </div>

    <div class="default">
        <div class="form-group">
            <div class="form-line">
                <input name="defaults[]" type="text" autocomplete="off">
            </div>
        </div>
    </div>
</div>
