<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use App\Filament\Pages\Dashboard;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->passwordReset()
            ->brandName('IPEH')
            ->brandLogo(asset('images/logo.svg'))
            ->favicon(asset('images/log-.svg'))
            ->colors([
                'primary' => Color::hex('#8DC63F'),
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn(): HtmlString => new HtmlString('
                    <link rel="icon" href="/images/log-.svg" type="image/svg+xml">
                    <style>
                        /* ━━━ SIDEBAR ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
                        img.fi-logo {
                            width: 140px !important;
                            height: auto !important;
                        }
                        .fi-sidebar-header {
                            padding-top: 1.25rem !important;
                            padding-bottom: 1.25rem !important;
                        }

                        /* ━━━ LOGIN - FONDO ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
                        .fi-simple-layout {
                            position: relative !important;
                            overflow: hidden !important;
                            background: linear-gradient(150deg, #dff0c4 0%, #eef8dc 40%, #f6fdf0 100%) !important;
                        }

                        /* Orbs de luz que se mueven lentamente en el fondo */
                        .fi-simple-layout::before {
                            content: "";
                            position: absolute;
                            inset: 0;
                            z-index: 0;
                            pointer-events: none;
                            background:
                                radial-gradient(ellipse 560px 560px at 90%  -10%, rgba(141,198,63,0.28) 0%, transparent 65%),
                                radial-gradient(ellipse 400px 400px at  -8%  92%, rgba(141,198,63,0.20) 0%, transparent 65%),
                                radial-gradient(ellipse 300px 300px at  55% 110%, rgba(87,148,20,0.12)  0%, transparent 65%);
                            animation: ipeh-orbs 14s ease-in-out infinite alternate;
                        }
                        @keyframes ipeh-orbs {
                            0%   { transform: scale(1)    translate( 0px,  0px); opacity: 0.75; }
                            33%  { transform: scale(1.04) translate(-10px,  6px); opacity: 1;    }
                            66%  { transform: scale(0.97) translate( 6px, -10px); opacity: 0.85; }
                            100% { transform: scale(1.05) translate(-6px,  4px);  opacity: 1;    }
                        }

                        /* El contenido del card flota sobre los orbs */
                        .fi-simple-main-ctn {
                            position: relative !important;
                            z-index: 1 !important;
                        }

                        /* ━━━ LOGIN - LOGO ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
                        @keyframes ipeh-logo-drop {
                            0%   { opacity: 0; transform: translateY(-30px) scale(0.90); filter: blur(5px); }
                            100% { opacity: 1; transform: translateY(0)     scale(1);    filter: blur(0);   }
                        }
                        @keyframes ipeh-logo-glow {
                            0%, 100% { filter: drop-shadow(0 0  0px rgba(141,198,63,0.0));  }
                            50%       { filter: drop-shadow(0 0 18px rgba(141,198,63,0.50)); }
                        }
                        .fi-simple-layout img.fi-logo {
                            width: 280px !important;
                            height: auto !important;
                            position: relative;
                            z-index: 1;
                            animation:
                                ipeh-logo-drop 0.8s cubic-bezier(0.22, 1, 0.36, 1) both,
                                ipeh-logo-glow 4s 1.2s ease-in-out infinite;
                        }

                        /* ━━━ LOGIN - CARD ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
                        @keyframes ipeh-card-rise {
                            0%   { opacity: 0; transform: translateY(24px) scale(0.97); }
                            100% { opacity: 1; transform: translateY(0)    scale(1);    }
                        }
                        @keyframes ipeh-shimmer {
                            0%   { background-position: -200% center; }
                            100% { background-position:  200% center; }
                        }
                        .fi-simple-main {
                            position: relative !important;
                            overflow: hidden !important;
                            border-radius: 1.5rem !important;
                            border: 1px solid rgba(141,198,63,0.18) !important;
                            background: rgba(255,255,255,0.88) !important;
                            backdrop-filter: blur(20px) !important;
                            -webkit-backdrop-filter: blur(20px) !important;
                            box-shadow:
                                0 10px 48px rgba(141,198,63,0.22),
                                0  2px 10px rgba(0,0,0,0.06),
                                inset 0 1px 0 rgba(255,255,255,0.95) !important;
                            animation: ipeh-card-rise 0.75s 0.25s cubic-bezier(0.22, 1, 0.36, 1) both;
                        }

                        /* Franja shimmer animada en el borde superior del card */
                        .fi-simple-main::before {
                            content: "";
                            position: absolute;
                            top: 0; left: 0; right: 0;
                            height: 3px;
                            z-index: 10;
                            background: linear-gradient(
                                90deg,
                                #579414 0%,
                                #8DC63F 25%,
                                #d6f28c 50%,
                                #8DC63F 75%,
                                #579414 100%
                            );
                            background-size: 200% 100%;
                            animation: ipeh-shimmer 2.5s linear infinite;
                        }

                        /* ━━━ LOGIN - TIPOGRAFÍA ━━━━━━━━━━━━━━━━━━━━━━━━━━ */
                        .fi-simple-header-subheading {
                            font-size: 0.875rem !important;
                            color: #58595B !important;
                            letter-spacing: 0.02em !important;
                            margin-top: 0.25rem !important;
                        }
                    </style>
                '),
            )
            ->navigationGroups([
                NavigationGroup::make('Academia'),
                NavigationGroup::make('Certificados'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
