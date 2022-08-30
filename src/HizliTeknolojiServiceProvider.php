<?php
namespace eT2M\HizliTeknoloji;

use Illuminate\Support\ServiceProvider;

class HizliTeknolojiServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }
    public function register()
    {
        $this->app->singleton('hizliteknoloji',function ($app){ 
            return new HizliTeknoloji($app);
        });
    }
}