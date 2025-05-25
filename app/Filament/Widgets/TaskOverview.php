<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TaskOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $user = auth()->user();

        // Super Admin → akses semua task
        if ($user->hasRole('super_admin')) {
            return $this->generateCards(Task::query());
        }

        // Punya permission → task dari project yang user terlibat
        if ($user->can('view_any_task_in_project_task')) {
            $projectIds = $user->projects()->pluck('projects.id'); // FIX: specify table
            return $this->generateCards(Task::whereIn('project_id', $projectIds));
        }

        // Default → task milik sendiri
        return $this->generateCards(Task::where('user_id', $user->id));
    }

    private function generateCards($query)
    {
        return [
            Card::make('Total Tasks', (clone $query)->count()),
            Card::make('Overdue', (clone $query)
                ->where('deadline', '<', now())
                ->where('status', '!=', 'Done')
                ->count())
                ->description('Lewat deadline!')
                ->color('danger'),
            Card::make('Selesai', (clone $query)
                ->where('status', 'Done')
                ->count())
                ->color('success'),
            Card::make('In Progress', (clone $query)
                ->where('status', 'In Progress')
                ->count())
                ->color('warning'),
        ];
    }
}