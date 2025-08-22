<?php

use Jorbascrumps\LaravelTombstone\Events\TombstoneExecuted;

if (! function_exists('tombstone')) {
    function tombstone(string $label, array $context = []): void {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, config('tombstone.trace_depth'));

        TombstoneExecuted::dispatch($label, $trace, $context);
    }
}

if (! function_exists('tokenizeTombstone')) {
    function tokenizeTombstone(array $tombstone): string {
        return implode('|', [
            $tombstone['label'],
            $tombstone['trace'][0]['file'],
            $tombstone['trace'][0]['line'],
        ]);
    }
}
