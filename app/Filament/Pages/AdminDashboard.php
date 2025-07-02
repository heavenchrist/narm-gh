<?php

namespace App\Filament\Pages;
use Filament\Pages\Dashboard as BaseDashboard;
class AdminDashboard extends BaseDashboard
{
    protected static ?string $title = 'Admin dashboard';
     protected static string $view = 'filament-panels::pages.dashboard';
}