<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison;

use Par\Core\Comparison\ClosureComparator;
use Par\Core\Comparison\Order;
use Par\Core\Comparison\ReverseComparator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ReverseComparatorTest extends TestCase
{
    public function testItReturnsDecoratedWhenReversed(): void
    {
        $decorated = new ClosureComparator(static fn (int $a, int $b): int => $a <=> $b);
        $comparator = new ReverseComparator($decorated);

        self::assertSame($decorated, $comparator->reversed());
    }

    public function testItReversesOrderOfDecoratedComparator(): void
    {
        $comparator = new ReverseComparator(new ClosureComparator(static fn (int $a, int $b): int => $a <=> $b));

        self::assertEquals(Order::Equal, $comparator->compare(1, 1));
        self::assertEquals(Order::Lesser, $comparator->compare(2, 1));
        self::assertEquals(Order::Greater, $comparator->compare(1, 2));
    }
}
