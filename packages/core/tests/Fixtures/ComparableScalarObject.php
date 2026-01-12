<?php

declare(strict_types=1);

namespace Par\Core\Tests\Fixtures;

use Par\Core\Comparison\Comparable;
use Par\Core\Comparison\Exception\IncomparableException;
use Par\Core\Comparison\Order;

/**
 * @internal
 *
 * @implements Comparable<self>
 */
final readonly class ComparableScalarObject implements Comparable
{
    public function __construct(public int|string $value) {}

    public function compare(Comparable $other): Order
    {
        if ($other instanceof self && gettype($this->value) === gettype($other->value)) {
            return Order::from($this->value <=> $other->value);
        }

        throw IncomparableException::fromValues(
            $this,
            $other,
            sprintf('Both values must be an %s<%s>.', self::class, gettype($this->value)),
        );
    }
}
