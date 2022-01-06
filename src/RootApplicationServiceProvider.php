<?php

namespace Cone\Root;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RootApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerAuth();
    }

    /**
     * Register the default authorization.
     *
     * @return void
     */
    protected function registerAuth(): void
    {
        Gate::define('viewRoot', static function (): bool {
            return true;
        });
    }
}
