<?php

declare(strict_types=1);

namespace Par\Time;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Par\Time\Exception\RuntimeException;
use Par\Time\Format\DateTimeFormatter;
use Par\Time\Format\TextStyle;

use function sprintf;

/**
 * A month-of-year, such as 'July'.
 *
 * Month is an enum representing the 12 months of the year - January, February, March, April, May, June, July, August, September, October, November and December.
 *
 * In addition to the textual enum name, each month-of-year has an int value. The int value follows normal usage and the ISO-8601 standard, from 1 (January) to 12 (December). It is recommended that applications use the enum rather than the int value to ensure code clarity.
 */
enum Month: int
{
    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;

    /**
     * Returns a Month from an integer value.
     *
     * @param int<1,12> $month
     */
    public static function fromInt(int $month): self
    {
        return self::from($month);
    }

    /**
     * Returns a Month from a native DateTimeInterface object.
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        return self::from((int) $dateTime->format('n'));
    }

    /**
     * Returns the localized name of the month.
     *
     * @param TextStyle $textStyle the length of the text
     */
    public function getDisplayName(string $locale, TextStyle $textStyle): string
    {
        // Choose the ICU pattern for month only.
        $pattern = match ($textStyle) {
            TextStyle::FULL => 'MMMM',
            TextStyle::FULL_STANDALONE => 'LLLL',
            TextStyle::SHORT => 'MMM',
            TextStyle::SHORT_STANDALONE => 'LLL',
            TextStyle::NARROW => 'MMMMM',
            TextStyle::NARROW_STANDALONE => 'LLLLL',
        };

        $formatter = DateTimeFormatter::ofPattern($pattern, $locale);

        return $formatter->format($this->toNative());
    }

    /**
     * Returns the month that is the specified number of months before this one.
     *
     * The calculation rolls around the start of the year from January to December. The specified period may be negative.
     */
    public function minus(int $months): self
    {
        return $this->plus(-$months);
    }

    /**
     * Returns the month that is the specified number of months after this one.
     *
     * The calculation rolls around the end of the year from December to January. The specified period may be negative.
     */
    public function plus(int $months): self
    {
        $amount = $months % 12;
        $normalized = ($this->value - 1 + $amount + 12) % 12 + 1;

        return self::from($normalized);
    }

    /**
     * Returns the integer value of the month.
     *
     * @return int<1,12>
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * Transforms the Month into a native DateTimeImmutable object.
     *
     * This uses 1st day of the month of the year 2000 at midnight UTC to create a stable date-time.
     */
    public function toNative(): DateTimeImmutable
    {
        try {
            // Create a stable date with the requested month.
            return (new DateTimeImmutable('00:00:00', new DateTimeZone('UTC')))
                ->setDate(2000, $this->value, 1);
        } catch (DateMalformedStringException $e) {
            throw new RuntimeException(
                sprintf(
                    'Failed to transform %s::%s to DateTimeImmutable: %s',
                    self::class,
                    $this->name,
                    $e->getMessage(),
                ),
                $e->getCode(),
                $e,
            );
        }
    }
}
