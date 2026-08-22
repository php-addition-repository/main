<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

final readonly class Year
{
    public static function fromNative(DateTimeInterface $year): self
    {
        return new self((int) $year->format('Y'));
    }

    private function __construct(private int $year) {}

    public function toNative(): DateTimeImmutable
    {
        return (new DateTimeImmutable('00:00:00', new DateTimeZone('UTC')))
            ->setDate($this->year, 1, 1);
    }
}
