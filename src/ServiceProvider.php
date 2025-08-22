<?php

namespace Jorbascrumps\LaravelTombstones;

use Illuminate\Support\Facades\Event;
use Jorbascrumps\LaravelTombstones\Console\Commands\ReportTombstones;
use Jorbascrumps\LaravelTombstones\Contracts\TombstoneReaderContract;
use Jorbascrumps\LaravelTombstones\Contracts\TombstoneWriterContract;
use Jorbascrumps\LaravelTombstones\Events\TombstoneExecuted;
use Jorbascrumps\LaravelTombstones\Listeners\LogTombstoneExecution;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/tombstone.php' => config_path('tombstone.php'),
        ]);

        Event::listen(TombstoneExecuted::class, LogTombstoneExecution::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ReportTombstones::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tombstone.php', 'tombstone');

        $this->app->bind(TombstoneReaderContract::class, config('tombstone.reader'));
        $this->app->bind(TombstoneWriterContract::class, config('tombstone.writer'));
    }
}
