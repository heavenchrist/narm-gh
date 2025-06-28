<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaritalStatus: string implements HasColor, HasIcon, HasLabel
{
    case Single = 'Single';
    case Married = 'Married';
    case Divorced = 'Divorced';
    case Separated = 'Separated';
    case Widowed ='Widowed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Married => 'Married',
            self::Divorced => 'Divorced',
            self::Separated => 'Separated',
            self::Widowed => 'Widowed',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Single => Color::Emerald,
            self::Married => Color::Purple,
            self::Divorced => Color::Sky,
            self::Separated => Color::Pink,
            self::Widowed => Color::Amber,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Single => 'heroicon-m-user',
            self::Married => 'heroicon-m-user-plus',
            self::Divorced => 'heroicon-m-user-minus',
            self::Separated => 'heroicon-m-users',
            self::Widowed => 'heroicon-m-user-circle',
        };
    }
}