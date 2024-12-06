<div class="flex flex-col items-center justify-center h-screen">

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <label for="prize">Select a Prize:</label>
        <select id="prize" wire:model="prizeId">
            <option value="" selected>Select a Prize</option>
            @foreach($prizes as $prize)
                <option value="{{ $prize->id }}">{{ $prize->name }} ({{ $prize->quantity }} left)</option>
            @endforeach
        </select>
    </div>

    <div wire:stream="winner" class="rounded m-10 h-20 text-2xl font-bold">
        {{ $winner?->first_name ?? 'Click the raffle button' }}
    </div>

    <button class="rounded border-2 border-blue-400 p-2 hover:bg-blue-400" wire:click="raffle">
        {{ $buttonMessage }}
    </button>

</div>
