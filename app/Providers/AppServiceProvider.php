<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   public function register(): void
    {
    //       $this->app->singleton(\App\Services\NotificationService::class, function ($app) {
    //     return new \App\Services\NotificationService();
    // });
    }

    /**
     * Bootstrap any application services.
     */ 
     
  public function boot(): void
  {
    // Schedule::command('bidding:process')->everyMinute();
   }
   
}
