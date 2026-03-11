<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel, HasColor
{
    case Idea = 'idea';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Done = 'done';
    case Cancelled = 'cancelled';

    /**
     * Teksts, ko redzēs lietotājs CRM sistēmā.
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Idea => 'Ideja',
            self::InProgress => 'Procesā',
            self::Review => 'Pārbaudē',
            self::Done => 'Pabeigts',
            self::Cancelled => 'Atcelts',
        };
    }

    /**
     * Krāsa Filament nozīmītēm (Badge).
     */
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Idea => 'gray',
            self::InProgress => 'info',
            self::Review => 'warning',
            self::Done => 'success',
            self::Cancelled => 'danger',
        };
    }
}
