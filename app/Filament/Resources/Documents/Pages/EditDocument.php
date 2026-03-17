<?php
namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Iegūstam datus no formas
        $data = $this->form->getRawState();
        $attachment = $data['attachment'] ?? null;

        if ($attachment) {
            // Notīrām vecos failus
            $this->record->clearMediaCollection('attachments');

            // Pievienojam jauno failu tieši no public diska
            $this->record->addMediaFromDisk($attachment, 'public')
                ->usingName($this->record->title ?? 'document')
                ->toMediaCollection('attachments');
        }
    }
}
