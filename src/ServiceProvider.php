<?php

namespace Jorbascrumps\LaravelTombstone;

use Illuminate\Support\Facades\Event;
use Jorbascrumps\LaravelTombstone\Console\Commands\ReportTombstones;
use Jorbascrumps\LaravelTombstone\Contracts\TombstoneReaderContract;
use Jorbascrumps\LaravelTombstone\Contracts\TombstoneWriterContract;
use Jorbascrumps\LaravelTombstone\Events\TombstoneExecuted;
use Jorbascrumps\LaravelTombstone\Listeners\LogTombstoneExecution;

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
