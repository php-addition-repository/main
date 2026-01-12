<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

/**
 * @template T
 *
 * @mixin Comparator<T>
 */
trait InvokableComparatorTrait
{
    public function __invoke(mixed $v1, mixed $v2): int
    {
        return $this->compare($v1, $v2)->value;
    }
}
