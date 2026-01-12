<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;
use Psr\Clock\ClockInterface as PsrClockInterface;

final class Clock implements ClockInterface
{
    private static ClockInterface $globalClock;

    public static function get(): ClockInterface
    {
        return self::$globalClock ??= new NativeClock();
    }

    public static function set(PsrClockInterface $clock): void
    {
        self::$globalClock = $clock instanceof ClockInterface ? $clock : new self($clock);
    }

    private function __construct(private readonly ?PsrClockInterface $clock = null) {}

    public function now(): DateTimeImmutable
    {
        return $this->clock->now();
    }

    public function sleep(float|int $seconds): void
    {
        (new NativeClock())->sleep($seconds);
    }
}
