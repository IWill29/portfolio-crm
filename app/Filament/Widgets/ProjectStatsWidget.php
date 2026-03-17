<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class ProjectStatsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Projekta rādītāji';

    protected function getStats(): array
    {
        return [
            Stat::make('Visi projekti', Project::count()),
            Stat::make('Aktīvie projekti', Project::where('status', 'in_progress')->count()),
            Stat::make('Kopējais budžets', '€' . number_format(Project::sum('budget'), 2, ',', ' ')),
            Stat::make('Vidējais budžets', '€' . number_format(Project::avg('budget') ?? 0, 2, ',', ' ')),
        ];
    }
}