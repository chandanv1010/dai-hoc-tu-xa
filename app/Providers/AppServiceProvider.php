<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Carbon\Carbon;
use App\Http\ViewComposers\MenuComposer;
use App\Http\ViewComposers\CartComposer;
use App\Http\ViewComposers\CustomerComposer;
use App\Models\Language;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        $locale = app()->getLocale(); // vn en cn
        $language = Language::where('canonical', $locale)->first();

        Validator::extend('custom_date_format', function($attribute, $value, $parameters, $validator){
            return Datetime::createFromFormat('d/m/Y H:i', $value) !== false;
        });

        Validator::extend('custom_after', function($attribute, $value, $parameters, $validator){
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $validator->getData()[$parameters[0]]);
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $value);
            
            return $endDate->greaterThan($startDate) !== false;
        });


        view()->composer(['frontend.*', 'mobile.*'], function($view) use ($language){
            $composerClasses = [
                MenuComposer::class,
                CartComposer::class,
                CustomerComposer::class,
            ];

            foreach($composerClasses as $key => $val){
                $composer = app()->make($val, ['language' => $language->id]);
                $composer->compose($view);
            }
        });

      

     

        Schema::defaultStringLength(191);
    }
}
