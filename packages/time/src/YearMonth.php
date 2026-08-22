<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;
use DateTimeInterface;
use Par\Time\Format\DateTimeFormatterBuilder;
use Par\Time\Format\FormatStyle;

final readonly class YearMonth
{
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        $normalized = (new DateTimeImmutable('00:00:00', 'UTC'))->setDate(
            (int) $dateTime->format('Y'),
            (int) $dateTime->format('m'),
            1,
        );

        return new self($normalized);
    }

    private function __construct(private DateTimeImmutable $native) {}

    public function getDisplayName(FormatStyle $formatStyle, ?string $locale = null): string
    {
        return DateTimeFormatterBuilder::create()
                                       ->setLocale($locale)
                                       ->appendLocalized($formatStyle)
                                       ->format($this->native);
    }

    public function getMonth(): Month
    {
        return Month::fromNative($this->native);
    }

    public function getYear(): Year
    {
        return Year::fromNative($this->native);
    }

    public function toNative(): DateTimeImmutable
    {
        return $this->native;
    }
}
