<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Carbon\Carbon;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $projectId = request()->query('project_id');
        if ($projectId) {
            $data['project_id'] = $projectId;
        }

        $data['assigned_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
{
    Carbon::setLocale('id');
    $assignedUser = $this->record->user;

    $projectName = optional($this->record->project)->name ?? 'Proyek Tanpa Nama';
    $deadline = Carbon::parse($this->record->deadline)->translatedFormat('l, d F Y') ?? 'Tidak Ada';


    Notification::make()
        ->title('Tugas Baru Untukmu: ' . $this->record->title)
        ->success()
        ->body('Kamu ditugaskan dalam proyek "' . $projectName . '" dengan deadline ' . $deadline)
        ->actions([
            Action::make('markAsUnread')
                ->button()
                ->markAsUnread(),
        ])
        ->sendToDatabase($assignedUser);
}


    protected function getRedirectUrl(): string
    {
        $projectId = request()->query('project_id') ?? $this->record->project_id;

        return TaskResource::getUrl('index', ['project_id' => $projectId]);
    }
}
