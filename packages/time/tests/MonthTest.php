<?php

declare(strict_types=1);

namespace Par\Time\Tests;

use DateTimeImmutable;
use Par\Time\Format\TextStyle;
use Par\Time\Month;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @internal
 */
final class MonthTest extends TestCase
{
    /**
     * @return iterable<string, array{0: Month, 1: string, 2: TextStyle, 3: string}>
     */
    public static function provideForGetDisplayName(): iterable
    {
        $month = Month::JANUARY;
        yield 'January, nl_NL, FULL' => [$month, 'nl_NL', TextStyle::FULL, 'januari'];
        yield 'January, nl_NL, FULL_STANDALONE' => [$month, 'nl_NL', TextStyle::FULL_STANDALONE, 'januari'];
        yield 'January, nl_NL, SHORT' => [$month, 'nl_NL', TextStyle::SHORT, 'jan'];
        yield 'January, nl_NL, SHORT_STANDALONE' => [$month, 'nl_NL', TextStyle::SHORT_STANDALONE, 'jan'];
        yield 'January, nl_NL, NARROW' => [$month, 'nl_NL', TextStyle::NARROW, 'J'];
        yield 'January, nl_NL, NARROW_STANDALONE' => [$month, 'nl_NL', TextStyle::NARROW_STANDALONE, 'J'];

        $month = Month::DECEMBER;
        yield 'December, en_US, FULL' => [$month, 'en_US', TextStyle::FULL, 'December'];
        yield 'December, en_US, FULL_STANDALONE' => [$month, 'en_US', TextStyle::FULL_STANDALONE, 'December'];
        yield 'December, en_US, SHORT' => [$month, 'en_US', TextStyle::SHORT, 'Dec'];
        yield 'December, en_US, SHORT_STANDALONE' => [$month, 'en_US', TextStyle::SHORT_STANDALONE, 'Dec'];
        yield 'December, en_US, NARROW' => [$month, 'en_US', TextStyle::NARROW, 'D'];
        yield 'December, en_US, NARROW_STANDALONE' => [$month, 'en_US', TextStyle::NARROW_STANDALONE, 'D'];

        yield 'July, fr_FR, FULL' => [Month::JULY, 'fr_FR', TextStyle::FULL, 'juillet'];
        yield 'August, de_DE, FULL' => [Month::AUGUST, 'de_DE', TextStyle::FULL, 'August'];
    }

    /**
     * @return iterable<string, array{0: int, 1: Month}>
     */
    public static function provideIntToMonth(): iterable
    {
        foreach (Month::cases() as $month) {
            yield $month->name => [$month->value, $month];
        }
    }

    /**
     * @return iterable<string, array{0: DateTimeImmutable, 1: Month}>
     */
    public static function provideNativeDateTimes(): iterable
    {
        yield 'january' => [new DateTimeImmutable('2023-01-02'), Month::JANUARY];
        yield 'february' => [new DateTimeImmutable('2023-02-03'), Month::FEBRUARY];
        yield 'march' => [new DateTimeImmutable('2023-03-04'), Month::MARCH];
        yield 'april' => [new DateTimeImmutable('2023-04-05'), Month::APRIL];
        yield 'may' => [new DateTimeImmutable('2023-05-06'), Month::MAY];
        yield 'june' => [new DateTimeImmutable('2023-06-07'), Month::JUNE];
        yield 'july' => [new DateTimeImmutable('2023-07-08'), Month::JULY];
        yield 'august' => [new DateTimeImmutable('2023-08-09'), Month::AUGUST];
        yield 'september' => [new DateTimeImmutable('2023-09-10'), Month::SEPTEMBER];
        yield 'october' => [new DateTimeImmutable('2023-10-11'), Month::OCTOBER];
        yield 'november' => [new DateTimeImmutable('2023-11-12'), Month::NOVEMBER];
        yield 'december' => [new DateTimeImmutable('2023-12-13'), Month::DECEMBER];
    }

    /**
     * @return iterable<string, array{0: Month, 1: int, 2: Month}>
     */
    public static function providePlusMinus(): iterable
    {
        yield 'plus 2' => [Month::JANUARY, 2, Month::MARCH];
        yield 'plus 15' => [Month::JANUARY, 15, Month::APRIL];
        yield 'plus -15' => [Month::JANUARY, -15, Month::OCTOBER];
        yield 'minus 2' => [Month::JANUARY, -2, Month::NOVEMBER];
        yield 'minus 15' => [Month::JANUARY, -15, Month::OCTOBER];
        yield 'minus -15' => [Month::JANUARY, 15, Month::APRIL];
        yield 'plus 0' => [Month::JUNE, 0, Month::JUNE];
        yield 'wrap around positive' => [Month::DECEMBER, 1, Month::JANUARY];
        yield 'wrap around negative' => [Month::JANUARY, -1, Month::DECEMBER];
    }

    #[Test]
    #[DataProvider('provideIntToMonth')]
    public function itCanBeCreatedFromInt(int $intValue, Month $month): void
    {
        self::assertSame($month, Month::from($intValue));
        self::assertSame($month, Month::fromInt($intValue));
        self::assertSame($intValue, $month->toInt());
        self::assertSame($intValue, $month->value);
    }

    #[Test]
    #[DataProvider('provideNativeDateTimes')]
    public function itCanBeCreatedFromNativeDateTime(DateTimeImmutable $date, Month $expected): void
    {
        self::assertSame($expected, Month::fromNative($date));
    }

    #[Test]
    #[DataProvider('provideForGetDisplayName')]
    public function itCanBeFormatted(
        Month $month,
        string $locale,
        TextStyle $textStyle,
        string $expected,
    ): void {
        self::assertSame($expected, $month->getDisplayName($textStyle, $locale));
    }

    #[Test]
    public function itCanBeTransformedToNativeDateTime(): void
    {
        $month = Month::FEBRUARY;
        $native = $month->toNative();

        self::assertSame('2000-02-01 00:00:00', $native->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $native->getTimezone()->getName());
    }

    #[Test]
    #[DataProvider('providePlusMinus')]
    public function itCanMinus(Month $base, int $amount, Month $expected): void
    {
        self::assertSame($expected, $base->minus(-$amount));
    }

    #[Test]
    #[DataProvider('providePlusMinus')]
    public function itCanPlus(Month $base, int $amount, Month $expected): void
    {
        self::assertSame($expected, $base->plus($amount));
    }

    #[Test]
    public function itThrowsExceptionWhenCreatingFromInvalidInt(): void
    {
        $this->expectException(ValueError::class);

        Month::fromInt(0);
    }
}
