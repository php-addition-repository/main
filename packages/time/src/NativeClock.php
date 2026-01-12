<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;

final class NativeClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function sleep(float|int $seconds): void
    {
        if (0 < $s = (int) $seconds) {
            sleep($s);
        }

        if (0 < $us = $seconds - $s) {
            usleep((int) ($us * 1E6));
        }
    }
}
