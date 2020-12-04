<div>
    <div class="config">
        <input type="hidden" name="config[]">
    </div>

    <div class="default">
        <div class="form-group">
            <div class="form-line">
                <select name="defaults[]">
                    <option value=""></option>
                    @if ($field->data->choices ?? false)
                        @foreach ($field->data->choices as $choice)
                            <option value="{{ $choice }}">{{ uctrans($choice, $module) }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
