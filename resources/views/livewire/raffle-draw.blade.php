<div class="flex flex-col items-center justify-center h-screen relative" style="background: url('{{ asset('images/bg.png') }}') center/cover no-repeat; ">

    <div class="absolute inset-0 bg-gradient-to-b from-black/5 to-black/30 z-0"></div>
    <div class="relative z-10">


    

    <div wire:stream="winner" class="rounded m-10 h-32 flex items-center justify-center text-7xl font-extrabold text-white bg-black/50 shadow-xl  px-8">
        {{ $winner?->first_name ?? '🎉 Click the Raffle Button to Begin! 🎉' }}
    </div>

    <div>
        
   
<div class="flex flex-col items-center space-y-4 mt-24">
   

    <!-- Prize Dropdown -->
    <div class="flex flex-col items-center">
        <label for="prize" class="text-white text-lg font-semibold mb-2">Select a Prize:</label>
        <select 
            id="prize" 
            wire:model="prizeId"
            class="p-3 bg-gray-800 text-white border border-gray-400 rounded focus:outline-none focus:ring focus:ring-blue-400"
        >
            <option value="" selected>Select a Prize</option>
            @foreach($prizes as $prize)
                <option value="{{ $prize->id }}">{{ $prize->name }} ({{ $prize->quantity }} left)</option>
            @endforeach
        </select>
    </div>

    <!-- Raffle Button -->
    <button 
        class="rounded-lg bg-blue-500 text-white px-8 py-3 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-md transition-all mt-8 "
        wire:click="raffle" 
        @if($isRunning) disabled @endif
    >
        @if($isRunning)
            <span class="loader"></span> <!-- Spinner -->
        @else
            {{ $buttonMessage }}
        @endif
    </button>

    @if (session('error'))
    <div class="alert alert-danger bg-red-500 text-white p-1 rounded">
        {{ session('error') }}
    </div>
@endif
</div>

   
</div>
    

</div>
