<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $attachment = $data['attachment'] ?? null;

        $model = parent::handleRecordCreation($data);

        if ($attachment) {
            $disk = Storage::disk('public');
            $relative = ltrim($attachment, '/');
            $fullPath = $disk->path($relative);

            if (file_exists($fullPath)) {
                $model->addMedia($fullPath)
                    ->usingName($model->title ?? 'document')
                    ->toMediaCollection('attachments');

                @unlink($fullPath);
            }
        }

        return $model;
    }
}
