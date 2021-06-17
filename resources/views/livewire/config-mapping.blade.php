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
                    <div class="grid grid-cols-3 px-2 py-3 hover:bg-blue-50">
                        <div>{{ $heading }}</div>
                        <div>{{ !empty($firstRow[$i]) ? $firstRow[$i] : '' }}</div>
                        <div>
                            <select class="px-2 py-1 bg-gray-100 border border-gray-200 border-solid rounded-lg browser-default h-9 focus:outline-none" wire:model="config.{{ $i }}.field" wire:change="filterFields({{ count($headings) }})">
                                <option value=""></option>
                                @foreach ($fields as $field)
                                    @continue(in_array($field->name, $filteredFields) && (empty($config[$i]) || $field->name != $config[$i]['field']))
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
                    @endforeach
                @endif

            </div>
        </div>

        {{-- Separator --}}
        <div class="absolute z-10 grid w-2/3 -bottom-14">
            <img src="{{ ucasset('img/step-link.png', 'uccello/import') }}" width="20" class="justify-self-center">
            <button class="absolute z-20 grid w-40 h-12 justify-items-center text-white font-semibold items-center cursor-pointer justify-self-center primary rounded-xl -bottom-7 @if($step > 1)hidden @endif"
                wire:click="launchImport">
                {{ trans('import::import.button.import') }}
            </button>
        </div>
    </x-md-vertical-step-card>
    @endif
</div>
