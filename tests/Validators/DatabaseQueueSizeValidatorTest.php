<?php

namespace ValeSaude\LaravelHealthCheck\Tests\Validators;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;
use ValeSaude\LaravelHealthCheck\Tests\TestCase;
use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueSizeValidator;

class DatabaseQueueSizeValidatorTest extends TestCase
{
    /** @var DatabaseQueueConfig&MockObject */
    private $configMock;
    /** @var DatabaseQueueSizeValidator */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configMock = $this->createMock(DatabaseQueueConfig::class);
        $this->sut = new DatabaseQueueSizeValidator($this->configMock);

        $this->setupQueueTables();
    }

    protected function tearDown(): void
    {
        $this->teardownQueueTables();

        parent::tearDown();
    }

    public function test_validate_returns_healthy_result_when_current_size_is_lesser_than_max_size(): void
    {
        // Given
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
            ->method('getMaxSize')
            ->willReturn(1);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertTrue($result->isHealthy());
    }

    public function test_validate_returns_unhealthy_result_when_current_size_is_greater_than_max_size(): void
    {
        // Given
        $this->addJobsToQueue('some-queue');
        $this->addJobsToQueue('default', 2);
        $this->configMock
            ->expects($this->exactly(3))
            ->method('getConnectionName')
            ->willReturn('testbench');
        $this->configMock
            ->expects($this->exactly(3))
            ->method('getTableName')
            ->willReturn('queues');
        $this->configMock
            ->expects($this->once())
            ->method('getMaxSize')
            ->willReturn(1);

        // When
        $result = $this->sut->validate();

        // Then
        $this->assertFalse($result->isHealthy());
        $this->assertEquals(
            'The "default" queue size (2) exceeds the maximum allowed (1).',
            $result->getMessage()
        );
    }

    private function addJobsToQueue(string $queue = 'default', int $count = 1): void
    {
        foreach (range(1, $count) as $i) {
            DB::connection('testbench')
                ->table('queues')
                ->insert([
                    'queue' => $queue,
                    'payload' => 'test',
                    'attempts' => 0,
                    'reserved_at' => null,
                    'available_at' => 0,
                    'created_at' => 0,
                ]);
        }
    }
}
