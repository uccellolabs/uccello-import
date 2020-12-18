<div>
    <div class="config">
        <input type="hidden" name="config[]">
    </div>

    <div class="default">
        <div class="form-group">
            <div class="form-line">
                <?php
                    $autocompleteSearch = false;

                    $entities = [];

                    if (isset($field->data->autocomplete_search) && $field->data->autocomplete_search === true) {
                        $autocompleteSearch = true;
                    } else {
                        $entities = auth()->user()->getAllowedGroupsAndUsers($domain, false);
                    }
                ?>
                <select name="defaults[]">
                    <option value=""></option>
                    <option value="me">{{ uctrans('me', $module) }}</option>
                    @foreach ($entities as $entity)
                        @continue($entity['uuid'] === auth()->user()->uuid)
                        <option value="{{ $entity['uuid'] }}">{{ $entity['recordLabel'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
