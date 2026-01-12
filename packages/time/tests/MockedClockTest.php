<?php

declare(strict_types=1);

namespace Par\Time\Tests;

use DateTimeImmutable;
use Par\Time\Clock;
use Par\Time\ClockInterface;
use Par\Time\MockedClock;
use Par\Time\PHPUnit\ClockSensitiveTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MockedClockTest extends TestCase
{
    use ClockSensitiveTrait;

    #[Test]
    public function nowReturnsMockedTime(): void
    {
        $dt = new DateTimeImmutable('2020-01-01T00:00:00Z');
        $clock = MockedClock::with($dt);
        self::assertEquals($dt, $clock->now());
    }

    #[Test]
    public function sleepAdvancesTime(): void
    {
        $dt = new DateTimeImmutable('2020-01-01T00:00:00Z');
        $clock = MockedClock::with($dt);
        $clock->sleep(2.5);
        $expected = $dt->modify('+2 seconds')->modify('+500000 microseconds');
        self::assertEqualsWithDelta($expected->getTimestamp(), $clock->now()->getTimestamp(), 1);
    }

    #[Test]
    public function withCurrentUsesNow(): void
    {
        $dt = new DateTimeImmutable('2020-01-01T00:00:00Z');
        $mock = self::createStub(ClockInterface::class);
        $mock->method('now')->willReturn($dt);
        self::saveClockBeforeTest();
        Clock::set($mock);

        $clock = MockedClock::withCurrent();

        self::assertSame($dt, $clock->now());
    }
}
