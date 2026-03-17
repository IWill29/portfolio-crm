<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Activitylog\Models\Activity;

class RecentActivityWidget extends TableWidget
{
    protected static ?string $heading = 'Pēdējās darbības';

    protected function getTableQuery(): Builder|Relation|null
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')->label('Laiks')->dateTime(),

            TextColumn::make('description')
                ->label('Darbība')
                ->wrap(),

            TextColumn::make('causer.name')
                ->label('Lietotājs')
                ->default('—'),

            TextColumn::make('subject.title')
                ->label('Objekta nosaukums')
                ->formatStateUsing(fn ($value, $record) => optional($record->subject)->title ?? ''),
        ];
    }
}