<?php

namespace Jorbascrumps\LaravelTombstones\Listeners;

use Jorbascrumps\LaravelTombstones\Contracts\TombstoneWriterContract;
use Jorbascrumps\LaravelTombstones\Events\TombstoneExecuted;

class LogTombstoneExecution
{
    public function __construct(
        private readonly TombstoneWriterContract $writer
    ) {
    }

    public function handle(TombstoneExecuted $event): void
    {
        $this->writer->write(
            $event->label,
            $event->trace,
            $event->context
        );
    }
}
