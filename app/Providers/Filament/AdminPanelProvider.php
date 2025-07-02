<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\UserDashboard;
use App\Filament\Pages\Auth\AdminLogin;
use Filament\Http\Middleware\Authenticate;
use lockscreen\FilamentLockscreen\Lockscreen;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;
use lockscreen\FilamentLockscreen\Http\Middleware\Locker;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use lockscreen\FilamentLockscreen\Http\Middleware\LockerTimer;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            ->profile(isSimple:true)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Cyan,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->brandName('Admin Panel')
            ->favicon(asset('images/logo.png'))
            ->brandName('Admin Portal')
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo-dk.png'))
            ->brandLogoHeight('50px')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
               UserDashboard::class,
            ]) ->plugin(RenewPasswordPlugin::make()->passwordExpiresIn(days: 15))
            ->plugin(new Lockscreen())

            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Account Settings')->icon('heroicon-o-cog-8-tooth'),
                // ...
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
                LockerTimer::class, // <- Add this (this is an optional, if you want to lock the request after 30 minutes idle)
            ])
           ->viteTheme('resources/css/filament/admin/theme.css')
            ->authMiddleware([
                Authenticate::class,
                Locker::class,
            ]);
    }
}
