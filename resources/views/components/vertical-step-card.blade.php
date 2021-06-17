{{-- Card --}}
<div class="relative mb-11">
    <div class="w-2/3 mx-auto bg-white border border-gray-200 border-solid justify-self-center rounded-xl">
        <x-md-vertical-step-card-title :title="$title" :step="$step" :closed="$closed"></x-md-vertical-step-card-title>

        {{-- Content --}}
        {{ $slot }}

        {{ $after }}
    </div>
</div>
