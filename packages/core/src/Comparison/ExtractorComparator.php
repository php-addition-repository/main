<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Closure;

/**
 * This comparator will use the extractor to get the value that should be used to compare provided values.
 *
 * @template TValue
 *
 * @implements Comparator<TValue>
 */
final readonly class ExtractorComparator implements Comparator
{
    /**
     * @use ReversibleComparatorTrait<TValue>
     */
    use ReversibleComparatorTrait;

    /**
     * @use ThenableComparatorTrait<TValue>
     */
    use ThenableComparatorTrait;

    /**
     * @use InvokableComparatorTrait<TValue>
     */
    use InvokableComparatorTrait;

    /**
     * @use UsingComparatorTrait<TValue>
     */
    use UsingComparatorTrait;

    /**
     * @template UValue
     *
     * @param Closure(TValue):UValue $extractor
     * @param Comparator<UValue> $comparator
     */
    public function __construct(private Closure $extractor, private Comparator $comparator) {}

    public function compare(mixed $v1, mixed $v2): Order
    {
        $extractor = $this->extractor;

        return $this->comparator->compare($extractor($v1), $extractor($v2));
    }
}
