<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login;
use Illuminate\Contracts\Support\Htmlable;

class RegionalLogin extends Login
{
    public function getHeading(): string | Htmlable
    {
        return __(key: 'Regional Login');
    }
}
