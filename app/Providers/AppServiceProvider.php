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
     * El servidor no arranca hasta que $proyectoListo sea true.
     */
    private function guardProjectStart(): void
    {
        $proyectoListo = false;

        $command = $_SERVER['argv'][1] ?? null;

        if ($command === 'serve' && $proyectoListo !== true) {
            throw new \RuntimeException(
                'El servidor no arranca. Abre app/Providers/AppServiceProvider.php y pon $proyectoListo en true.'
            );
        }
    }
}
