<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.task-resource.list-tasks';


    public string $project_id;
    public Project $project;

    // Default pagination per page
    protected int $perPage = 5;

    protected function getRouteQueryString(): array
    {
        return array_merge(parent::getRouteQueryString(), [
            'project_id' => ['except' => ''],
        ]);
    }

    public function mount(): void
    {
        $this->project_id = request()->query('project_id')
            ?: abort(404, 'Project ID is required');

        $this->project = Project::with('users')->findOrFail($this->project_id);

        parent::mount();
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Filter task berdasarkan project_id dari query string
        if ($this->project_id) {
            $query->where('project_id', $this->project_id);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn() => TaskResource::getUrl('create', ['project_id' => $this->project_id])),
        ];
    }

    // Jangan override tasks di getViewData, biar pagination & filter di getTableQuery jalan mulus
    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'project' => $this->project,
        ]);
    }

    // Redirect setelah hapus tetap bawa project_id
    protected function getDeletedRedirectUrl(): string
    {
        return static::getResource()::getUrl('index', ['project_id' => $this->project_id]);
    }
}
