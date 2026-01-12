<?php

declare(strict_types=1);

namespace Par\Time;

use Psr\Clock\ClockInterface as PsrClockInterface;

interface ClockInterface extends PsrClockInterface
{
    public function sleep(float|int $seconds): void;
}
