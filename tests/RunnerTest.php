<?php

namespace ValeSaude\LaravelHealthCheck\Tests;

use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use ValeSaude\LaravelHealthCheck\Config\ProfileConfig;
use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;
use ValeSaude\LaravelHealthCheck\Result;
use ValeSaude\LaravelHealthCheck\Runner;

class RunnerTest extends TestCase
{
    /** @var ProfileConfig&MockObject */
    private $configMock;
    /** @var Container&MockObject */
    private $containerMock;
    /** @var Runner */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configMock = $this->createMock(ProfileConfig::class);
        $this->containerMock = $this->createMock(Container::class);
        $this->sut = new Runner($this->configMock, $this->containerMock);
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
        $validator1Mock = $this->createMock(ValidatorInterface::class);
        $validator2Mock = $this->createMock(ValidatorInterface::class);
        $validator3Mock = $this->createMock(ValidatorInterface::class);
        $validator4Mock = $this->createMock(ValidatorInterface::class);
        $this->configMock
            ->expects($this->exactly(2))
            ->method('getValidatorsForProfile')
            ->withConsecutive([$profile1], [$profile2])
            ->willReturnOnConsecutiveCalls([$validator1, $validator2], [$validator3, $validator4]);
        $this->containerMock
            ->expects($this->exactly(4))
            ->method('make')
            ->withConsecutive([$validator1], [$validator2], [$validator3], [$validator4])
            ->willReturnOnConsecutiveCalls(
                $validator1Mock,
                $validator2Mock,
                $validator3Mock,
                $validator4Mock
            );
        $validator1Mock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(Result::healthy());
        $validator2Mock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(Result::healthy());
        $validator3Mock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(Result::unhealthy('Some unhealthy message.'));
        $validator4Mock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(Result::healthy());

        // When
        $resultSet = $this->sut->run($profile1, $profile2);

        // Then
        $this->assertFalse($resultSet->isHealthy());
        $this->assertCount(4, $resultSet->getResults());
    }
}
