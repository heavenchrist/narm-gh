<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\UserDashboard;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Auth\MemberLogin;
use App\Filament\Portal\Pages\MyProfile;
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

class PortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('portal')
            ->path('/portal')
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Sky,
                'primary' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->spa()
            ->passwordReset()
            ->login(MemberLogin::class)
            ->profile(isSimple:true)
            ->databaseNotifications()
            ->favicon(asset('images/logo.png'))
            ->brandName('NARM GH Portal')
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo-dk.png'))
            ->brandLogoHeight('50px')
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\\Filament\\Portal\\Resources')
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\\Filament\\Portal\\Pages')
            ->pages([
                //Pages\Dashboard::class,
                UserDashboard::class,
            ])
            /* ->plugin(
                \Hasnayeen\Themes\ThemesPlugin::make()->canViewThemesPage(fn () => false),
            ) */
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->plugin(RenewPasswordPlugin::make()->passwordExpiresIn(days: 60))
            ->plugin(new Lockscreen())
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Account Settings')->icon('heroicon-o-cog-8-tooth'),
                // ...
            ])
            /* ->navigationItems([

                NavigationItem::make('My Profile')
                    //->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                    ->url(fn (): string => MyProfile::getUrl()),
                    //->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),
                // ...
            ]) */
            ->discoverWidgets(in: app_path('Filament/Portal/Widgets'), for: 'App\\Filament\\Portal\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])->sidebarCollapsibleOnDesktop()
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
                LockerTimer::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authMiddleware([
                Authenticate::class,
                Locker::class,
            ]);
    }
}
