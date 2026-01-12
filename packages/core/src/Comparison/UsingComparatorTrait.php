<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Closure;

/**
 * @template TValue
 *
 * @mixin Comparator<TValue>
 */
trait UsingComparatorTrait
{
    public function using(Closure $extractor): Comparator
    {
        return new ExtractorComparator($extractor, $this);
    }
}
