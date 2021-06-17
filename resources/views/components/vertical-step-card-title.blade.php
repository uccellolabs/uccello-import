{{-- Title --}}
<div class="flex items-center justify-between py-4 border-b border-gray-200 border-solid cursor-pointer px-7" wire:click="changeStep({{ $step }})">
    <div class="text-2xl font-semibold">{{ $title }}</div>
    <div>
        <i class="float-right text-2xl material-icons">@if ($closed)expand_more @else expand_less @endif</i>
    </div>
</div>
