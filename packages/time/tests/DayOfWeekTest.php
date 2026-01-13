<?php

declare(strict_types=1);

namespace Par\Time\Tests;

use DateTimeImmutable;
use Par\Time\DayOfWeek;
use Par\Time\Format\TextStyle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class DayOfWeekTest extends TestCase
{
    /**
     * @return iterable<string, array{0: DayOfWeek, 1: string, 2: TextStyle, 3: string}>
     */
    public static function provideForGetDisplayName(): iterable
    {
        $dayOfWeek = DayOfWeek::SATURDAY;
        yield 'dutch full' => [$dayOfWeek, 'nl_NL', TextStyle::FULL, 'zaterdag'];
        yield 'dutch full-standalone' => [$dayOfWeek, 'nl_NL', TextStyle::FULL_STANDALONE, 'zaterdag'];
        yield 'dutch short' => [$dayOfWeek, 'nl_NL', TextStyle::SHORT, 'za'];
        yield 'dutch short-standalone' => [$dayOfWeek, 'nl_NL', TextStyle::SHORT_STANDALONE, 'za'];
        yield 'dutch narrow' => [$dayOfWeek, 'nl_NL', TextStyle::NARROW, 'Z'];
        yield 'dutch narrow-standalone' => [$dayOfWeek, 'nl_NL', TextStyle::NARROW_STANDALONE, 'Z'];

        $dayOfWeek = DayOfWeek::MONDAY;
        yield 'english full' => [$dayOfWeek, 'en_US', TextStyle::FULL, 'Monday'];
        yield 'english full-standalone' => [$dayOfWeek, 'en_US', TextStyle::FULL_STANDALONE, 'Monday'];
        yield 'english short' => [$dayOfWeek, 'en_US', TextStyle::SHORT, 'Mon'];
        yield 'english short-standalone' => [$dayOfWeek, 'en_US', TextStyle::SHORT_STANDALONE, 'Mon'];
        yield 'english narrow' => [$dayOfWeek, 'en_US', TextStyle::NARROW, 'M'];
        yield 'english narrow-standalone' => [$dayOfWeek, 'en_US', TextStyle::NARROW_STANDALONE, 'M'];
    }

    /**
     * @return iterable<string, array{0: int, 1: DayOfWeek}>
     */
    public static function provideIntToDayOfWeek(): iterable
    {
        foreach (DayOfWeek::cases() as $day) {
            yield $day->name => [$day->value, $day];
        }
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
        self::assertEquals($expected, DayOfWeek::fromNative($date));
    }

    #[Test]
    public function itCanMinus(): void
    {
        $dayOfWeek = DayOfWeek::MONDAY;

        self::assertSame(DayOfWeek::SATURDAY, $dayOfWeek->minus(2));
        self::assertSame(DayOfWeek::SUNDAY, $dayOfWeek->minus(15));
        self::assertSame(DayOfWeek::TUESDAY, $dayOfWeek->minus(-15));
    }

    #[Test]
    public function itCanPlus(): void
    {
        $dayOfWeek = DayOfWeek::MONDAY;

        self::assertSame(DayOfWeek::WEDNESDAY, $dayOfWeek->plus(2));
        self::assertSame(DayOfWeek::TUESDAY, $dayOfWeek->plus(15));
        self::assertSame(DayOfWeek::SUNDAY, $dayOfWeek->plus(-15));
    }

    #[Test]
    #[DataProvider('provideIntToDayOfWeek')]
    public function itCreatesFromInt(int $intValue, DayOfWeek $dayOfWeek): void
    {
        self::assertEquals($dayOfWeek, DayOfWeek::from($intValue));
        self::assertEquals($dayOfWeek, DayOfWeek::fromInt($intValue));
        self::assertEquals($intValue, $dayOfWeek->toInt());
        self::assertEquals($intValue, $dayOfWeek->value);
    }

    #[Test]
    #[DataProvider('provideForGetDisplayName')]
    public function itShouldDisplayNameAsText(
        DayOfWeek $dayOfWeek,
        string $locale,
        TextStyle $textStyle,
        string $expected,
    ): void {
        self::assertSame($expected, $dayOfWeek->getDisplayName($locale, $textStyle));
    }
}
