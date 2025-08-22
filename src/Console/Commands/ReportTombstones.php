<?php

namespace Jorbascrumps\LaravelTombstone\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Jorbascrumps\LaravelTombstone\Contracts\TombstoneReaderContract;
use Jorbascrumps\LaravelTombstone\Filters\DayFilter;
use Jorbascrumps\LaravelTombstone\TombstoneScanner;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class ReportTombstones extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tombstone:report'
                         . '{--days= : The number of days to report tombstones for}'
                         . '{--only= : TODO}';

    /**
     * The console command description.
     */
    protected $description = 'TODO';

    /**
     * Execute the console command.
     */
    public function handle(TombstoneScanner $scanner, TombstoneReaderContract $reader): int
    {
        $filters = [];
        if ($this->option('days')) {
            $filters[] = new DayFilter($this->option('days'));
        }

        $tombstones = $scanner->scan(base_path())
            ->keyBy(fn ($tombstone) => $tombstone->tokenize())
            ->toArray();
        $obituary = $reader->read($filters);
        $numObituaryEntries = 0;

        spin(
            callback: function () use (&$numObituaryEntries, $obituary, &$tombstones) {
                foreach ($obituary as $entry) {
                    $numObituaryEntries++;
                    $token = $entry->tokenize();

                    if (isset($tombstones[$token])) {
                        $tombstones[$token]->timestamp = $entry->timestamp;
                        $tombstones[$token]->context = $entry->context;
                    }
                }
            },
            message: 'Generating report...'
        );

        $tombstones = collect($tombstones)
            ->when($this->option('only'), fn ($tombstones, $only) =>
                match ($only) {
                    'alive' => $tombstones->filter->isAlive(),
                    'dead' => $tombstones->filter->isDead(),
                    default => $tombstones,
                }
            );

        table(
            ['', 'Label', 'File', 'Line', 'Context', 'Last Seen'],
            $tombstones->map(fn ($tombstone) => [
                $tombstone->tokenize(),
                $tombstone->label,
                Str::after($tombstone->getFile(), base_path(DIRECTORY_SEPARATOR)),
                $tombstone->getLine(),
                json_encode($tombstone->getContext()),
                $tombstone->timestamp ? date('Y-m-d H:i:s', $tombstone->timestamp) : null,
            ])
        );

        note(
            sprintf(
                'Found %d tombstones %s obituary entries.',
                $tombstones->count(),
                Number::format($numObituaryEntries)
            )
        );

        return self::SUCCESS;
    }
}
