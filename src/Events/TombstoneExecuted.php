<?php

namespace Jorbascrumps\LaravelTombstone\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TombstoneExecuted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $label,
        public array $trace,
        public array $context = [],
        public ?int $executedAt = null
    ) {
        $this->executedAt ??= now()->unix();
    }
}
