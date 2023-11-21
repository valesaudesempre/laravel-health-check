<?php

namespace ValeSaude\LaravelHealthCheck\Tests\Validators;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;
use ValeSaude\LaravelHealthCheck\Tests\TestCase;
use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueStuckJobValidator;

class DatabaseQueueStuckJobValidatorTest extends TestCase
{
    /** @var DatabaseQueueConfig&MockObject */
    private $configMock;
    /** @var DatabaseQueueStuckJobValidator() */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configMock = $this->createMock(DatabaseQueueConfig::class);
        $this->sut = new DatabaseQueueStuckJobValidator($this->configMock);

        $this->setupQueueTables();
    }

    protected function tearDown(): void
    {
        $this->teardownQueueTables();

        parent::tearDown();
    }

    public function test_validate_returns_healthy_result_when_there_are_no_stuck_jobs(): void
    {
        // Given
        $nowAsTimestamp = now()->getTimestamp();
        $this->addJobToQueue($nowAsTimestamp, $nowAsTimestamp);
        $this->configMock
            ->expects($this->once())
            ->method('getConnectionName')
            ->willReturn('testbench');
        $this->configMock
            ->expects($this->once())
            ->method('getTableName')
            ->willReturn('queues');
        $this->configMock
            ->expects($this->once())
            ->method('getStuckJobThreshold')
            ->willReturn(10);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertTrue($result->isHealthy());
    }

    public function test_validate_returns_healthy_result_when_all_stuck_jobs_are_stuck_for_less_than_the_threshold(): void
    {
        // Given
        $nowAsTimestamp = now()->getTimestamp();
        $this->addJobToQueue($nowAsTimestamp - 9);
        $this->configMock
            ->expects($this->once())
            ->method('getConnectionName')
            ->willReturn('testbench');
        $this->configMock
            ->expects($this->once())
            ->method('getTableName')
            ->willReturn('queues');
        $this->configMock
            ->expects($this->once())
            ->method('getStuckJobThreshold')
            ->willReturn(10);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertTrue($result->isHealthy());
    }

    public function test_it_returns_unhealthy_result_when_there_is_a_stuck_job(): void
    {
        // Given
        $nowAsTimestamp = now()->getTimestamp();
        $this->addJobToQueue($nowAsTimestamp - 11);
        $this->configMock
            ->expects($this->once())
            ->method('getConnectionName')
            ->willReturn('testbench');
        $this->configMock
            ->expects($this->once())
            ->method('getTableName')
            ->willReturn('queues');
        $this->configMock
            ->expects($this->once())
            ->method('getStuckJobThreshold')
            ->willReturn(10);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertFalse($result->isHealthy());
        $this->assertEquals('The "default" queue job 1 is stuck for 11s.', $result->getMessage());
        $this->assertEquals(['payload' => 'test'], $result->getMetadata());
    }

    private function addJobToQueue(int $availableAt, ?int $reservedAt = null): void
    {
        DB::connection('testbench')
            ->table('queues')
            ->insert([
                'queue' => 'default',
                'payload' => 'test',
                'attempts' => 0,
                'reserved_at' => $reservedAt,
                'available_at' => $availableAt,
                'created_at' => $availableAt,
            ]);
    }
}
