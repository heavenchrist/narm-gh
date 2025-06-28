<?php

namespace App\Filament\Pages;
use Filament\Pages\Dashboard as BaseDashboard;
class UserDashboard extends BaseDashboard
{
    protected static ?string $title = 'My Dashboard';
   protected static string $routePath = 'user-dashboard';
   //protected static bool $shouldRegisterNavigation = false;
}