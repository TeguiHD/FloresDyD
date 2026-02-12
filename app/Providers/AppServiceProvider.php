<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

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
        // Forzar HTTPS en producción
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Super Admin tiene todos los permisos
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Directivas Blade personalizadas
        Blade::directive('price', function ($expression) {
            return "<?php echo '$' . number_format($expression, 2); ?>";
        });

        // Componentes anónimos con prefijo
        Blade::anonymousComponentPath(
            resource_path('views/components'),
            'flores'
        );
    }
}
