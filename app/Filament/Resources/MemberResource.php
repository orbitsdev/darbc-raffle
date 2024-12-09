<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Member;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Imports\MembersImport;
use Filament\Resources\Resource;
use Filament\Actions\StaticAction;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FilamentForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\ActionGroup;

use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\MemberResource\Pages;
use App\Http\Controllers\GlobalActionController;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MemberResource\RelationManagers;


class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::memberForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fullname'),
                Tables\Columns\TextColumn::make('darbc_id')
                ->searchable(isIndividual: true)->label('DARBC ID'),
                Tables\Columns\TextColumn::make('last_name')
                ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('id_number')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])

            ->headerActions([
                Action::make('Clear Table')
                ->label('Clear All Members')

                ->icon('heroicon-o-trash')
                ->button()
                ->hidden(function () {
                    return Member::count() === 0;
                })
                ->outlined()
                ->requiresConfirmation() // Ask for confirmation before clearing
                ->modalHeading('Confirm Table Clear')
                ->modalSubheading('Are you sure you want to delete all members? This action cannot be undone.')
                ->action(function (): void {

                   DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Member::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');



                })
                ->closeModalByClickingAway(false) // Disable closing by clicking outside
                ->modalWidth('md'),
                Action::make('total mebers')
                ->label('Total Members')
                ->modalSubmitAction(false)
                ->icon('heroicon-o-users')
                ->button()
                ->outlined()
                ->modalContent(function (): View {

                    $totalMembers = Member::count();
                    return view(
                        'total-members',
                        ['totalMembers' => $totalMembers,],
                    );
                })
                ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                ->closeModalByClickingAway(false)->modalWidth('2xl'),
                Action::make('Import')
                ->button()
                ->action(function (array $data): void {


                    $file  = Storage::disk('public')->path($data['file']);

                    Excel::import(new MembersImport, $file);

                    if (Storage::disk('public')->exists($data['file'])) {

                        Storage::disk('public')->delete($data['file']);
                    }


                })
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/csv',
                            'text/csv',
                            'text/plain',
                        ])
                        ->disk('public')
                        ->directory('imports')
                        ->label('Excel File'),
                ])
                ->outlined()
                ->button()
                ->label('Import Members')
                ->modalHeading('Upload Member File')
                ->modalDescription('Follow these instructions to import members into the system:

            1. Ensure your file is in the correct format (`.xlsx`, `.xls`, or `.csv`).
            2. The file must include these columns:
               - **First Name**: The member\'s first name.
               - **Middle Name**: The member\'s middle name (optional).
               - **Last Name**: The member\'s last name.
               - **DARBC ID**: This must be unique for each member.

            3. If updating existing members, ensure the "DARBC ID" matches records in the system. Otherwise, new members will be created.
            4. Verify your data before uploading to prevent errors.

            Thank you for your cooperation!')

            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
