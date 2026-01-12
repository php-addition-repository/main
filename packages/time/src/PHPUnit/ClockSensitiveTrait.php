<?php

declare(strict_types=1);

namespace Par\Time\PHPUnit;

use DateTimeImmutable;
use Par\Time\Clock;
use Par\Time\ClockInterface;
use Par\Time\MockedClock;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;

trait ClockSensitiveTrait
{
    public static function mockTime(bool|DateTimeImmutable $when = true): ClockInterface
    {
        Clock::set(
            match (true) {
                false === $when => self::saveClockBeforeTest(false),
                $when instanceof DateTimeImmutable => MockedClock::with($when),
                default => MockedClock::withCurrent(),
            },
        );

        return Clock::get();
    }

    /**
     * @internal
     */
    #[Before]
    #[BeforeClass]
    public static function saveClockBeforeTest(bool $save = true): ClockInterface
    {
        /** @var ClockInterface|null $originalClock */
        static $originalClock;

        if ($save && $originalClock) {
            self::restoreClockAfterTest();
        }

        return $save ? $originalClock = Clock::get() : $originalClock;
    }

    /**
     * @internal
     */
    #[After]
    protected static function restoreClockAfterTest(): void
    {
        Clock::set(self::saveClockBeforeTest(false));
    }
}
