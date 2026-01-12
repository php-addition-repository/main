<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison;

use Par\Core\Comparison\ClosureComparator;
use Par\Core\Comparison\Order;
use Par\Core\Comparison\ThenComparator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ThenComparatorTest extends TestCase
{
    public function testItReturnsDecoratedWhenReversed(): void
    {
        $comparator = new ThenComparator(
            new ClosureComparator(static fn(array $left, array $right) => $left[0] <=> $right[0]),
            new ClosureComparator(static fn(array $left, array $right) => $left[1] <=> $right[1]),
        );

        self::assertEquals(Order::Equal, $comparator->compare([1, 2], [1, 2]));
        self::assertEquals(Order::Lesser, $comparator->compare([1, 2], [1, 1]));
        self::assertEquals(Order::Greater, $comparator->compare([2, 1], [2, 2]));
    }
}
