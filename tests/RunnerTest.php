<?php

namespace ValeSaude\LaravelHealthCheck\Tests;

use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use ValeSaude\LaravelHealthCheck\Config\ProfileConfig;
use ValeSaude\LaravelHealthCheck\Result;
use ValeSaude\LaravelHealthCheck\Runner;

class RunnerTest extends TestCase
{
    /** @var ProfileConfig&MockObject */
    private $profileConfigMock;
    /** @var Container&MockObject */
    private $containerMock;
    /** @var Runner */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profileConfigMock = $this->createMock(ProfileConfig::class);
        $this->containerMock = $this->createMock(Container::class);
        $this->sut = new Runner($this->profileConfigMock, $this->containerMock);
    }

    public function test_run_runs_the_expected_profiles_and_returns_the_result_set(): void
    {
        // Given
        $profile1 = 'profile1';
        $profile2 = 'profile2';
        $validator1 = '\\Validator\\Class\\1';
        $validator2 = '\\Validator\\Class\\2';
        $validator3 = '\\Validator\\Class\\3';
        $validator4 = '\\Validator\\Class\\4';
        $this->profileConfigMock
            ->expects($this->exactly(2))
            ->method('getValidatorsForProfile')
            ->withConsecutive([$profile1], [$profile2])
            ->willReturnOnConsecutiveCalls([$validator1, $validator2], [$validator3, $validator4]);
        $this->containerMock
            ->expects($this->exactly(4))
            ->method('call')
            ->withConsecutive(
                [[$validator1, 'validate']],
                [[$validator2, 'validate']],
                [[$validator3, 'validate']],
                [[$validator4, 'validate']]
            )
            ->willReturnOnConsecutiveCalls(
                Result::healthy(),
                Result::healthy(),
                Result::unhealthy('Some unhealthy message.'),
                Result::healthy()
            );

        // When
        $resultSet = $this->sut->run($profile1, $profile2);

        // Then
        $this->assertFalse($resultSet->isHealthy());
        $this->assertCount(4, $resultSet->getResults());
    }
}
