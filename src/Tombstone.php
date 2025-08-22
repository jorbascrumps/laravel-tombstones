<?php

namespace Jorbascrumps\LaravelTombstone;

class Tombstone
{
    private string $token;

    public function __construct(
        public string $label,
        public array $trace,
        public array $context = [],
        public ?string $timestamp = null
    ) {
    }

    public function getFile(): string
    {
        return $this->trace[0]['file'];
    }

    public function getLine(): int
    {
        return $this->trace[0]['line'];
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function tokenize(): string
    {
        if (isset($this->token)) {
            return $this->token;
        }

        // Use crc32 directly for even more compact storage (4 bytes vs 8 chars)
        return $this->token = (string) crc32(implode('|', [
            $this->label,
            $this->getFile(),
            $this->getLine(),
        ]));
    }

    public function isAlive(): bool
    {
        return ! $this->isDead();
    }

    public function isDead(): bool
    {
        return $this->timestamp === null;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'trace' => $this->trace,
            'context' => $this->context,
            'timestamp' => $this->timestamp,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            trace: $data['trace'],
            context: $data['context'] ?? [],
            timestamp: $data['timestamp'] ?? null
        );
    }
}
