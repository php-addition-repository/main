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
final class ClosureComparatorTest extends TestCase
{
    /**
     * @return iterable<array{0:mixed,1:mixed,Order}>
     */
    public static function comparableValuesProvider(): iterable
    {
        yield 'equal' => [1, 1, Order::Equal];
        yield 'greater' => ['a10', 'a1', Order::Greater];
        yield 'lesser' => [0.1, 2, Order::Lesser];
    }

    #[DataProvider('comparableValuesProvider')]
    public function testItCanCompareValues(mixed $a, mixed $b, Order $expected): void
    {
        $comparator = Comparators::with(static fn(mixed $a, mixed $b): int => $a <=> $b);

        self::assertEquals($expected, $comparator->compare($a, $b));
    }

    public function testItReturnsProvidedComparatorIfItsAlreadyComparator(): void
    {
        $comparatorMock = self::createStub(Comparator::class);

        self::assertSame($comparatorMock, Comparators::with($comparatorMock));
    }
}
