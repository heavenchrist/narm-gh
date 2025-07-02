<?php

namespace App\Filament\Portal\Pages;
  use Filament\Pages\Dashboard as BaseDashboard;
class MyDashboard extends BaseDashboard
{

    protected static ?string $title = 'My Dashboard';
   protected static string $routePath = 'my-user-dashboard';
   //protected static bool $shouldRegisterNavigation = false;

}
