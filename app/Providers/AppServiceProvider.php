<?php

namespace App\Providers;

use App\Models\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        Schema::defaultStringLength(191);
        date_default_timezone_set('America/New_York');

        // add custom config vars from config table
        config([
            'global_db' => Config::all([
                'config_key','config_value','config_type'
            ])
            -> keyBy('config_key')
            -> transform(function ($setting) {

                if($setting -> config_type == 'emails') {
                    if(stristr($setting -> config_value, ',')) {
                        return explode(',', $setting -> config_value);
                    }
                    return [$setting -> config_value];
                }

                return $setting -> config_value;
            })
            -> toArray()
        ]);

        //\Debugbar::disable();
    }
}
