<?php

namespace ValeSaude\LaravelHealthCheck\Tests\Commands;

use Illuminate\Support\Facades\Event;
use ValeSaude\LaravelHealthCheck\Events\UnhealthyApplicationComponentsEvent;
use ValeSaude\LaravelHealthCheck\Tests\Dummies\HealthyValidatorDummy;
use ValeSaude\LaravelHealthCheck\Tests\Dummies\UnhealthyValidatorDummy;
use ValeSaude\LaravelHealthCheck\Tests\TestCase;

class RunCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('laravel-health-check.profiles', [
            'profile-1' => [HealthyValidatorDummy::class],
            'profile-2' => [UnhealthyValidatorDummy::class],
        ]);
    }

    public function test_it_runs_only_a_specific_profile_when_profile_option_is_passed(): void
    {
        // When
        $exitCode = $this->artisan('health-check:run', ['--profile' => 'profile-1']);

        // Then
        $this->assertEquals(0, $exitCode);
    }

    public function test_it_runs_all_profiles_when_profile_option_is_not_passed(): void
    {
        // Given
        Event::fake([UnhealthyApplicationComponentsEvent::class]);

        // When
        $exitCode = $this->artisan('health-check:run');

        // Then
        $this->assertEquals(1, $exitCode);
        Event::assertDispatched(
            UnhealthyApplicationComponentsEvent::class,
            function (UnhealthyApplicationComponentsEvent $event) {
                $unhealthyResults = $event->getResultSet()->getUnhealthyResults();

                return 1 === count($unhealthyResults) &&
                    isset($unhealthyResults[UnhealthyValidatorDummy::class]);
            }
        );
    }
}
