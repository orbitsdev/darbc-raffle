<?php

namespace App\Livewire;

use App\Models\Member;
use Livewire\Component;

class TotalMember extends Component
{public $totalMembers;

    public function mount()
    {
        $this->totalMembers = Member::count(); // Retrieve the total count of members
    }

    public function render()
    { 
        $totalMembers = Member::count();
        return view('livewire.total-member', [
            'totalMembers' => $totalMembers,
        ]);
    }
}
