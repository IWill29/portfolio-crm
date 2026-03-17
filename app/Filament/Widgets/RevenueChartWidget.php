<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Ieņēmumu tendence';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $i) => Carbon::now()->subMonths($i))
            ->map(fn (Carbon $date) => $date->format('Y-m'))
            ->values();

        $revenueByMonth = $months->mapWithKeys(fn (string $month) => [
            $month => Project::query()
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->sum('budget')
        ]);

        return [
            'labels' => $revenueByMonth->keys()->map(fn (string $month) => Carbon::parse($month)->isoFormat('MMM YYYY'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Budžets',
                    'data' => $revenueByMonth->values()->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.25)',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                ],
            ],
        ];
    }
}