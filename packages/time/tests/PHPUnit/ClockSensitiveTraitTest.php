<?php

declare(strict_types=1);

namespace Par\Time\Tests\PHPUnit;

use Par\Time\Clock;
use Par\Time\ClockInterface;
use Par\Time\MockedClock;
use Par\Time\NativeClock;
use Par\Time\PHPUnit\ClockSensitiveTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ClockSensitiveTraitTest extends TestCase
{
    use ClockSensitiveTrait;

    private static ?ClockInterface $clock = null;

    public static function setUpBeforeClass(): void
    {
        self::$clock = self::mockTime();
    }

    public static function tearDownAfterClass(): void
    {
        self::$clock = null;
    }

    public function testMockClock(): void
    {
        self::assertInstanceOf(MockedClock::class, self::$clock);
        self::assertInstanceOf(NativeClock::class, Clock::get());

        $clock = self::mockTime();
        /* @phpstan-ignore-next-line */
        self::assertInstanceOf(MockedClock::class, Clock::get());
        /* @phpstan-ignore-next-line */
        self::assertSame(Clock::get(), $clock);

        /* @phpstan-ignore-next-line */
        self::assertNotSame($clock, self::$clock);

        self::restoreClockAfterTest();
        self::saveClockBeforeTest();

        self::assertInstanceOf(MockedClock::class, self::$clock);
        /* @phpstan-ignore-next-line */
        self::assertInstanceOf(NativeClock::class, Clock::get());

        $clock = self::mockTime();
        /* @phpstan-ignore-next-line */
        self::assertInstanceOf(MockedClock::class, Clock::get());
        /* @phpstan-ignore-next-line */
        self::assertSame(Clock::get(), $clock);

        /* @phpstan-ignore-next-line */
        self::assertNotSame($clock, self::$clock);
    }
}
