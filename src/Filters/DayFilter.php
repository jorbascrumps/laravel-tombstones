<?php

namespace Jorbascrumps\LaravelTombstones\Filters;

use Jorbascrumps\LaravelTombstones\Tombstone;

class DayFilter
{
    protected int $cutoffTimestamp;

    public function __construct(protected int $days = 30)
    {
        $this->cutoffTimestamp = now()
            ->subDays($this->days)
            ->unix();
    }

    public function __invoke(Tombstone $tombstone): bool
    {
        return $tombstone->timestamp && $tombstone->timestamp >= $this->cutoffTimestamp;
    }
}
