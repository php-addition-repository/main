<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

/**
 * @template TValue
 *
 * @implements Comparator<TValue>
 */
final readonly class ThenComparator implements Comparator
{
    /**
     * @use InvokableComparatorTrait<TValue>
     */
    use InvokableComparatorTrait;

    /**
     * @use ReversibleComparatorTrait<TValue>
     */
    use ReversibleComparatorTrait;

    /**
     * @use ThenableComparatorTrait<TValue>
     */
    use ThenableComparatorTrait;

    /**
     * @use UsingComparatorTrait<TValue>
     */
    use UsingComparatorTrait;

    /**
     * @param Comparator<TValue> $first
     * @param Comparator<TValue> $next
     */
    public function __construct(private Comparator $first, private Comparator $next) {}

    public function compare(mixed $v1, mixed $v2): Order
    {
        return Order::from($this->first->compare($v1, $v2)->value <=> $this->next->compare($v1, $v2)->value);
    }
}
