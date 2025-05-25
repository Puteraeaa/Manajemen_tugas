<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;


class TaskResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Task::class;

    protected int $perPage = 1;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function getLabel(): string
    {
        return 'Task Project';
    }

    public static function getNavigationLabel(): string
    {
        return 'Task Project';
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
             'view_any_task_in_project',
            'create',
            'update',
            'update_status_in_task',
            'delete',
        ];
    }


    public function getTitle(): string
    {
        $projectId = request()->query('project_id');
        $project = \App\Models\Project::find($projectId);
        return $project ? "Tasks for Project: {$project->name}" : "Tasks";
    }



    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        $hasUpdateTask = $user->can('update_task'); // full edit permission
        $hasUpdateStatus = $user->can('update_status_in_task_task'); // edit status permission

        $canEditAll = $hasUpdateTask && ! $hasUpdateStatus;
        $canEditStatusOnly = $hasUpdateStatus && $hasUpdateTask;

        return $form->schema([
            Hidden::make('project_id')
                ->default(fn() => request()->query('project_id'))
                ->required(),

            // Full edit form
            Section::make('Edit Task')
                ->schema([
                    Select::make('user_id')
                        ->label('Assigned User')
                        ->required()
                        ->preload()
                        ->relationship('user', 'name')
                        ->options(function (callable $get) {
                            $projectId = $get('project_id');
                            if ($projectId) {
                                return \App\Models\User::whereHas('projects', function ($query) use ($projectId) {
                                    $query->where('projects.id', $projectId);
                                })->pluck('name', 'id');
                            }
                            return [];
                        })
                        ->searchable(),

                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->nullable(),

                    Select::make('status')
                        ->options([
                            'To Do' => 'To Do',
                            'In Progress' => 'In Progress',
                            'Done' => 'Done',
                        ])
                        ->default('To Do')
                        ->required(),

                    Select::make('priority')
                        ->options([
                            'Low' => 'Low',
                            'Medium' => 'Medium',
                            'High' => 'High',
                        ])
                        ->default('Medium')
                        ->required(),

                    DatePicker::make('deadline')->nullable(),
                    TextInput::make('estimated_time')->numeric()->nullable(),
                    FileUpload::make('attachment_path')->nullable(),
                ])
                ->visible($canEditAll),

            // Edit status only form
            Section::make('Update Status')
                ->schema([

                    Card::make()
                        ->schema([
                            TextInput::make('title')
                                ->label('Judul Task')
                                ->disabled()
                                ->columnSpan('full')
                                ->extraAttributes(['class' => 'bg-gray-100 text-gray-700 rounded-md border border-gray-300']),

                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->disabled()
                                ->rows(3)
                                ->columnSpan('full')
                                ->extraAttributes(['class' => 'bg-gray-100 text-gray-700 rounded-md border border-gray-300']),

                            TextInput::make('priority')
                                ->label('Prioritas')
                                ->disabled()
                                ->columnSpan('full')
                                ->extraAttributes(['class' => 'bg-gray-100 text-gray-700 rounded-md border border-gray-300']),

                            DatePicker::make('deadline')
                                ->label('Deadline')
                                ->disabled()
                                ->columnSpan('full')
                                ->extraAttributes(['class' => 'bg-gray-100 text-gray-700 rounded-md border border-gray-300']),
                        ])
                        ->columns(1),


                    Select::make('status')
                        ->options([
                            'To Do' => 'To Do',
                            'In Progress' => 'In Progress',
                            'Done' => 'Done',
                        ])
                        ->default('To Do')
                        ->required(),

                  

                    TextInput::make('estimated_time')
                        ->nullable()
                        ->label('Estimated Time (hours)'),
                ])
                ->visible($canEditStatusOnly),

            // View only form kalau gak punya permission edit apa-apa
            Section::make('Task Information')
                ->schema([
                    TextInput::make('user.name')->label('Assigned User')->disabled(),
                    
                    TextInput::make('title')->disabled(),
                    Textarea::make('description')->disabled(),
                    TextInput::make('status')->disabled(),
                    TextInput::make('priority')->disabled(),
                    DatePicker::make('deadline')->disabled(),
                    TextInput::make('estimated_time')->disabled(),
                    TextInput::make('attachment_path')->label('Attachment')->disabled(),
                ])
                ->visible(! $canEditAll && ! $canEditStatusOnly),
        ]);
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('description')->label('Description')->searchable(),
                TextColumn::make('assignedBy.name')
                ->label('Gives Assignments')
                ->searchable()
                ->sortable(),
                TextColumn::make('attachment_path')
                    ->label('Attachment')
                    ->color('primary')
                    ->icon('heroicon-o-paper-clip')
                    ->formatStateUsing(fn($state) => $state ? 'View File' : 'No File')
                    ->url(fn($record) => $record->attachment_path ? asset('storage/' . $record->attachment_path) : null, true)
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match (strtolower($state)) {
                        'to do' => 'warning',
                        'in progress' => 'success',
                        'done' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn($state) => match (strtolower($state)) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('deadline')->date(),
                TextColumn::make('estimated_time')->label('Estimated Time (hours)')->placeholder("No time"),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created At')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated At')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'To Do' => 'To Do',
                        'In Progress' => 'In Progress',
                        'Done' => 'Done',
                    ])->placeholder('All Statuses'),
                SelectFilter::make('priority')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                    ])->placeholder('All Priorities'),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();


        if (
            !auth()->user()->hasRole('super_admin') &&
            !auth()->user()->can('view_any_task_in_project_task')
        ) {

            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
