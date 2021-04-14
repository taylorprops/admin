<?php

namespace App\Providers;

use App\Models\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\BrightMLS\CompanyBrightOffices;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

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
            'notifications' => Config::all([
                'config_key','config_value','config_type', 'notify_by_email', 'notify_by_text'
            ])
            -> keyBy('config_key')
            -> transform(function ($setting) {

                $notification = [];

                if($setting -> config_type == 'notification') {

                    if(stristr($setting -> config_value, ',')) {
                        $notification['emails'] = explode(',', $setting -> config_value);
                    }
                    $notification['emails'] = [$setting -> config_value];
                    $notification['notify_by_email'] = $setting -> notify_by_email;
                    $notification['notify_by_text'] = $setting -> notify_by_text;

                    return $notification;

                } else if($setting -> config_type == 'on_off') {

                    $notification['on_off'] = $setting -> config_value;
                    $notification['notify_by_email'] = $setting -> notify_by_email;
                    $notification['notify_by_text'] = $setting -> notify_by_text;

                    return $notification;

                }

                return $setting -> config_value;
            })
            -> toArray()
        ]);

        config([
            'bright_office_codes' => CompanyBrightOffices::all([
                'bright_office_code'
            ])
            -> transform(function ($setting) {

                if(stristr($setting -> bright_office_code, ',')) {
                    return explode(',', $setting -> bright_office_code);
                }

                return $setting -> bright_office_code;

            })
            -> toArray()
        ]);

        //\Debugbar::disable();
    }
}
