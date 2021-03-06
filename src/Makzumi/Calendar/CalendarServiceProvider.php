<?php

namespace Makzumi\Calendar;

use Illuminate\Support\ServiceProvider;

class CalendarServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Share name of the Service
     *
     * @var string
     */
    protected $name = 'calendar';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app[$this->name] = $this->app->bind($this->name, function ($app) {
            return new Calendar;
        });

        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Calendar', 'Makzumi\Calendar\Facades\Calendar');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [$this->name];
    }
}
