<?php

namespace App\Providers;

use App\Model\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/../Http/helpers.php';
		
		/*$this->app->bind(
                    'Illuminate\Contracts\Auth\Registrar',
                    'App\Services\Registrar'
            );

		$this->app->bind('path.storage', function () {
				return '';
		});*/
    }
}
