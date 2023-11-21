<?php

namespace ValeSaude\LaravelHealthCheck\Tests;

use ValeSaude\LaravelHealthCheck\Result;
use ValeSaude\LaravelHealthCheck\ResultSet;

class ResultSetTest extends TestCase
{
    public function test_is_healthy_returns_true_when_all_results_are_healthy(): void
    {
        // Given
        $sut = new ResultSet([
            '\\Validator\\Class\\1' => Result::healthy(),
            '\\Validator\\Class\\2' => Result::healthy(),
        ]);

        // Then
        $this->assertTrue($sut->isHealthy());
    }

    public function test_is_healthy_returns_false_when_at_least_one_result_is_unhealthy(): void
    {
        // Given
        $sut = new ResultSet([
            '\\Validator\\Class\\1' => Result::healthy(),
            '\\Validator\\Class\\2' => Result::unhealthy('Some unhealthy message.'),
        ]);

        // Then
        $this->assertFalse($sut->isHealthy());
    }
}
