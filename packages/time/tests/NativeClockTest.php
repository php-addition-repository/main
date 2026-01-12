<?php

declare(strict_types=1);

namespace Par\Time\Tests;

use Par\Time\NativeClock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class NativeClockTest extends TestCase
{
    #[Test]
    public function nowCreatesNewDateTimeImmutable(): void
    {
        $clock = new NativeClock();

        $first = $clock->now();
        usleep(1);
        $second = $clock->now();

        self::assertNotEquals($first, $second);
    }

    #[Test]
    public function sleepHoldsExecution(): void
    {
        $clock = new NativeClock();
        $start = hrtime(true);
        $clock->sleep(1.1);
        $elapsed = hrtime(true) - $start;
        self::assertEquals(1.1, round($elapsed / 1000_000_000, 1));
    }
}
