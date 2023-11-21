<?php

namespace ValeSaude\LaravelHealthCheck;

/**
 * @phpstan-type Metadata array<string, mixed>
 */
class Result
{
    /** @var bool */
    private $healthy;
    /** @var string|null */
    private $message;
    /** @var Metadata */
    private $metadata;

    /**
     * @phpstan-param Metadata $metadata
     */
    public function __construct(bool $healthy, ?string $message = null, array $metadata = [])
    {
        $this->healthy = $healthy;
        $this->message = $message;
        $this->metadata = $metadata;
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @phpstan-return Metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public static function healthy(): Result
    {
        return new self(true);
    }

    /**
     * @phpstan-param Metadata $metadata
     */
    public static function unhealthy(string $message, array $metadata = []): Result
    {
        return new self(false, $message, $metadata);
    }
}