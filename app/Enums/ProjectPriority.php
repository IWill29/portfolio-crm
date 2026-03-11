<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectPriority: string implements HasLabel, HasColor
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    /**
     * Teksts, ko redzēs lietotājs CRM sarakstos un formās.
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Low => 'Zema',
            self::Medium => 'Vidēja',
            self::High => 'Augsta',
            self::Urgent => 'Steidzami',
        };
    }

    /**
     * Krāsa vizuālam brīdinājumam Filament tabulās.
     */
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Low => 'gray',
            self::Medium => 'info',
            self::High => 'warning',
            self::Urgent => 'danger',
        };
    }
}
