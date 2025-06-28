<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RevalidationStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'Pending';
    case Processing = 'Processing';
    case Approved = 'Approved';
    case Verifying = 'Verifying';
    case Disapproved = 'Disapproved';

    public function getLabel(): string
    {
        return match ($this) {
            self::Verifying => 'Verifying',
            self::Processing => 'Processing',
            self::Approved => 'Approved',
            self::Pending => 'Pending',
            self::Disapproved => 'Disapproved',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Verifying => Color::Purple,
            self::Processing => Color::Indigo,
            self::Approved=>Color::Emerald,
            self::Pending => Color::Amber,
            self::Disapproved => Color::Rose,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Verifying => 'heroicon-m-sparkles',
            self::Processing => 'heroicon-m-arrow-path',
            self::Approved => 'heroicon-m-check-badge',
            self::Pending => 'heroicon-o-pause-circle',
            self::Disapproved => 'heroicon-m-x-circle',
        };
    }
}