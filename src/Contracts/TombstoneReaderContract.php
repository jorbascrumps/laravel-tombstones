<?php

namespace Jorbascrumps\LaravelTombstone\Contracts;

use Illuminate\Support\LazyCollection;

interface TombstoneReaderContract
{
    public function read(array $filters = []): LazyCollection;
}
