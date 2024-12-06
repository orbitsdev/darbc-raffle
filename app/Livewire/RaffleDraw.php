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
    public $prizeId = null; // Selected prize ID
    public $eventId = null; // Event ID for filtering prizes
    protected ?Prize $selectedPrize = null; // Cache the selected prize object
    public bool $isRunning = false; // Indicates if the raffle process is ongoing

    #[Title('Raffle Draw')]
    public function mount($event_id = null)
    {
        $this->eventId = $event_id; // Set the event ID if passed
    }

    public function updatedPrizeId()
    {
        $this->selectedPrize = $this->prizeId ? Prize::find($this->prizeId) : null; // Fetch and cache the selected prize
    }

    public function hasQuantity(): bool
    {
        if (!$this->selectedPrize && $this->prizeId) {
            $this->selectedPrize = Prize::find($this->prizeId);
        }
        return $this->selectedPrize && $this->selectedPrize->quantity > 0;
    }

    public function render()
    {
        return view('livewire.raffle-draw', [
            'prizes' => $this->eventId
                ? Prize::where('event_id', $this->eventId)->where('quantity','>',0)->get()
                : collect(),
        ]);
    }

    public function raffle(): void
    {
        if (!$this->prizeId) {
            session()->flash('error', 'Please select a prize before starting the raffle.');
            return;
        }

        if (!$this->hasQuantity()) {
            session()->flash('error', 'The selected prize is out of stock.');
            return;
        }

        $this->isRunning = true; // Set the spinner state to true

        $applicants = Member::where('status', true)
            ->whereDoesntHave('winners', function ($query) {
                $query->where('prize_id', $this->prizeId);
            })
            ->inRandomOrder()
            ->get();

        if ($applicants->isEmpty()) {
            session()->flash('error', 'No eligible participants left for this prize.');
            $this->isRunning = false; // Reset spinner state
            return;
        }

        $spinDuration = 3000000;
        $delayPerStream = 100000;
        $totalIterations = $spinDuration / $delayPerStream;

        for ($i = 0; $i < $totalIterations; $i++) {
            $randomParticipant = $applicants->random();
            $this->stream('winner', $randomParticipant->first_name . ' ' . $randomParticipant->last_name, true);
            usleep($delayPerStream);
        }

        $this->winner = $applicants->random();

        Winner::create([
            'member_id' => $this->winner->id,
            'prize_id' => $this->prizeId,
        ]);

        if ($this->selectedPrize) {
            $this->selectedPrize->decrement('quantity');
        }
        $this->dispatch('raffle-winner-drawn'); 
        $this->buttonMessage = 'Draw Again!';
        $this->isRunning = false; // Reset spinner state
    }

    
}
