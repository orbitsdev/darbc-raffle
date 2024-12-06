<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Member;
use App\Models\Prize;
use Illuminate\Http\Request;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Builder;

class FilamentForm extends Controller
{

    public static function eventForm(): array
    {
        return [
            Section::make('Event Management')
                ->description('Manage and update the event details for the raffle system. This section allows you to organize events, define their timelines, and activate or deactivate them.')
                ->columns([
                    'sm' => 3,
                    'xl' => 9,
                    '2xl' => 9,
                ])
                ->headerActions([])
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->label('Event Name')
                        ->maxLength(191),
                    TextInput::make('description')
                        ->columnSpanFull()
                        ->label('Description')
                        ->maxLength(500),
                    DatePicker::make('start_date')
                        ->date()
                        ->columnSpan(3)
                        ->required()
                        ->label('Start Date'),
                    DatePicker::make('end_date')
                        ->date()
                        ->columnSpan(3)
                        ->required()
                        ->label('End Date'),
                    Toggle::make('is_active')
                        ->columnSpanFull(3)->default(true)
                        ->label('Is Active'),
                ]),
        ];
    }

    public static function memberForm(): array
    {
        return [
            Section::make('Raffle Management')
                ->description('Manage and update the raffle details displayed on your platform. This section allows you to organize and streamline raffle entries and participant information.')
                ->columns([
                    'sm' => 3,
                    'xl' => 9,
                    '2xl' => 9,
                ])
                ->headerActions([])
                ->schema([
                    TextInput::make('darbc_id')->columnSpanFull()->unique(ignoreRecord: true)->label('DARBC ID')->numeric(),
                    TextInput::make('first_name')->columnSpan(3),
                    TextInput::make('middle_name')->columnSpan(3),
                    TextInput::make('last_name')->columnSpan(3),

                ]),

        ];
    }
    public static function prizeForm(): array
    {
        return [
            Section::make('Prize Management')
                ->description('Manage and update the prize details for the raffle system. This section allows you to define the prizes, including their types, quantities, and additional details.')
                ->columns([
                    'sm' => 3,
                    'xl' => 9,
                    '2xl' => 9,
                ])
                ->headerActions([])
                ->schema([
                    Select::make('event_id')
                        ->options(Event::pluck('name', 'id'))

                        // ->relationship(name: 'event', titleAttribute: 'name',ignoreRecord: true)
                        ->searchable()
                        ->label('What Event?')
                        ->required()
                        ->preload()->columnSpanFull(),

                    TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->label('Prize Name')
                        ->maxLength(191)->columnSpan(3),
                    TextInput::make('type')
                        ->columnSpan(3)
                        ->label('Prize Type')
                        ->maxLength(191),
                    TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->label('Quantity')

                        ->columnSpan(3),

                    SpatieMediaLibraryFileUpload::make('image')->columnSpanFull(),
                ]),
        ];
    }


    public static function winnerForm(): array
    {
        return [
            Section::make('Winner Management')
                ->description('Manage and update the winner details for the raffle system. This section allows you to assign members to prizes and ensure accurate records of raffle winners.')
                ->columns([
                    'sm' => 3,
                    'xl' => 9,
                    '2xl' => 8,
                ])
                ->headerActions([])
                ->schema([
                    Select::make('member_id')
                    ->options(Member::get()->map(function ($d) {
                        return ['name' => $d->fullName, 'id' => $d->id];
                    })->pluck('name', 'id'))
                    ->searchable()
                        ->preload()->columnSpanFull(),
                    Select::make('prize_id')
                         ->disabled(fn(string $operation): bool => $operation === 'edit')
                        ->options(Prize::pluck('name', 'id'))->label('Prize')

                        // ->relationship(name: 'event', titleAttribute: 'name',ignoreRecord: true)
                        ->searchable()

                        ->required()
                        ->preload()->columnSpan(4),

                ]),
        ];
    }
}
