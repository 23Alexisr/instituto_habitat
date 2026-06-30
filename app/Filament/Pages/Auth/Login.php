<?php

namespace App\Filament\Pages\Auth;

use Illuminate\Contracts\Support\Htmlable;

class Login extends \Filament\Pages\Auth\Login
{
    protected static string $view   = 'filament.pages.auth.login';
    protected static string $layout = 'filament.layouts.auth';

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getSubHeading(): string|Htmlable|null
    {
        return null;
    }
}
