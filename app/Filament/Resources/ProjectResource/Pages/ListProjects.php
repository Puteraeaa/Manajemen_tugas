<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;
use Carbon\Carbon;


class ListProjects extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.list-projects';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Project')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(ProjectResource::getUrl('create'))
                ->visible(fn() => auth()->user()->can('create', Project::class)),
        ];
    }
    public function deleteProject(int $projectId)
    {
        $project = Project::findOrFail($projectId);
    
        $this->authorize('delete', $project); // Cek permission user
    
        $project->delete();
    
        $this->notify('success', 'Project berhasil dihapus!');
    
        $this->redirect(); // Reload page biar list update
    }
    

    public function getProjects()
    {
        $user = auth()->user();

        $query = Project::query()
            ->withCount([
                'tasks as total_tasks',
                'tasks as done_tasks' => function ($q) {
                    $q->where('status', 'Done');
                },
                'tasks as in_progress_tasks' => function ($q) {
                    $q->where('status', 'In Progress');
                },
                'tasks as overdue_tasks' => function ($q) {
                    $q->where('deadline', '<', now());
                },
            ]);

        if ($user->hasRole('super_admin')) {
            return $query->get();
        }

        return $query->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->get();
    }


    protected function getViewData(): array
    {
        return [
            'projects' => $this->getProjects(), // Kirimkan data projects ke view
        ];
    }
}
