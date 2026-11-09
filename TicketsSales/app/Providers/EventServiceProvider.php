<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Event;
use App\Observers\EventObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::observe(EventObserver::class);
        Admin::observe(AdminObserver::class);
    }


}
