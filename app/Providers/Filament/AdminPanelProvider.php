<?php

namespace App\Providers\Filament;

use App\Filament\Pages\TypecastAudioCredentialsPage;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('RevizySeeder')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->navigationItems([
                NavigationItem::make('API Providers')
                    ->group('RevizySeeder')
                    ->icon('heroicon-o-cloud')
                    ->sort(4)
                    ->url(fn (): string => Route::has('filament.admin.resources.api-providers.index')
                        ? route('filament.admin.resources.api-providers.index')
                        : url('/admin/api-providers'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.api-providers.*')),
                NavigationItem::make('Revizy Mapping')
                    ->group('RevizySeeder')
                    ->icon('heroicon-o-map')
                    ->sort(5)
                    ->url(fn (): string => Route::has('filament.admin.resources.revizy-curriculum-mappings.index')
                        ? route('filament.admin.resources.revizy-curriculum-mappings.index')
                        : url('/admin/revizy-curriculum-mappings'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.revizy-curriculum-mappings.*')),
                NavigationItem::make('Rapid Labeling')
                    ->group('Tools')
                    ->icon('heroicon-o-bolt')
                    ->url('/rapid-labeling.php'),
            ])
            ->pages([
                Dashboard::class,
                TypecastAudioCredentialsPage::class,
            ])
            ->widgets([
                \App\Filament\Widgets\PageExtractionProgressWidget::class,
                \App\Filament\Widgets\DeepLUsageWidget::class,
                \App\Filament\Widgets\WorkflowQueueWidget::class,
                \App\Filament\Widgets\PagesNeedingExtractionWidget::class,
                \App\Filament\Widgets\FailedJobsWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

}
