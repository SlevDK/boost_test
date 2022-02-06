<?php

namespace App\Providers;

use App\Models\Song;
use App\Repositories\SongRepository;
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
    public function boot()
    {
        $this->app->singleton(SongRepository::class, function() {
            return new SongRepository(Song::class);
        });
    }
}
