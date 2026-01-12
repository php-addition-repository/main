<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison\Comparators;

use Par\Core\Comparison\Comparator;
use Par\Core\Comparison\Comparators;
use Par\Core\Comparison\Order;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class UtilsTest extends TestCase
{
    public static function provideForComesAfter(): iterable
    {
        yield 'after' => [true, 2, 1];
        yield 'not-after' => [false, 1, 2];
    }

    public static function provideForComesAfterOrEquals(): iterable
    {
        yield from self::provideForComesAfter();

        yield 'equals' => [true, 3, 3];
        yield 'not-equals' => [false, 3, 4];
    }

    public static function provideForComesBefore(): iterable
    {
        yield 'before' => [true, 5, 6];
        yield 'not-before' => [false, 6, 5];
    }

    public static function provideForComesBeforeOrEquals(): iterable
    {
        yield from self::provideForComesBefore();

        yield 'equals' => [true, 3, 3];
        yield 'not-equals' => [false, 4, 3];
    }

    #[DataProvider('provideForComesAfter')]
    public function testItCanDetermineIfValueComesAfterAnotherValue(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        self::assertEquals($expected, Comparators::comesAfter($value, $otherValue));
    }

    #[DataProvider('provideForComesAfter')]
    public function testItCanDetermineIfValueComesAfterAnotherValueUsingCustomComparator(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        $comparatorMock = $this->createMock(Comparator::class);
        $comparatorMock->expects(self::once())
                       ->method('compare')
                       ->with($value, $otherValue)
                       ->willReturn($expected ? Order::Greater : Order::Lesser);

        self::assertEquals($expected, Comparators::comesAfter($value, $otherValue, $comparatorMock));
    }

    #[DataProvider('provideForComesAfterOrEquals')]
    public function testItCanDetermineIfValueComesAfterOrEqualsAnotherValue(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        self::assertEquals($expected, Comparators::comesAfterOrEquals($value, $otherValue));
    }

    #[DataProvider('provideForComesAfterOrEquals')]
    public function testItCanDetermineIfValueComesAfterOrEqualsAnotherValueUsingCustomComparator(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        $comparatorMock = $this->createMock(Comparator::class);
        $comparatorMock->expects(self::once())
                       ->method('compare')
                       ->with($value, $otherValue)
                       ->willReturn($expected ? Order::Equal : Order::Lesser);

        self::assertEquals($expected, Comparators::comesAfterOrEquals($value, $otherValue, $comparatorMock));
    }

    #[DataProvider('provideForComesBefore')]
    public function testItCanDetermineIfValueComesBeforeAnotherValue(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        self::assertEquals($expected, Comparators::comesBefore($value, $otherValue));
    }

    #[DataProvider('provideForComesBefore')]
    public function testItCanDetermineIfValueComesBeforeAnotherValueUsingCustomComparator(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        $comparatorMock = $this->createMock(Comparator::class);
        $comparatorMock->expects(self::once())
                       ->method('compare')
                       ->with($value, $otherValue)
                       ->willReturn($expected ? Order::Lesser : Order::Greater);

        self::assertEquals($expected, Comparators::comesBefore($value, $otherValue, $comparatorMock));
    }

    #[DataProvider('provideForComesBeforeOrEquals')]
    public function testItCanDetermineIfValueComesBeforeOrEqualsAnotherValue(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        self::assertEquals($expected, Comparators::comesBeforeOrEquals($value, $otherValue));
    }

    #[DataProvider('provideForComesBeforeOrEquals')]
    public function testItCanDetermineIfValueComesBeforeOrEqualsAnotherValueUsingCustomComparator(
        bool $expected,
        mixed $value,
        mixed $otherValue,
    ): void {
        $comparatorMock = $this->createMock(Comparator::class);
        $comparatorMock->expects(self::once())
                       ->method('compare')
                       ->with($value, $otherValue)
                       ->willReturn($expected ? Order::Equal : Order::Greater);

        self::assertEquals($expected, Comparators::comesBeforeOrEquals($value, $otherValue, $comparatorMock));
    }
}
