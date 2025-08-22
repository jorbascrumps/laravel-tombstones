<?php

namespace Jorbascrumps\LaravelTombstones;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class TombstoneScanner
{
    use Conditionable;

    private const TOMBSTONE_REGEX = '/tombstone\s*\(\s*[\'"]([^\'"]+)[\'"]/';

    protected array $includedExtensions;

    protected array $excludedDirectories;

    public function __construct()
    {
        $this->includedExtensions = config('tombstone.include_extensions', []);
        $this->excludedDirectories = config('tombstone.exclude_directories', []);
    }

    public function scan(string $path): LazyCollection
    {
        $finder = Finder::create()
            ->files()
            ->in($path)
            ->name($this->buildNamePattern())
            ->notPath($this->excludedDirectories);

        return LazyCollection::make(function () use ($finder) {
            foreach ($finder as $file) {
                if (! $this->filterTombstones($file)) {
                    continue;
                }

                foreach ($this->extractTombstones($file) as $tombstone) {
                    yield $tombstone;
                }
            }
        });
    }

    protected function buildNamePattern(): array
    {
        return array_map(static fn($ext) => "*.$ext", $this->includedExtensions);
    }

    protected function filterTombstones(SplFileInfo $file): bool
    {
        return preg_match(self::TOMBSTONE_REGEX, $file->getContents());
    }

    protected function extractTombstones(SplFileInfo $file): Collection {
        $contents = $file->getContents();

        preg_match_all(
            self::TOMBSTONE_REGEX,
            $contents,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        return collect($matches[1])->map(fn($match) => new Tombstone(
            label: $match[0],
            trace: [[
                'file' => $file->getRealPath(),
                'line' => substr_count($contents, "\n", 0, $match[1]) + 1,
            ]]
        ));
    }
}
