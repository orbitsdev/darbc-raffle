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

    public ?Member $winner = null;
    public string $buttonMessage = 'Start Raffle!';
    public $prizeId = null;
    public $eventId = null;
    protected ?Prize $selectedPrize = null;
    public bool $isRunning = false; //

    #[Title('Raffle Draw')]
    public function mount($event_id = null)
    {
        $this->eventId = $event_id; // Set the event ID if passed
    }

    public function updatedPrizeId()
    {
        $this->selectedPrize = $this->prizeId ? Prize::find($this->prizeId) : null;
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

        $this->isRunning = true; 

        $applicants = Member::where('status', true)
        ->whereDoesntHave('winners', function ($query) {
            $query->where('prize_id', $this->prizeId)
                  ->whereHas('prize', function ($prizeQuery) {
                      $prizeQuery->where('event_id', $this->eventId);
                  });
        })
        ->inRandomOrder()
            ->get();

        if ($applicants->isEmpty()) {
            session()->flash('error', 'No eligible participants left for this prize.');
            $this->isRunning = false;
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
        $this->isRunning = false;
    }


}
