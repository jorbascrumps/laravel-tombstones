<?php

namespace Jorbascrumps\LaravelTombstones\Contracts;

use Illuminate\Support\LazyCollection;

interface TombstoneReaderContract
{
    public function read(array $filters = []): LazyCollection;
}
