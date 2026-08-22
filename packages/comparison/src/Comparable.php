<?php

declare(strict_types=1);

namespace Par\Comparison;

use Par\Comparison\Exception\IncomparableException;

/**
 * @template-covariant T
 *
 * @api
 */
interface Comparable
{
    /**
     * Compares this object with another object of the same type.
     *
     * @param mixed $other the object to compare with
     *
     * @return Order the result of the comparison
     *
     * @throws IncomparableException when the objects are not comparable
     *
     * @phpstan-assert T $other
     */
    public function compare(mixed $other): Order;
}
