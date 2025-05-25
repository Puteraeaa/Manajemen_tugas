<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskTable extends BaseWidget
{
    protected static ?string $heading = 'Tasks Management';
    protected int | string | array $columnSpan = 2;

    // Property untuk menyimpan tab yang aktif
    public $activeTab = 'project';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|null
    {
        $user = auth()->user();

        if ($this->activeTab === 'personal') {
            // Personal Tasks: task tanpa project (project_id = null)
            if ($user->hasRole('super_admin')) {
                return Task::query()
                    ->whereNull('project_id')
                    ->orderBy('priority', 'desc')
                    ->orderBy('deadline');
            }

            return Task::query()
                ->where('user_id', $user->id)
                ->whereNull('project_id')
                ->orderBy('priority', 'desc')
                ->orderBy('deadline');
        }

        // Project Tasks: task dengan project (project_id != null)
        if ($user->hasRole('super_admin')) {
            return Task::query()
                ->whereNotNull('project_id')
                ->orderBy('priority', 'desc')
                ->orderBy('deadline');
        }

        if ($user->can('view_any_task_in_project_task')) {
            $projectIds = $user->projects()->pluck('projects.id')->toArray();
            return Task::query()
                ->whereIn('project_id', $projectIds)
                ->whereNotNull('project_id')
                ->orderBy('priority', 'desc')
                ->orderBy('deadline');
        }

        return Task::query()
            ->where('user_id', $user->id)
            ->whereNotNull('project_id')
            ->orderBy('priority', 'desc')
            ->orderBy('deadline');
    }

    protected function getTableColumns(): array
    {
        $baseColumns = [
            TextColumn::make('title')->label('Task'),
            TextColumn::make('user.name')->label('Assigned To'),
            TextColumn::make('priority')
                ->label('Priority')
                ->badge()
                ->color(fn($state) => match (strtolower($state)) {
                    'high' => 'danger',
                    'medium' => 'warning',
                    'low' => 'success',
                    default => 'gray',
                }),
            TextColumn::make('deadline')
                ->date()
                ->label('Deadline'),
            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn($state) => match (strtolower($state)) {
                    'done' => 'success',
                    'in progress' => 'warning',
                    'to do' => 'gray',
                    default => 'gray',
                }),
        ];

        // Tambahkan kolom Project hanya untuk tab Project Tasks
        if ($this->activeTab === 'project') {
            array_unshift($baseColumns, TextColumn::make('project.name')->label('Project'));
        }

        return $baseColumns;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->headerActions([
                // Tab buttons
                \Filament\Tables\Actions\Action::make('project_tasks')
                    ->label('Project Tasks')
                    ->icon('heroicon-o-folder')
                    ->color($this->activeTab === 'project' ? 'primary' : 'gray')
                    ->action(function () {
                        $this->activeTab = 'project';
                        $this->dispatch('refreshWidget');
                    }),
                \Filament\Tables\Actions\Action::make('personal_tasks')
                    ->label('Personal Tasks')
                    ->icon('heroicon-o-user')
                    ->color($this->activeTab === 'personal' ? 'primary' : 'gray')
                    ->action(function () {
                        $this->activeTab = 'personal';
                        $this->dispatch('refreshWidget');
                    }),
            ]);
    }

    // Method untuk refresh widget saat tab berubah
    protected $listeners = ['refreshWidget' => '$refresh'];
}