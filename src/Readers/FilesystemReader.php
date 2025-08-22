<?php

namespace Jorbascrumps\LaravelTombstone\Readers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Jorbascrumps\LaravelTombstone\Contracts\TombstoneReaderContract;
use Jorbascrumps\LaravelTombstone\Tombstone;
use JsonException;

class FilesystemReader implements TombstoneReaderContract
{
    protected Filesystem $disk;

    protected string $filename;

    public function __construct()
    {
        $this->disk = Storage::build([
            'driver' => config('tombstone.driver'),
            'root' => config('tombstone.path'),
        ]);

        $this->filename = config('tombstone.filename');
    }

    public function read(array $filters = []): LazyCollection
    {
        return LazyCollection::make(function () use ($filters) {
            $handle = $this->disk->readStream($this->filename);
            $lineNumber = 0;

            try {
                while (($line = fgets($handle)) !== false) {
                    $lineNumber++;

                    try {
                        $tombstone = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                        $tombstone = Tombstone::fromArray($tombstone);
                    } catch (JsonException $e) {
                        // TODO: Report error?

                        continue;
                    }

                    foreach ($filters as $filter) {
                        if (! $filter($tombstone)) {
                            continue 2;
                        }
                    }

                    yield $tombstone;
                }
            } finally {
                fclose($handle);
            }
        });
    }
}
