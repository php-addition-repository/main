<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

/**
 * @template T
 *
 * @mixin Comparator<T>
 */
trait ReversibleComparatorTrait
{
    public function reversed(): Comparator
    {
        return new ReverseComparator($this);
    }
}
