<?php

namespace App\Filament\Resources;
use App\Filament\Resources\PersonalTaskResource\Pages;
use App\Models\PersonalTask;
use App\Models\Task;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalTaskResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Task::class;

   

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'update_status_in_task_task',
            'delete',
           
        ];
    }

    public static function getLabel(): string
    {
        return 'Task Personal';
    }

    public static function getNavigationLabel(): string
    {
        return 'Task Personal';
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Users')
                    ->required()
                    ->preload()
                    ->relationship('user', 'name')
                    ->options(
                        User::whereDoesntHave('roles', function ($query) {
                            $query->where('name', 'super_admin');
                        })->pluck('name', 'id')
                    ),

                TextInput::make('title')
                    ->label('Title')
                    ->required(),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(5)
                    ->nullable(),

                FileUpload::make('attachment_path')->nullable(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'To Do' => 'To Do',
                        'In Progress' => 'In Progress',
                        'Done' => 'Done',
                    ])
                    ->required(),

                DatePicker::make('deadline')
                    ->label('Deadline')
                    ->nullable(),


            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),

                    TextColumn::make('description')->label('Description')->searchable(),

                    TextColumn::make('assignedBy.name')
                    ->label('Gives Assignments')
                    ->searchable(),

                    TextColumn::make('user.name')
                    ->label('Assigned')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'To Do' => 'gray',
                        'In Progress' => 'warning',
                        'Done' => 'success',
                    }),

                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'To Do' => 'To Do',
                        'In Progress' => 'In Progress',
                        'Done' => 'Done',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListPersonalTasks::route('/'),
            'create' => Pages\CreatePersonalTask::route('/create'),
            'edit' => Pages\EditPersonalTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->whereNull('project_id');

        if (
            !auth()->user()->hasRole('super_admin') &&
            !auth()->user()->can('view_any_task_in_project_task')
        ) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
