<?php

declare(strict_types=1);

namespace Par\Time\Tests;

use DateTimeImmutable;
use Par\Time\Clock;
use Par\Time\ClockInterface;
use Par\Time\NativeClock;
use Par\Time\PHPUnit\ClockSensitiveTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface as PsrClockInterface;

/**
 * @internal
 */
final class ClockTest extends TestCase
{
    use ClockSensitiveTrait;

    #[Test]
    public function getReturnsDefaultNativeClock(): void
    {
        $clock = Clock::get();
        self::assertInstanceOf(NativeClock::class, $clock);
    }

    #[Test]
    public function nowWithPsrClockDelegates(): void
    {
        $psrClock = $this->createMock(PsrClockInterface::class);
        $expected = new DateTimeImmutable('2020-01-01T00:00:00Z');
        $psrClock->expects(self::once())
                 ->method('now')
                 ->willReturn($expected);

        Clock::set($psrClock);

        $now = Clock::get()->now();
        self::assertEquals($expected, $now);
    }

    #[Test]
    public function setWithClockMakesItGlobal(): void
    {
        $mock = self::createStub(ClockInterface::class);

        Clock::set($mock);
        self::assertSame($mock, Clock::get());
    }

    #[Test]
    public function setWithPsrClockWrapsIt(): void
    {
        $psrClock = self::createStub(PsrClockInterface::class);

        Clock::set($psrClock);

        self::assertInstanceOf(Clock::class, Clock::get());
    }

    #[Test]
    public function sleepUsesToNativeClock(): void
    {
        $mockClock = self::createStub(PsrClockInterface::class);

        Clock::set($mockClock);
        $start = hrtime(true);
        Clock::get()->sleep(1.1);
        $elapsed = hrtime(true) - $start;
        self::assertEquals(1.1, round($elapsed / 1000_000_000, 1));
    }
}
