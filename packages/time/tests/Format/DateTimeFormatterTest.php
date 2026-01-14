<?php

declare(strict_types=1);

namespace Par\Time\Tests\Format;

use DateTimeImmutable;
use IntlDateFormatter;
use Par\Time\Format\DateTimeFormatter;
use PHPUnit\Framework\TestCase;

use function is_string;
use function sprintf;

use const LC_TIME;

/**
 * @internal
 */
final class DateTimeFormatterTest extends TestCase
{
    private string|false $defaultLocale = false;

    public function testOfPatternWithCustomLocale(): void
    {
        $this->mockLocale('nl_NL.UTF-8');

        $pattern = 'd MMMM yyyy';
        $formatter = DateTimeFormatter::ofPattern($pattern, 'en_US');
        $dateTime = new DateTimeImmutable('2020-01-01');

        self::assertSame(
            IntlDateFormatter::formatObject($dateTime, $pattern, 'en_US'),
            $formatter->format($dateTime),
        );
    }

    public function testOfPatternWithDefaultLocale(): void
    {
        $expectedLocale = $this->mockLocale('nl_NL.UTF-8');

        $pattern = 'd MMMM yyyy';
        $formatter = DateTimeFormatter::ofPattern($pattern);
        $dateTime = new DateTimeImmutable('2020-01-01');

        self::assertSame(
            IntlDateFormatter::formatObject($dateTime, $pattern, $expectedLocale),
            $formatter->format($dateTime),
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_string($this->defaultLocale)) {
            setlocale(LC_TIME, $this->defaultLocale);
            $this->defaultLocale = false;
        }
    }

    private function mockLocale(string $locale): string
    {
        if (!is_string($this->defaultLocale)) {
            $this->defaultLocale = setlocale(LC_TIME, '0');
        }

        $expectedLocale = setlocale(LC_TIME, $locale);
        if (false === $expectedLocale) {
            self::markTestSkipped(sprintf('Unable to set locale to "%s" for testing.', $locale));
        }

        return $expectedLocale;
    }
}
