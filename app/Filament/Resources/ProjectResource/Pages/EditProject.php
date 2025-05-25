<?php


namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterSave(): void
    {
        $this->record->users()->sync($this->form->getState()['users'] ?? []);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['users'] = $this->record->users->pluck('id')->toArray(); // preload relasi pivot
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
