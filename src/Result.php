<?php

namespace ValeSaude\LaravelHealthCheck;

class Result
{
    /** @var bool */
    private $healthy;
    /** @var string|null */
    private $message;

    public function __construct(bool $healthy, ?string $message = null)
    {
        $this->healthy = $healthy;
        $this->message = $message;
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public static function healthy(): Result
    {
        return new self(true);
    }

    public static function unhealthy(string $message): Result
    {
        return new self(false, $message);
    }
}