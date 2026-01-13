<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Exception\RuntimeException;
use Par\Time\Format\TextStyle;

enum DayOfWeek: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;

    /**
     * @param int<1,7> $dayOfWeek
     */
    public static function fromInt(int $dayOfWeek): self
    {
        return self::from($dayOfWeek);
    }

    /**
     * Creates a DayOfWeek from a native DateTimeInterface object.
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        // 1 (Mon) to 7 (Sun)
        $isoDayOfWeek = (int) $dateTime->format('N');

        return self::from($isoDayOfWeek);
    }

    public function getDisplayName(string $locale, TextStyle $textStyle): string
    {
        if (!class_exists(IntlDateFormatter::class)) {
            throw new InvalidArgumentException('The intl extension (IntlDateFormatter) is required.');
        }

        // Create a stable date with the requested ISO weekday.
        // ISO week: setISODate(year, week, isoDayOfWeek) → isoDayOfWeek: 1=Mon .. 7=Sun
        $date = (new DateTimeImmutable('00:00:00', new DateTimeZone('UTC')))
            ->setISODate(2000, 1, $this->value);

        // Choose the ICU pattern for weekday only.
        $pattern = match ($textStyle) {
            TextStyle::FULL => 'EEEE',
            TextStyle::FULL_STANDALONE => 'cccc',
            TextStyle::SHORT => 'EEE',
            TextStyle::SHORT_STANDALONE => 'ccc',
            TextStyle::NARROW => 'EEEEE',
            TextStyle::NARROW_STANDALONE => 'ccccc',
        };

        // Use Gregorian calendar; we only care about formatting the weekday name.
        $formatter = new IntlDateFormatter(
            locale: $locale,
            dateType: IntlDateFormatter::NONE,
            timeType: IntlDateFormatter::NONE,
            timezone: 'UTC',
            calendar: IntlDateFormatter::GREGORIAN,
            pattern: $pattern,
        );

        $text = $formatter->format($date);

        if (false === $text) {
            // If formatting fails, surface a useful error.
            $err = intl_get_error_message();
            throw new RuntimeException("Failed to format weekday: $err");
        }

        return $text;
    }

    /**
     * Returns the day-of-week that is the specified number of days before this one.
     *
     * The calculation rolls around the start of the year from Monday to Sunday. The specified period may be negative.
     */
    public function minus(int $days): self
    {
        return $this->plus(-$days);
    }

    /**
     * Returns the day-of-week that is the specified number of days after this one.
     *
     * The calculation rolls around the end of the week from Sunday to Monday. The specified period may be negative.
     */
    public function plus(int $days): self
    {
        $normalized = (($this->value - 1 + $days) % 7 + 7) % 7 + 1;

        return self::from($normalized);
    }

    /**
     * Returns the integer value of the day-of-week.
     *
     * @return int<1,7>
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
