<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison\Comparators;

use Par\Core\Comparison\Comparators;
use Par\Core\Comparison\Order;
use Par\Core\Tests\Fixtures\ComparableScalarObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ValuesComparatorTest extends TestCase
{
    public static function comparableValuesProvider(): iterable
    {
        yield 'Comparable{1} <=> Comparable{1} = Equal' => [
            new ComparableScalarObject(1),
            new ComparableScalarObject(1),
            Order::Equal,
        ];
        yield '1 <=> 1 = Equal' => [1, 1, Order::Equal];
        yield 'Comparable{2} <=> Comparable{1} = Greater' => [
            new ComparableScalarObject(2),
            new ComparableScalarObject(1),
            Order::Greater,
        ];
        yield '2 <=> 1 = Greater' => [2, 1, Order::Greater];
        yield 'Comparable{1} <=> Comparable{2} = Lesser' => [
            new ComparableScalarObject(1),
            new ComparableScalarObject(2),
            Order::Lesser,
        ];
        yield '1 <=> 2 = Lesser' => [1, 2, Order::Lesser];
    }

    #[Test]
    #[DataProvider('comparableValuesProvider')]
    public function itCanCompareValues(mixed $a, mixed $b, Order $expected): void
    {
        $comparator = Comparators::values();

        self::assertEquals($expected, $comparator->compare($a, $b));
    }
}
