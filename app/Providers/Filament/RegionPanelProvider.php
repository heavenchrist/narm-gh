<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\RegionalDashboard;
use App\Filament\Pages\Auth\RegionalLogin;
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

class RegionPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('region')
            ->path('region')
            ->colors([
                'primary' => Color::Amber,
            ])->login(RegionalLogin::class)
            ->passwordReset()
            ->databaseNotifications()
            ->profile(isSimple:true)
            ->favicon(asset('images/logo.png'))
            ->brandName('Regional Portal')
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo-dk.png'))
            ->brandLogoHeight('50px')
            ->discoverResources(in: app_path('Filament/Region/Resources'), for: 'App\\Filament\\Region\\Resources')
            ->discoverPages(in: app_path('Filament/Region/Pages'), for: 'App\\Filament\\Region\\Pages')
            ->pages([
                RegionalDashboard::class,
            ]) ->userMenuItems([
                'profile' => MenuItem::make()->label('Account Settings')->icon('heroicon-o-cog-8-tooth'),
                // ...
            ])->plugin(new Lockscreen())
           /*  ->plugin(
                \Hasnayeen\Themes\ThemesPlugin::make()->canViewThemesPage(fn () => false),

            ) */
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->plugin(RenewPasswordPlugin::make()->passwordExpiresIn(days: 30))
            ->sidebarCollapsibleOnDesktop()
            ->discoverWidgets(in: app_path('Filament/Region/Widgets'), for: 'App\\Filament\\Region\\Widgets')
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
