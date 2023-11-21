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

    /**
     * @phpstan-return ResultArray
     */
    public function getUnhealthyResults(): array
    {
        return array_filter(
            $this->results,
            static function (Result $result) {
                return !$result->isHealthy();
            }
        );
    }

    public function isHealthy(): bool
    {
        return empty($this->getUnhealthyResults());
    }

    /**
     * @phpstan-return ResultArray
     */
    public function getResults(): array
    {
        return $this->results;
    }
}