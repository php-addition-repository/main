<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison\Comparators;

use Par\Core\Comparison\Comparators;
use Par\Core\Comparison\Exception\IncomparableException;
use Par\Core\Comparison\Order;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FloatsComparatorTest extends TestCase
{
    public static function comparableValuesProvider(): iterable
    {
        yield 'equal' => [1, 1, Order::Equal];
        yield 'greater' => [2, 1, Order::Greater];
        yield 'lesser' => [2, 3, Order::Lesser];
    }

    public static function incompatibleValuesProvider(): iterable
    {
        yield 'strings' => ['foo', 'bar'];
        yield 'integers' => [1, 2];
        yield 'a-not-compatible' => ['foo', 1.0];
        yield 'b-not-compatible' => [1.0, 'foo'];
    }

    #[DataProvider('comparableValuesProvider')]
    public function testItCanCompareValues(float $a, float $b, Order $expected): void
    {
        $comparator = Comparators::floats();

        self::assertEquals($expected, $comparator->compare($a, $b));
    }

    #[DataProvider('incompatibleValuesProvider')]
    public function testItThrowsIncomparableExceptionForIncompatibleValues(mixed $a, mixed $b): void
    {
        $comparator = Comparators::floats();

        $this->expectException(IncomparableException::class);

        $comparator->compare($a, $b);
    }
}
