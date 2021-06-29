<div wire:poll.3s>
    @if ($importData)
        Total : {{ $importData['imported'] }} / {{ $importData['total'] }}<br>
        Success : {{ $importData['success']}}<br>
        Error : {{ $importData['error']}}<br>
        Current time: {{ now() }}
    @endif
</div>
