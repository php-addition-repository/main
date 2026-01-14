<?php

declare(strict_types=1);

namespace Par\Time\Tests;

use DateTimeImmutable;
use Par\Time\DayOfWeek;
use Par\Time\Format\TextStyle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @internal
 */
final class DayOfWeekTest extends TestCase
{
    /**
     * @return iterable<string, array{0: int, 1: DayOfWeek}>
     */
    public static function provideIntToDayOfWeek(): iterable
    {
        foreach (DayOfWeek::cases() as $day) {
            yield $day->name => [$day->value, $day];
        }
    }

    #[Test]
    #[DataProvider('provideIntToDayOfWeek')]
    public function itCanBeCreatedFromInt(int $intValue, DayOfWeek $dayOfWeek): void
    {
        self::assertSame($dayOfWeek, DayOfWeek::from($intValue));
        self::assertSame($dayOfWeek, DayOfWeek::fromInt($intValue));
        self::assertSame($intValue, $dayOfWeek->toInt());
        self::assertSame($intValue, $dayOfWeek->value);
    }

    #[Test]
    public function itThrowsExceptionWhenCreatingFromInvalidInt(): void
    {
        $this->expectException(ValueError::class);

        DayOfWeek::fromInt(0);
    }

    /**
     * @return iterable<string, array{0: DateTimeImmutable, 1: DayOfWeek}>
     */
    public static function provideNativeDateTimes(): iterable
    {
        yield 'monday' => [new DateTimeImmutable('2023-01-02'), DayOfWeek::MONDAY];
        yield 'tuesday' => [new DateTimeImmutable('2023-01-03'), DayOfWeek::TUESDAY];
        yield 'wednesday' => [new DateTimeImmutable('2023-01-04'), DayOfWeek::WEDNESDAY];
        yield 'thursday' => [new DateTimeImmutable('2023-01-05'), DayOfWeek::THURSDAY];
        yield 'friday' => [new DateTimeImmutable('2023-01-06'), DayOfWeek::FRIDAY];
        yield 'saturday' => [new DateTimeImmutable('2023-01-07'), DayOfWeek::SATURDAY];
        yield 'sunday' => [new DateTimeImmutable('2023-01-08'), DayOfWeek::SUNDAY];
    }

    #[Test]
    #[DataProvider('provideNativeDateTimes')]
    public function itCanBeCreatedFromNativeDateTime(DateTimeImmutable $date, DayOfWeek $expected): void
    {
        self::assertSame($expected, DayOfWeek::fromNative($date));
    }

    /**
     * @return iterable<string, array{0: DayOfWeek, 1: int, 2: DayOfWeek}>
     */
    public static function providePlusDays(): iterable
    {
        yield 'Monday + 1 = Tuesday' => [DayOfWeek::MONDAY, 1, DayOfWeek::TUESDAY];
        yield 'Monday + 7 = Monday' => [DayOfWeek::MONDAY, 7, DayOfWeek::MONDAY];
        yield 'Monday + 8 = Tuesday' => [DayOfWeek::MONDAY, 8, DayOfWeek::TUESDAY];
        yield 'Sunday + 1 = Monday' => [DayOfWeek::SUNDAY, 1, DayOfWeek::MONDAY];
        yield 'Monday + 0 = Monday' => [DayOfWeek::MONDAY, 0, DayOfWeek::MONDAY];
        yield 'Monday + -1 = Sunday' => [DayOfWeek::MONDAY, -1, DayOfWeek::SUNDAY];
        yield 'Monday + -7 = Monday' => [DayOfWeek::MONDAY, -7, DayOfWeek::MONDAY];
        yield 'Monday + -8 = Sunday' => [DayOfWeek::MONDAY, -8, DayOfWeek::SUNDAY];
    }

