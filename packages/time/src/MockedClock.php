<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;

use function sprintf;

final class MockedClock implements ClockInterface
{
    /**
     * Returns MockedClock which internally uses provided dateTime.
     */
    public static function with(DateTimeImmutable $dateTime): self
    {
        return new self($dateTime);
    }

    /**
     * Returns MockedClock using current native dateTime.
     */
    public static function withCurrent(): self
    {
        return new self(Clock::get()->now());
    }

    private function __construct(private DateTimeImmutable $dateTime) {}

    public function now(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function sleep(float|int $seconds): void
    {
        $now = (float) $this->dateTime->format('Uu') + $seconds * 1e6;
        $now = substr_replace(sprintf('@%07.0F', $now), '.', -6, 0);
        $timezone = $this->dateTime->getTimezone();

        $this->dateTime = (new DateTimeImmutable($now, $timezone))->setTimezone($timezone);
    }
}
