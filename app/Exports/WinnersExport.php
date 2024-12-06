<?php

namespace App\Exports;

use App\Models\Winner;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WinnersExport implements FromView
{
    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function view(): View
    {
        return view('exports.winners-export', [
            'event' => $this->event,
            'winners' => Winner::with('member', 'prize')
                ->whereHas('prize', function ($query) {
                    $query->where('event_id', $this->event->id);
                })->latest()
                ->get(),
        ]);
    }
}
