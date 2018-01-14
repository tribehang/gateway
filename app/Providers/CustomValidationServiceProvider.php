<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CustomValidationServiceProvider extends ServiceProvider
{
    const BASE64_PNG = 'data:image/png;base64';
    const BASE64_JPG = 'data:image/jpg;base64';
    const BASE64_JPEG = 'data:image/jpeg;base64';

    private $validBase64ImageTypes = [
        self::BASE64_PNG,
        self::BASE64_JPG,
        self::BASE64_JPEG,
    ];
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setBase64ImageValidation();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    protected function setBase64ImageValidation()
    {
        Validator::extend('base64Image', function ($attribute, $value, $parameters, $validator) {
            preg_match('/^data:.*;base64/', $value, $match);

            return (! empty($match) && (in_array($match[0], $this->validBase64ImageTypes)));
        });

        Validator::replacer('base64Image', function ($message, $attribute, $rule, $parameters) {
            return 'Image type must be one of these formats: ' . implode(',', $this->validBase64ImageTypes);
        });
    }
}
