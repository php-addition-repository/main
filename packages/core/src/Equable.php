<?php

declare(strict_types=1);

namespace Par\Core;

/**
 * An object implementing this interface can determine if it equals any other value.
 *
 * Strict comparison (`$a === $b`) does not work on different instances of objects that represent the same value.
 * Regular comparison (`$a == $b`) is possible, but you need to remember to use it on object comparison, but not on
 * other value types which is confusing. By implementing this interface on the objects that require comparison you can
 * use `$a->equals($b)` and you have all the control.
 */
interface Equable
{
    /**
     * Determines if this object should be considered equal to other value.
     *
     * In most cases the method evaluates to true if the other value has the same type and internal value(s) with an
     * implementation like the following.
     * ```php
     * public function equals(?Equable $other): bool
     * {
     *    if ($other instanceof self) {
     *        return $other->value === $this->value;
     *    }
     *
     *    return false;
     * }
     * ```
     *
     * @template TValue of Equable
     * @param TValue|null $other The other value with which to compare
     *
     * @return bool `true` if this object should be considered equal to other value, `false` otherwise
     */
    public function equals(?Equable $other): bool;
}
