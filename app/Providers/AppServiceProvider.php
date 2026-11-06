<?php

namespace App\Providers;

use App\Services\CompanySettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CompanySettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $settings = app(CompanySettings::class)->all();
            config(['company' => array_intersect_key($settings, config('company'))]);
            $view->with('companySettings', $settings);
        });
    }
}
