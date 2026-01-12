<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Par\Core\Comparison\Exception\IncomparableException;

/**
 * This interface imposes a total ordering on the objects of each class that implements it.
 *
 * @template T of Comparable
 */
interface Comparable
{
    /**
     * Compares this object with the other value for order.
     *
     * ```php
     * usort($list, static fn(ComparableType $a, ComparableType $b): int => $a->compareTo($b)->value);
     * ```
     *
     * __NOTE:__ It is strongly recommended, but not required, that `($a->compare($b) === Order::equals) ===
     * $a->equals($b)`. Generally speaking, any class that implements the `Par\Core\Comparison\Comparable` interface
     * and violates this condition should clearly state this fact.
     *
     * @param Comparable<T> $other The other value to compare with
     *
     * @return Order The order of other in comparison to this
     *
     * @throws IncomparableException if other value cannot be compared to this
     *
     * @phpstan-assert T $other
     */
    public function compare(self $other): Order;
}
