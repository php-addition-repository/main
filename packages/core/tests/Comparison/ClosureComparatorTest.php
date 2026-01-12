<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison;

use Par\Core\Comparison\ClosureComparator;
use Par\Core\Comparison\Order;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @internal
 */
final class ClosureComparatorTest extends TestCase
{
    public function testItCanBeUsedAsCallable(): void
    {
        $comparator = new ClosureComparator(static fn(int $a, int $b): Order => Order::from($a <=> $b));

        $reverseRange = range(10, 1);
        usort($reverseRange, $comparator);

        self::assertEquals(range(1, 10), $reverseRange);
    }

    public function testItCanCompareUsingCallableThatReturnsInt(): void
    {
        $comparator = new ClosureComparator(static fn(int $a, int $b): int => $a <=> $b);

        self::assertEquals(Order::Equal, $comparator->compare(1, 1));
        self::assertEquals(Order::Greater, $comparator->compare(2, 1));
        self::assertEquals(Order::Lesser, $comparator->compare(1, 2));
    }

    public function testItCanCompareUsingCallableThatReturnsOrder(): void
    {
        $comparator = new ClosureComparator(static fn(int $a, int $b): Order => Order::from($a <=> $b));

        self::assertEquals(Order::Equal, $comparator->compare(1, 1));
        self::assertEquals(Order::Greater, $comparator->compare(2, 1));
        self::assertEquals(Order::Lesser, $comparator->compare(1, 2));
    }

    public function testItThrowsTypeErrorWhenComparatorDoesNotReturnExpectedType(): void
    {
        /** @phpstan-ignore argument.type */
        $comparator = new ClosureComparator(static fn(int $a, int $b): bool => false);

        self::expectException(TypeError::class);
        $comparator->compare(1, 2);
    }
}
