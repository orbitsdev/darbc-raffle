<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Exports\WinnersExport;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FilamentForm;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventResource\RelationManagers;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::eventForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                ToggleColumn::make('is_active')
                ,
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                ->date()
                    ->sortable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Draw Raffle')
                
                ->tooltip('Draw Raffle')
                // Identifier should be unique and camelCase
                    ->label('Draw Raffle') // Consistent casing
                    ->icon('heroicon-o-ticket')
                    ->size('lg')
                    
                    ->url(function (Model $record) {
                        return route('raffle.draw', ['event_id' => $record->id]);
                    })->hidden(function (Model $record) {
    
                           
                        return !$record->prizes()->where('quantity', '>', 0)->exists();
                        // return $record->totalBatches() <= 0;
                    }),

                    Action::make('Winners')
  
    ->action(function (Model $record) {

        $startDate = $record->start_date ? Carbon::parse($record->start_date)->format('F j, Y') : 'Unknown Start';
        $endDate = $record->end_date ? Carbon::parse($record->end_date)->format('F j, Y') : 'Unknown End';
        $filename = $record->name . ' - ' . $startDate . ' to ' . $endDate . ' - Winners.xlsx';
        
        return Excel::download(new WinnersExport($record), $filename);
        
    })
    
   
    ->icon('heroicon-o-arrow-down-tray')
    ->requiresConfirmation()
    ->modalHeading('Export Winners')
    ->modalSubheading('Download the winners of the event as an Excel report for your reference.')
    ->modalButton('Download Report')
    ->label('Export Winners')->hidden(function (Model $record) {
    
                           
        return !$record->prizes()->whereHas('winners')->exists();
    // return $record->totalBatches() <= 0;
})
    ,


                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(fn (Builder $query) => $query->latest());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
