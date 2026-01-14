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
     * Returns a DayOfWeek from an integer.
     *
     * @param int<1,7> $dayOfWeek
     */
    public static function fromInt(int $dayOfWeek): self
    {
        return self::from($dayOfWeek);
    }

    /**
     * Returns a DayOfWeek from a native DateTimeInterface object.
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        // 1 (Mon) to 7 (Sun)
        $isoDayOfWeek = (int) $dateTime->format('N');

        return self::from($isoDayOfWeek);
    }

    /**
     * Returns the localized name of the day-of-week.
     *
     * @param TextStyle $textStyle the length of the text
     */
    public function getDisplayName(string $locale, TextStyle $textStyle): string
    {
        // Choose the ICU pattern for weekday only.
        $pattern = match ($textStyle) {
            TextStyle::FULL => 'EEEE',
            TextStyle::FULL_STANDALONE => 'cccc',
            TextStyle::SHORT => 'EEE',
            TextStyle::SHORT_STANDALONE => 'ccc',
            TextStyle::NARROW => 'EEEEE',
            TextStyle::NARROW_STANDALONE => 'ccccc',
        };

        $formatter = DateTimeFormatter::ofPattern($pattern, $locale);

        return $formatter->format($this->toNative());
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
        $amount = $days % 7;
        $normalized = ($this->value - 1 + $amount + 7) % 7 + 1;

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

    /**
     * Transforms the DayOfWeek into a native DateTimeImmutable object.
     *
     * This uses 1st week of the year 2000 at midnight UTC to create a stable date-time.
     */
    public function toNative(): DateTimeImmutable
    {
        try {
            // Create a stable date with the requested ISO weekday.
            // ISO week: setISODate(year, week, isoDayOfWeek) → isoDayOfWeek: 1=Mon .. 7=Sun
            return (new DateTimeImmutable('00:00:00', new DateTimeZone('UTC')))
                ->setISODate(2000, 1, $this->value);
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
