<?php

declare(strict_types=1);

namespace Par\Core;

use DateTimeInterface;

/**
 * Class containing utility methods for determining equality of values.
 *
 * This class provides methods to compare values, including support for custom equality
 * defined by the `Equable` interface and precise comparison of date/time objects. It also
 * provides helper methods for checking equality with collections of values.
 */
final readonly class Values
{
    private const string DATE_TIME_COMPARE_FORMAT = 'Y-m-d H:i:s.u e';

    /**
     * Determines if two values should be considered equal.
     *
     * - If both values implement `\Par\Core\Equable` then `$value->equals($otherValue)` is used.
     * - When both values are instances of `\DateTime` or `\DateTimeImmutable` then the moment in time they represent is compared including timezone and millisecond precision.
     * - Otherwise a strict comparison (`$value === $otherValue`) is used.
     *
     * Usage:
     * ```php
     * if (Values::equals($a, $b)) {
     *     // $a and $b are equal
     * }
     * ```
     *
     * @template TValue
     *
     * @param mixed  $value      The value to test
     * @param TValue $otherValue The other value with which to compare
     *
     * @return bool `true` if both values should be considered equal
     */
    public static function equals(mixed $value, mixed $otherValue): bool
    {
        if ($value === $otherValue) {
            return true;
        }

        if ($value instanceof Equable) {
            return $otherValue instanceof Equable && $value->equals($otherValue);
        }

        if ($value instanceof DateTimeInterface && $otherValue instanceof DateTimeInterface) {
            $format = self::DATE_TIME_COMPARE_FORMAT;

            return $value::class === $otherValue::class && $value->format($format) === $otherValue->format($format);
        }

        return false;
    }

    /**
     * Determines if a value should be considered equal to __any__ of the items in the list of other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsAnyIn($a, [$b, $c])) {
     *     // When equal to $b OR $c
     * }
     * ```
     *
     * @see Values::equals
     *
     * @template TValue
     *
     * @param mixed            $value       The value to test
     * @param iterable<TValue> $otherValues The list of other values with which to compare
     *
     * @return bool `true` if value should be considered equal to any of the items in the list of other values
     */
    public static function equalsAnyIn(mixed $value, iterable $otherValues): bool
    {
        return self::containsValue($value, $otherValues);
    }

    /**
     * Determines if a value should be considered equal to __any__ of the other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsAnyOf($a, $b, $c)) {
     *     // When equal to $b OR $c
     * }
     * ```
     *
     * @see Values::equals
     *
     * @template TValue
     *
     * @param mixed  $value          The value to test
     * @param TValue ...$otherValues The other values with which to compare
     *
     * @return bool `true` if value should be considered equal to any of the other values
     */
    public static function equalsAnyOf(mixed $value, mixed ...$otherValues): bool
    {
        return self::equalsAnyIn($value, $otherValues);
    }

    /**
     * Determines if a value should be considered equal to __none__ of the items in the list of other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsNoneIn($a, [$b, $c])) {
     *     // When not equal to $b AND $c
     * }
     * ```
     *
     * @see Values::equals
     *
     * @template TValue
     *
     * @param mixed            $value       The value to test
     * @param iterable<TValue> $otherValues The list of other values with which to compare
     *
     * @return bool `true` if value should be considered equal to __none__ of the items in the list of other values
     */
    public static function equalsNoneIn(mixed $value, iterable $otherValues): bool
    {
        return self::containsValue($value, $otherValues, false);
    }

    /**
     * Determines if a value should be considered equal to __none__ of the other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsNoneOf($a, $b, $c)) {
     *     // When not equal to $b AND $c
     * }
     * ```
     *
     * @see Values::equals
     *
     * @template TValue
     *
     * @param mixed  $value          The value to test
     * @param TValue ...$otherValues The other values with which to compare
     *
     * @return bool `true` if value should be considered equal to __none__ of the other values
     */
    public static function equalsNoneOf(mixed $value, mixed ...$otherValues): bool
    {
        return self::equalsNoneIn($value, $otherValues);
    }

    /**
     * @template TValue
     *
     * @param iterable<TValue> $otherValues
     */
    private static function containsValue(mixed $value, iterable $otherValues, bool $onMatch = true): bool
    {
        foreach ($otherValues as $otherValue) {
            if (self::equals($value, $otherValue)) {
                return $onMatch;
            }
        }

        return !$onMatch;
    }
}
