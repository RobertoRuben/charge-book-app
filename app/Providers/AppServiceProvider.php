<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Establecer español como idioma predeterminado
        App::setLocale('es');
        
        // Configurar Language Switch
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es']) // Solo español
                ->labels([
                    'es' => 'Español',
                ])
                ->displayLocale('es'); // Mostrar etiquetas en español
        });
    }
}