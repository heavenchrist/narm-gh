<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TrainingMode: string implements HasColor, HasIcon, HasLabel
{
    case ONLINE = 'online';
    case IN_PERSON = 'in_person';
    case HYBRID = 'hybrid';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::IN_PERSON => 'In Person',
            self::HYBRID => 'Hybrid',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ONLINE => Color::Amber,
            self::IN_PERSON => Color::Rose,
            self::HYBRID => Color::Pink,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ONLINE => 'heroicon-m-globe-alt',
            self::IN_PERSON => 'heroicon-m-user-group',
            self::HYBRID => 'heroicon-m-arrow-path-rounded-square',
        };
    }
}
