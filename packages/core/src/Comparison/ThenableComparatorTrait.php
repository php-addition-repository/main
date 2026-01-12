<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

/**
 * @template TValue
 *
 * @mixin Comparator<TValue>
 */
trait ThenableComparatorTrait
{
    public function then(Comparator $nextComparator): Comparator
    {
        return new ThenComparator($this, $nextComparator);
    }
}
