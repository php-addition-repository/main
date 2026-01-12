<?php

declare(strict_types=1);

namespace Par\Core\Tests\Comparison;

use Par\Core\Comparison\ClosureComparator;
use Par\Core\Comparison\ExtractorComparator;
use Par\Core\Comparison\Order;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ExtractorComparatorTest extends TestCase
{
    public function testItPassesExtractedValueToDecoratedComparator(): void
    {
        $decorated = new ClosureComparator(static fn(int $a, int $b): int => $a <=> $b);
        $comparator = new ExtractorComparator(static fn(string $value): int => (int) $value, $decorated);

        self::assertEquals(Order::Equal, $comparator->compare('1', '1'));
        self::assertEquals(Order::Greater, $comparator->compare('2', '1'));
        self::assertEquals(Order::Lesser, $comparator->compare('1', '2'));
    }
}
