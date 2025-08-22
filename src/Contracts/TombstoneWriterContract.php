<?php

namespace Jorbascrumps\LaravelTombstones\Contracts;

interface TombstoneWriterContract
{
    public function write(string $label, array $trace, array $context = []): void;
}