    /**
     * @return iterable<string, array{0: DayOfWeek, 1: int, 2: DayOfWeek}>
     */
    public static function providePlusMinus(): iterable
    {
        yield 'plus 2' => [DayOfWeek::MONDAY, 2, DayOfWeek::WEDNESDAY];
        yield 'plus 15' => [DayOfWeek::MONDAY, 15, DayOfWeek::TUESDAY];
        yield 'plus -15' => [DayOfWeek::MONDAY, -15, DayOfWeek::SUNDAY];
        yield 'minus 2' => [DayOfWeek::MONDAY, -2, DayOfWeek::SATURDAY];
        yield 'minus 15' => [DayOfWeek::MONDAY, -15, DayOfWeek::SUNDAY];
        yield 'minus -15' => [DayOfWeek::MONDAY, 15, DayOfWeek::TUESDAY];
        yield 'plus 0' => [DayOfWeek::FRIDAY, 0, DayOfWeek::FRIDAY];
        yield 'wrap around positive' => [DayOfWeek::SUNDAY, 1, DayOfWeek::MONDAY];
        yield 'wrap around negative' => [DayOfWeek::MONDAY, -1, DayOfWeek::SUNDAY];
    }

    #[Test]
    #[DataProvider('providePlusMinus')]
    public function itCanPlus(DayOfWeek $base, int $amount, DayOfWeek $expected): void
    {
        self::assertSame($expected, $base->plus($amount));
    }

    #[Test]
    #[DataProvider('providePlusMinus')]
    public function itCanMinus(DayOfWeek $base, int $amount, DayOfWeek $expected): void
    {
        self::assertSame($expected, $base->minus(-$amount));
    }

    #[Test]
    public function itCanBeTransformedToNativeDateTime(): void
    {
        $dayOfWeek = DayOfWeek::FRIDAY;
        $native = $dayOfWeek->toNative();

        self::assertSame('2000-01-07 00:00:00', $native->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $native->getTimezone()->getName());
    }

    /**
     * @return iterable<string, array{0: DayOfWeek, 1: string, 2: TextStyle, 3: string}>
     */
    public static function provideForGetDisplayName(): iterable
    {
        $dayOfWeek = DayOfWeek::SATURDAY;
        yield 'Saturday, nl_NL, FULL' => [$dayOfWeek, 'nl_NL', TextStyle::FULL, 'zaterdag'];
        yield 'Saturday, nl_NL, FULL_STANDALONE' => [$dayOfWeek, 'nl_NL', TextStyle::FULL_STANDALONE, 'zaterdag'];
        yield 'Saturday, nl_NL, SHORT' => [$dayOfWeek, 'nl_NL', TextStyle::SHORT, 'za'];
        yield 'Saturday, nl_NL, SHORT_STANDALONE' => [$dayOfWeek, 'nl_NL', TextStyle::SHORT_STANDALONE, 'za'];
        yield 'Saturday, nl_NL, NARROW' => [$dayOfWeek, 'nl_NL', TextStyle::NARROW, 'Z'];
        yield 'Saturday, nl_NL, NARROW_STANDALONE' => [$dayOfWeek, 'nl_NL', TextStyle::NARROW_STANDALONE, 'Z'];

        $dayOfWeek = DayOfWeek::MONDAY;
        yield 'Monday, en_US, FULL' => [$dayOfWeek, 'en_US', TextStyle::FULL, 'Monday'];
        yield 'Monday, en_US, FULL_STANDALONE' => [$dayOfWeek, 'en_US', TextStyle::FULL_STANDALONE, 'Monday'];
        yield 'Monday, en_US, SHORT' => [$dayOfWeek, 'en_US', TextStyle::SHORT, 'Mon'];
        yield 'Monday, en_US, SHORT_STANDALONE' => [$dayOfWeek, 'en_US', TextStyle::SHORT_STANDALONE, 'Mon'];
        yield 'Monday, en_US, NARROW' => [$dayOfWeek, 'en_US', TextStyle::NARROW, 'M'];
        yield 'Monday, en_US, NARROW_STANDALONE' => [$dayOfWeek, 'en_US', TextStyle::NARROW_STANDALONE, 'M'];

        yield 'Wednesday, fr_FR, FULL' => [DayOfWeek::WEDNESDAY, 'fr_FR', TextStyle::FULL, 'mercredi'];
        yield 'Friday, de_DE, FULL' => [DayOfWeek::FRIDAY, 'de_DE', TextStyle::FULL, 'Freitag'];
    }

    #[Test]
    #[DataProvider('provideForGetDisplayName')]
    public function itCanBeFormatted(
        DayOfWeek $dayOfWeek,
        string $locale,
        TextStyle $textStyle,
        string $expected,
    ): void {
        self::assertSame($expected, $dayOfWeek->getDisplayName($locale, $textStyle));
    }
}
