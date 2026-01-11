<?php

declare(strict_types=1);

namespace Par\Core\Tests;

use Par\Core\Equable;

/**
 * @internal
 */
final readonly class EquableObject implements Equable
{
    public function __construct(private string|int|float $value)
    {
    }

    public function equals(?Equable $other): bool
    {
        return $other instanceof EquableObject && $other->value === $this->value;
    }
}
