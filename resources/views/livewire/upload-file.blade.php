<div>
    <x-md-vertical-step-card title="{{ trans('import::import.label.upload_file') }}" step="0" closed="{{ $step > 0 }}">
        <div class="p-12 @if($step > 0)hidden @endif">
            <div>
                <input type="file"
                    wire:model="file"
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">

                @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6">
                Ligne d'entête ?
                <div class="flex flex-row mb-3 cursor-pointer" wire:click="toggleWithHeader()">
                    <div class="flex items-center">
                        <div class="relative flex items-center w-10 h-5 transition duration-200 ease-linear border-2 rounded-full @if($config['withHeader']) border-primary-900 @else border-gray-400 @endif">
                            <div class="absolute w-3 h-3 transition duration-100 ease-linear transform rounded-full cursor-pointer left-1 @if($config['withHeader']) translate-x-4 bg-primary-900 @else translate-x-0 bg-gray-400 @endif"></div>
                        </div>
                    </div>
                    <div class="ml-2">{{ trans('module-designer::ui.block.config_module.yes') }}</div>
                </div>
            </div>

            <div class="mt-6">
                Première ligne à importer
                <input type="number" wire:model="config.firstRow">

                @error('config.firstRow') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Separator --}}
        <div class="absolute z-10 grid w-2/3 -bottom-14">
            <img src="{{ ucasset('img/step-link.png', 'uccello/import') }}" width="20" class="justify-self-center">
            <button class="absolute z-20 grid w-40 h-12 justify-items-center text-white font-semibold items-center cursor-pointer justify-self-center primary rounded-xl -bottom-7 @if($step > 0)hidden @endif"
                wire:click="uploadFile">
                {{ trans('import::import.button.continue') }}
            </button>
        </div>
    </x-md-vertical-step-card>
</div>
