<div class="flex flex-col items-center justify-center h-screen relative" style="background: url('{{ asset('images/bg.png') }}') center/cover no-repeat;">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/5 to-black/30 z-0"></div>

    <!-- Main Content -->
    <div class="relative z-10 flex flex-col items-center h-full w-full">
        <!-- Winner Display Centered -->
        <div class="absolute top-1/3 transform -translate-y-1/2 flex items-center justify-center text-center">
            <div wire:stream="winner" class="rounded-lg h-32 flex items-center justify-center text-7xl font-extrabold text-white bg-black/50 shadow-xl px-8 ">
                {{ $winner?->fullname ?? '🎉 Click the Raffle Button to Begin! 🎉' }}
            </div>
        </div>

        <!-- Prize Selection and Button Lowered -->
        <div class="absolute bottom-16 flex flex-col items-center space-y-4">
            <!-- Prize Dropdown -->
            <label for="prize" class="text-white text-lg font-semibold">Select a Prize:</label>
            <select 
                id="prize" 
                wire:model="prizeId"
                class="w-64 p-3 bg-gray-800 text-white border-2 border-blue-500 rounded-lg focus:outline-none focus:ring focus:ring-blue-400 text-center"
            >
                <option value="" selected>Select a Prize</option>
                @foreach($prizes as $prize)
                    <option value="{{ $prize->id }}">{{ $prize->name }} ({{ $prize->quantity }} left)</option>
                @endforeach
            </select>

            <!-- Raffle Button -->
            <button 
                class="rounded-lg bg-blue-500 text-white px-8 py-3 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-md transition-all"
                wire:click="raffle" 
                @if($isRunning) disabled @endif
            >
                @if($isRunning)
                    <span class="loader"></span> <!-- Spinner -->
                @else
                    {{ $buttonMessage }}
                @endif
            </button>

            <!-- Error Message -->
            @if (session('error'))
                <div class="alert alert-danger bg-red-500 text-white p-1 rounded text-center">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</div>
