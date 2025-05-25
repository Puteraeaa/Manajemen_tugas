<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TaskStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Tugas';

    protected static ?string $maxHeight = '400px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $user = auth()->user();

        $query = Task::selectRaw('status, COUNT(*) as total')
            ->groupBy('status');

        if ($user->hasRole('super_admin')) {
            // semua task tanpa filter
        } elseif ($user->can('view_any_task_in_project_task')) {
            $projectIds = $user->projects()->pluck('projects.id');
            $query->whereIn('project_id', $projectIds);
        } else {
            $query->where('user_id', $user->id);
        }

        $data = $query->pluck('total', 'status');

        return [
            'datasets' => [
                [
                    'data' => $data->values(),
                    'backgroundColor' => [
                        '#fbbf24', // To Do - kuning
                        '#3b82f6', // In Progress - biru
                        '#10b981', // Done - hijau
                    ],
                ],
            ],
            'labels' => $data->keys(),
        ];
    }
}
