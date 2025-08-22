<?php

return [
    'driver' => env('TOMBSTONE_DRIVER', 'local'),

    'path' => env('TOMBSTONE_PATH', storage_path('tombstones')),

    'filename' => env('TOMBSTONE_FILENAME', 'tombstones.jsonl'),

    'reader' => Jorbascrumps\LaravelTombstones\Readers\FilesystemReader::class,

    'writer' => Jorbascrumps\LaravelTombstones\Writers\FilesystemWriter::class,

    'trace_depth' => env('TOMBSTONE_TRACE_DEPTH', 1),

    'include_extensions' => [
        'php',
    ],

    'exclude_directories' => [
        'database',
        'node_modules',
        'storage',
        'tests',
        'vendor',
    ],
];
