<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Validator;
use Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // force https - konfigurasi sesuai kebutuhan
//        if (config('app.env') === 'staging' || config('app.env') === 'training' || config('app.env') === 'production') {
//            \URL::forceScheme('https');
//        }

        // create new rule validation
        Validator::extend('gte', function ($attribute, $value, $parameters) {
            if (isset($parameters[1])) {
                $other = $parameters[1];

                return intval($value) >= intval($other);
            } else {
                return true;
            }
        });

        Validator::replacer('gte', function ($message, $attribute, $rule, $params) {
            return str_replace('_', ' ', 'The ' . $attribute . ' must be greater than the ' . $params[0]);
        });

        Storage::extend('sftp', function ($app, $config) {
            return new Filesystem(new SftpAdapter($config));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
