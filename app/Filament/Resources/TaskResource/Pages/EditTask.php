<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Boleh lihat semua field
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        // Kalau user hanya punya permission update_status_in_task
        if (
            $user->can('update_status_in_task') &&
            !$user->can('update') // Tidak punya full update
        ) {
            // Hapus semua field kecuali status
            return [
                'status' => $data['status'],
            ];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $projectId = $this->record->project_id;
        return TaskResource::getUrl('index', ['project_id' => $projectId]);
    }
}
