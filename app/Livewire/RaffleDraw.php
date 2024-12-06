<?php

namespace App\Livewire;

use App\Models\Prize;
use App\Models\Member;
use App\Models\Winner;
use Livewire\Component;
use Livewire\Attributes\On; 
use Livewire\Attributes\Title;

class RaffleDraw extends Component
{
    public ?Member $winner = null; // Store the final winner
    public string $buttonMessage = 'Start Raffle!'; // Button text
    public $prizeId = null; // Selected prize
    public $eventId = null; // Event ID for filtering prizes

    #[Title('Raffle Draw')]
    public function mount($event_id = null)
    {
        // Set the event ID if passed
        $this->eventId = $event_id;
    }

    public function render()
    {
        return view('livewire.raffle-draw', [
            'prizes' => $this->eventId
                ? Prize::where('event_id', $this->eventId)->where('quantity', '>', 0)->get() // Filter prizes by event_id
                : collect(), // Empty collection if no event_id is provided
        ]);
    }

    public function raffle(): void
    {
        // Ensure a prize is selected
        if (!$this->prizeId) {
            session()->flash('error', 'Please select a prize before starting the raffle.');
            return;
        }

        // Fetch eligible participants
        $applicants = Member::where('status', true)
            ->whereDoesntHave('winners', function ($query) {
                $query->where('prize_id', $this->prizeId);
            })
            ->inRandomOrder()
            ->get();

        // Ensure there are eligible participants
        if ($applicants->isEmpty()) {
            session()->flash('error', 'No eligible participants left for this prize.');
            return;
        }

        // Set spinning duration (e.g., 3 seconds)
        $spinDuration = 3000000; // 3 seconds in microseconds
        $delayPerStream = 100000; // 0.1 second (100ms)
        $totalIterations = $spinDuration / $delayPerStream;

        // Loop through participants for the duration
        for ($i = 0; $i < $totalIterations; $i++) {
            $randomParticipant = $applicants->random();
            $this->stream('winner', $randomParticipant->first_name . ' ' . $randomParticipant->last_name, true);
            usleep($delayPerStream);
        }

        // Select the final winner
        $this->winner = $applicants->random();

        // Save the winner to the database
        Winner::create([
            'member_id' => $this->winner->id,
            'prize_id' => $this->prizeId,
        ]);

        // Decrease prize quantity
        $prize = Prize::find($this->prizeId);
        $prize->decrement('quantity');

        // Update button message
        $this->buttonMessage = 'Draw Again!';
    }
    
}
