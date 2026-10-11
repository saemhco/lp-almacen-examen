<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->guardProjectStart();
    }

    /**
     * Ningún endpoint responde hasta que $proyectoListo sea true.
     * Las pruebas automáticas no quedan bloqueadas.
     */
    private function guardProjectStart(): void
    {
        if ($this->app->runningUnitTests()) {
            config(['proyecto.listo' => true]);

            return;
        }

        $proyectoListo = false;

        config(['proyecto.listo' => $proyectoListo]);
    }
}
