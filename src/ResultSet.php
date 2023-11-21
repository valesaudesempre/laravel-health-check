<?php

namespace ValeSaude\LaravelHealthCheck;

/**
 * @phpstan-type ResultArray array<class-string, Result>
 */
class ResultSet
{
    /** @var ResultArray */
    private $results;

    /**
     * @phpstan-param ResultArray $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function isHealthy(): bool
    {
        foreach ($this->results as $result) {
            if (!$result->isHealthy()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @phpstan-return ResultArray
     */
    public function getResults(): array
    {
        return $this->results;
    }
}