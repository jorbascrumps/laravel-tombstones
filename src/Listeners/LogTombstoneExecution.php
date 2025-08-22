<?php

namespace Jorbascrumps\LaravelTombstone\Listeners;

use Jorbascrumps\LaravelTombstone\Contracts\TombstoneWriterContract;
use Jorbascrumps\LaravelTombstone\Events\TombstoneExecuted;

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
