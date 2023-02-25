<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
    public function boot()
    {
        Validator::extend('legthForType', function ($attribute, $value, $parameters, $validator) {
            // se non mi conosco il type evito la validazione del taxcode           
            if (!isset($parameters[0]) || !in_array($parameters[0], [1, 2, 3, 4])) {
                return true;
            }

            if ($parameters[0] == 4) {
                if (strlen($value) == 16) {
                    return true;
                } else {
                    return false;
                }
            }

            if (in_array($parameters[0], [1, 2, 3])) {
                if (strlen($value) == 11) {
                    return true;
                } else {
                    return false;
                }
            }
        }, 'Tax code must be 16 caracters log with type 4 selected and 11 for the others types.');

        Validator::extend('typeForType', function ($attribute, $value, $parameters, $validator) {
            return true;
            // se non mi conosco il type evito la validazione del taxcode           
            if (!isset($parameters[0]) || !in_array($parameters[0], [1, 2, 3, 4])) {
                return true;
            }

            if ($parameters[0] == 4) {
                if (preg_match('/^([0-9][a-z][A-Z]*)$/', $value)) {
                    return true;
                } else {
                    return false;
                }
            }
            if (in_array($parameters[0], [1, 2, 3])) {
                if (preg_match('/^([0-9]*)$/', $value)) {
                    return true;
                } else {
                    return false;
                }
            }
        }, 'Tax code must contains only numbers and letters with type 4 selected and only numbers for the others types');
    }
}
