<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasColor, HasIcon, HasLabel
{
    case Male = 'Male';

    case Female = 'Female';

    public function getLabel(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Male => Color::Emerald,
            self::Female => Color::Purple,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Male => 'heroicon-m-user-circle',
            self::Female => 'heroicon-m-user',
        };
    }
}
