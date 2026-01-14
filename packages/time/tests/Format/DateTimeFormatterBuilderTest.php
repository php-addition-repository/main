<?php

declare(strict_types=1);

namespace Par\Time\Tests\Format;

use DateTimeImmutable;
use Par\Time\Exception\RuntimeException;
use Par\Time\Format\DateTimeFormatterBuilder;
use Par\Time\Format\TextStyle;
use Par\Time\Temporal\TemporalTextField;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class DateTimeFormatterBuilderTest extends TestCase
{
    public function testAppendLiteralAddsLiteralToPattern(): void
    {
        $builder = DateTimeFormatterBuilder::create();
        $builder = $builder->appendLiteral('T') // Appending a literal "T"
                           ->setLocale('en_US');

        $dateTime = new DateTimeImmutable('2024-01-01T15:30:00');

        self::assertSame('T', $builder->format($dateTime));
    }

    public function testAppendLiteralEmptyStringSkipsAddition(): void
    {
        $builder = DateTimeFormatterBuilder::create();
        $builder->appendPattern('yyyy')
                ->appendLiteral('') // Appending an empty string should not change the pattern
                ->setLocale('en_US');

        $dateTime = new DateTimeImmutable('2024-01-01');

        self::assertSame('2024', $builder->format($dateTime));
    }

    public function testAppendLiteralWithSpecialCharacters(): void
    {
        $builder = DateTimeFormatterBuilder::create();
        $builder->appendLiteral("It's ") // Appending a literal with special characters
                ->appendPattern('yyyy')
                ->setLocale('en_US');

        $dateTime = new DateTimeImmutable('2024-01-01');

        self::assertSame("It's 2024", $builder->format($dateTime));
    }

    public function testAppendTextAppendsToPattern(): void
    {
        $field1 = self::createStub(TemporalTextField::class);
        $field1->method('getPattern')
               ->with(TextStyle::FULL)
               ->willReturn('EEEE');

        $field2 = self::createStub(TemporalTextField::class);
        $field2->method('getPattern')
               ->with(TextStyle::SHORT)
               ->willReturn('MMM');

        $builder = DateTimeFormatterBuilder::create();
        $builder->setLocale('en_US')
                ->appendText($field1, TextStyle::FULL)
                ->appendText($field2, TextStyle::SHORT);

        $dateTime = new DateTimeImmutable('2024-01-01'); // Monday, January

        self::assertSame('MondayJan', $builder->format($dateTime));
    }

    public function testFormatBuildsAndFormats(): void
    {
        $field = self::createStub(TemporalTextField::class);
        $field->method('getPattern')->willReturn('yyyy');

        $builder = DateTimeFormatterBuilder::create();
        $builder->appendText($field, TextStyle::FULL)
                ->setLocale('en_US');

        $dateTime = new DateTimeImmutable('2024-01-01');

        self::assertSame('2024', $builder->format($dateTime));
    }

    public function testItCanBeBuiltWithEmptyPattern(): void
    {
        $this->expectException(RuntimeException::class);

        DateTimeFormatterBuilder::create()->build();
    }

    public function testSetLocaleAffectsBuiltFormatter(): void
    {
        $field = self::createStub(TemporalTextField::class);
        $field->method('getPattern')->willReturn('MMMM');

        $builder = DateTimeFormatterBuilder::create();
        $builder->appendText($field, TextStyle::FULL);

        $dateTime = new DateTimeImmutable('2024-01-01');

        $builder->setLocale('en_US');
        self::assertSame('January', $builder->format($dateTime));

        $builder->setLocale('fr_FR');
        self::assertSame('janvier', $builder->format($dateTime));
    }
}
