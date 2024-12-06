<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Filament\Actions\StaticAction;
use Illuminate\Contracts\View\View;
use Filament\Actions\Action;
class GlobalActionController extends Controller
{
    public static function make(string $path): Action
    {
        return Action::make('Refference')
            ->modalSubmitAction(false)
            ->modalContent(fn (): View => view(
                'livewire.total-member',))
            ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
            ->closeModalByClickingAway(false)->modalWidth('7xl');
    }
}
