<?php

namespace ValeSaude\LaravelHealthCheck\Tests\Validators;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;
use ValeSaude\LaravelHealthCheck\Tests\TestCase;
use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueJobExecutionTimeValidator;

class DatabaseQueueJobExecutionTimeValidatorTest extends TestCase
{
    /** @var DatabaseQueueConfig&MockObject */
    private $configMock;
    /** @var DatabaseQueueJobExecutionTimeValidator */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configMock = $this->createMock(DatabaseQueueConfig::class);
        $this->sut = new DatabaseQueueJobExecutionTimeValidator($this->configMock);

        $this->setupQueueTables();
    }

    protected function tearDown(): void
    {
        $this->teardownQueueTables();

        parent::tearDown();
    }

    public function test_validate_returns_healthy_result_when_there_are_no_running_jobs(): void
    {
        // Given
        $this->addPendingJobToQueue();
        $this->configMock
            ->expects($this->once())
            ->method('getConnectionName')
            ->willReturn('testbench');
        $this->configMock
            ->expects($this->once())
            ->method('getTableName')
            ->willReturn('queues');
        $this->configMock
            ->expects($this->never())
            ->method('getMaxSize');

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertTrue($result->isHealthy());
    }

    public function test_validate_returns_healthy_result_when_all_running_jobs_are_running_within_the_max_execution_time(): void
    {
        // Given
        $nowAsTimestamp = now()->getTimestamp();
        $this->addJobToQueue($nowAsTimestamp, $nowAsTimestamp + 59);
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
            ->method('getMaxExecutionTimeForQueue')
            ->with('default')
            ->willReturn(60);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertTrue($result->isHealthy());
    }

    public function test_it_returns_unhealthy_result_when_a_running_job_is_running_for_more_than_the_max_execution_time(): void
    {
        // Given
        $nowAsTimestamp = now()->getTimestamp();
        $this->addJobToQueue($nowAsTimestamp, $nowAsTimestamp + 61);
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
            ->method('getMaxExecutionTimeForQueue')
            ->with('default')
            ->willReturn(60);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertFalse($result->isHealthy());
        $this->assertEquals(
            'The "default" queue job 1 execution time (61s) exceeds the maximum allowed (60s).',
            $result->getMessage()
        );
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

    private function addPendingJobToQueue(): void
    {
        $this->addJobToQueue(now()->getTimestamp());
    }
}
