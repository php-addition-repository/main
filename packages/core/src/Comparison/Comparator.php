<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Closure;
use Par\Core\Comparison\Exception\IncomparableException;

/**
 * An object able to compare two values and determine their order in a collection.
 *
 * @template TValue The type of values that can be compared using this comparator
 */
interface Comparator
{
    /**
     * Alias to allow passing a comparator to the PHP native methods that support a callable to execute sorting.
     *
     * Same as:
     * ```
     * usort($list, static fn($v1, $v2): int => $comparator->compare($v1, $v2)->value)
     * ```
     *
     * @param TValue $v1 The first value to be compared
     * @param TValue $v2 The second value to be compared
     *
     * @throws IncomparableException if arguments are not comparable
     */
    public function __invoke(mixed $v1, mixed $v2): int;

    /**
     * Compares its two arguments for order.
     *
     * @param TValue $v1 The first value to be compared
     * @param TValue $v2 The second value to be compared
     *
     * @return Order The order of the second value in comparison to the first
     *
     * @throws IncomparableException if arguments are not comparable
     */
    public function compare(mixed $v1, mixed $v2): Order;

    /**
     * Returns a comparator that imposes the reverse ordering of this comparator.
     *
     * @return Comparator<TValue>
     */
    public function reversed(): self;

    /**
     * Returns a lexicographic-order comparator with another comparator.
     *
     * @param Comparator<TValue> $nextComparator
     *
     * @return Comparator<TValue>
     */
    public function then(self $nextComparator): self;

    /**
     * Returns a comparator that uses the extractor to determine the values to compare.
     *
     * @param Closure(TValue): mixed $extractor The extractor to use to determine the values to compare
     *
     * @return Comparator<TValue>
     */
    public function using(Closure $extractor): self;
}
