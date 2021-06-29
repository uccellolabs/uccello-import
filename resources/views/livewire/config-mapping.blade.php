<div>
    @if ($step >= 1)
    <x-md-vertical-step-card title="{{ trans('import::import.label.config_mapping') }}" step="1" closed="{{ $step > 1 }}">
        <div class="p-12 @if($step > 1)hidden @endif">
            <div class="flex flex-col">
                <div class="grid grid-cols-3 font-semibold">
                    <div>Header</div>
                    <div>First row</div>
                    <div>Field</div>
                </div>

                @if ($headings && $firstRow)
                    @foreach ($headings as $i => $heading)
                    <div class="grid grid-cols-3 px-2 py-3">
                        <div>{{ $heading }}</div>
                        <div>{{ !empty($firstRow[$i]) ? $firstRow[$i] : '' }}</div>
                        <div>
                            <select class="px-2 py-1 bg-gray-100 border border-gray-200 border-solid rounded-lg browser-default h-9 focus:outline-none" wire:model="config.fields.{{ $i }}.field" wire:change="filterFieldsAndShowOptions({{ count($headings) }}, {{ $i }})">
                                <option value=""></option>
                                @foreach ($fields as $field)
                                    @continue(in_array($field->name, $filteredFields) && (empty($config['fields'][$i]) || $field->name != $config['fields'][$i]['field']))
                                    @continue(!$field->isCreateable())
                                    <option value="{{ $field->name }}">
                                        @if ($field->required)<span class="text-red-500">*</span>@endif
                                        {{ uctrans('field.'.$field->name, $module) }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- <div class="relative w-5/6" x-data="{open:false}" x-on:click.away="open=false">
                                <div class="flex flex-row p-2 bg-gray-100 border border-gray-200 rounded">
                                    <div class="flex flex-col w-full cursor-pointer" x-on:click="open=!open">
                                        <div class="flex items-center justify-between w-full">
                                            <div class="flex items-center w-full">My workspace</div>
                                            <div class="mr-1 text-primary-900"  :class="{'transform -rotate-90 duration-300':open===true, 'transform rotate-90 duration-300':open===false}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute z-10 w-full p-1 text-sm bg-white border border-gray-200 rounded-b-md" x-show="open">
                                    <div class="flex flex-col w-full">
                                        <div class="flex flex-row items-center p-2 mx-1 cursor-pointer hover:bg-gray-100">Market team</div>
                                        <div class="flex flex-row items-center p-2 mx-1 cursor-pointer hover:bg-gray-100">Market team</div>
                                        <div class="flex flex-row items-center p-2 mx-1 cursor-pointer hover:bg-gray-100">Market team</div>
                                        <div class="flex flex-row items-center p-2 mx-1 cursor-pointer hover:bg-gray-100">Market team</div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    @if (!empty($config['fields'][$i]['options']))
                    <div class="p-5 mb-4 rounded-lg bg-primary-500 bg-opacity-10">
                        <div class="grid grid-cols-3 mb-3">
                            @foreach ($config['fields'][$i]['options'] as $option)
                                @php($option = (object) $option)

                                {{-- Input --}}
                                @if(in_array($option->type, ['text', 'email', 'number', 'password']))
                                    <div class="flex flex-col ml-4">
                                        <div class="mb-2 text-sm">{{ $option->label ?? '' }}</div>
                                        <div class="bg-gray-100 border rounded-md border-primary-500">
                                            <input type="{{ $option->type }}"
                                                class="w-full px-3 py-2 bg-transparent browser-default"
                                                @if (!empty($option->placeholder))placeholder="{{ $option->placeholder }}"@endif
                                                wire:model="config.fields.{{ $i }}.data.{{ $option->key }}">
                                        </div>
                                    </div>
                                {{-- Boolean --}}
                                @elseif($option->type === 'boolean')
                                    <div class="flex flex-col ml-4">
                                        <div class="mb-2 text-sm">{{ $option->label ?? '' }}</div>
                                        <div class="h-10 pt-1 switch">
                                            <label>
                                            <input type="checkbox" wire:model="config.fields.{{ $i }}.data.{{ $option->key }}">
                                            <span class="lever" style="margin-left: 0; margin-right: 8px"></span>
                                            {{ trans('module-designer::ui.block.config_columns.yes') }}
                                            </label>
                                        </div>
                                    </div>
                                {{-- Select --}}
                                @elseif($option->type === 'select')
                                    <div class="flex flex-col ml-4">
                                        <div class="mb-2 text-sm">{{ $option->label ?? '' }}</div>
                                        <div class="bg-gray-100 border rounded-md border-primary-500">
                                            <select class="w-full h-10 px-3 bg-transparent rounded-md browser-default"
                                                wire:model="config.fields.{{ $i }}.data.{{ $option->key }}"
                                                @if ($option->altersDynamicFields ?? false)wire:change="reloadFieldOptions({{ $index }})"@endif>
                                                @foreach ($option->choices as $choice)
                                                    @php($choice = (object) $choice)
                                                    <option value="{{ $choice->value }}">{{ $choice->label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                {{-- Array --}}
                                @elseif($option->type === 'array')
                                    @php ($fieldData = (array) $field->data)
                                    @if (!empty($fieldData[$option->key]))
                                        @foreach ($fieldData[$option->key] as $rowIndex => $row)
                                            <div class="flex flex-col ml-4">
                                                <div class="mb-2 text-sm">{{ trans('module-designer::ui.block.config_columns.array_value') }}</div>
                                                <div class="bg-gray-100 border rounded-md rounded-lg border-primary-500">
                                                    <input type="text"
                                                        class="w-full px-3 py-2 bg-transparent browser-default"
                                                        wire:model="config.fields.{{ $i }}.data.{{ $option->key }}.{{ $rowIndex }}.value">
                                                </div>
                                            </div>
                                            <div class="flex flex-col ml-4">
                                                <div class="mb-2 text-sm">{{ trans('module-designer::ui.block.config_columns.array_label') }}</div>
                                                <div class="bg-gray-100 border rounded-md rounded-lg border-primary-500">
                                                    <input type="text"
                                                        class="w-full px-3 py-2 bg-transparent browser-default"
                                                        wire:model="config.fields.{{ $i }}.data.{{ $option->key }}.{{ $rowIndex }}.label">
                                                </div>
                                            </div>
                                            <div class="flex items-center ml-4">
                                                @if (count($fieldData[$option->key]) > 1)
                                                <a class="px-2 py-1 text-center text-white cursor-pointer red rounded-xl" wire:click="deleteRowFromFieldOptionArray('{{ $field->name }}', '{{ $option->key }}', {{ $rowIndex }})">
                                                    <i class="text-base material-icons">delete</i>
                                                </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    <a class="w-12 px-2 py-1 mt-4 ml-4 text-center text-white cursor-pointer primary rounded-xl" wire:click="addRowToFieldOptionArray('{{ $field->name }}', '{{ $option->key }}')">
                                        <i class="text-base material-icons">add</i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Separator --}}
        <div class="absolute z-10 grid w-2/3 -bottom-14">
            <img src="{{ ucasset('img/step-link.png', 'uccello/import') }}" width="20" class="justify-self-center">
            <button class="absolute z-20 grid w-40 h-12 justify-items-center focus:outline-none text-white font-semibold items-center cursor-pointer justify-self-center primary rounded-xl -bottom-7 @if($step > 2)hidden @endif"
                wire:click="launchImport">
                {{ trans('import::import.button.import') }}
            </button>
        </div>
    </x-md-vertical-step-card>
    @endif
</div>
