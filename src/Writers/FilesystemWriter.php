<?php

namespace Jorbascrumps\LaravelTombstones\Writers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Jorbascrumps\LaravelTombstones\Contracts\TombstoneWriterContract;
use JsonException;

class FilesystemWriter implements TombstoneWriterContract
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

    /**
     * @throws JsonException
     */
    public function write(string $label, array $trace, array $context = [], ?int $executedAt = null): void
    {
        $tombstone = [
            'label' => $label,
            'context' => $context,
            'trace' => $trace,
            'timestamp' => $executedAt ?? now()->unix(),
        ];

        $jsonLine = json_encode($tombstone, JSON_THROW_ON_ERROR) . "\n";

        // Handle local disk differently to avoid memory issues
        if (config('tombstone.driver') === 'local') {
            $filePath = $this->disk->path($this->filename);
            file_put_contents($filePath, $jsonLine, FILE_APPEND | LOCK_EX);
        } else {
            $this->disk->append($this->filename, $jsonLine);
        }
    }
}
