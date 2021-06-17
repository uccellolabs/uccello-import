<div>
    <x-md-vertical-step-card title="{{ trans('import::import.label.upload_file') }}" step="0" closed="{{ $step > 0 }}">
        <div class="p-12 @if($step > 0)hidden @endif">
            <input type="file"
                wire:model="file"
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">

            @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
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
