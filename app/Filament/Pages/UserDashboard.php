<?php

namespace App\Filament\Pages;
use Filament\Pages\Dashboard as BaseDashboard;
class UserDashboard extends BaseDashboard
{
    protected static ?string $title = 'Admin Dashboard';
   protected static string $routePath = 'admin-dashboard';
   //protected static bool $shouldRegisterNavigation = false;
}
