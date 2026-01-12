<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Closure;
use Stringable;

/**
 * Comparators can be used to determine the order of values in a collection.
 *
 * All the methods return a function that accepts 2 values and returns their `Order`.
 *
 * Usage example:
 * ```php
 * $stream = \Par\Core\Collection\Stream\MixedStream::fromIterable(array_shuffle(range(1, 10));
 * $stream->sorted(Comparators::integers()); // [1,2,3,4,5,6,7,8,9,10]
 * ```
 *
 * @phpstan-type StringType string|Stringable
 * @phpstan-type OrderType int<-1,1>|Order
 */
final readonly class Comparators
{
    /**
     * Returns `true` if value comes after otherValue.
     *
     * This is the same as:
     * ```
     * $comparator->compare($value, $otherValue) === Order::Greater;
     * ```
     *
     * @template T type of value to test
     *
     * @param T $value The value to test
     * @param T $otherValue The other value to test against
     * @param Comparator<T>|null $comparator Optional comparator to use, defaults to `Par\Core\Comparison\Comparators::values()`
     *
     * @return bool `true` if value comes after otherValue
     */
    public static function comesAfter(mixed $value, mixed $otherValue, ?Comparator $comparator = null): bool
    {
        $comparator ??= self::values();

        return Order::Greater === $comparator->compare($value, $otherValue);
    }

    /**
     * Returns `true` if value comes after or equals otherValue.
     *
     * This is the same as:
     * ```
     * $comparator->compare($value, $otherValue) !== Order::Lesser;
     * ```
     *
     * @template T type of value to test
     *
     * @param T $value the value to test
     * @param T $otherValue the other value to test against
     * @param Comparator<T>|null $comparator Optional comparator to use, defaults to `Par\Core\Comparison\Comparators::values()`
     *
     * @return bool `true` if value comes after or equals otherValue
     */
    public static function comesAfterOrEquals(mixed $value, mixed $otherValue, ?Comparator $comparator = null): bool
    {
        return !self::comesBefore($value, $otherValue, $comparator);
    }

    /**
     * Returns `true` if value comes before otherValue.
     *
     * This is the same as:
     * ```
     * $comparator->compare($value, $otherValue) === Order::Lesser;
     * ```
     *
     * @template T type of value to test
     *
     * @param T $value the value to test
     * @param T $otherValue the other value to test against
     * @param Comparator<T>|null $comparator Optional comparator to use, defaults to `Par\Core\Comparison\Comparators::values()`
     *
     * @return bool `true` if value comes before otherValue
     */
    public static function comesBefore(mixed $value, mixed $otherValue, ?Comparator $comparator = null): bool
    {
        $comparator ??= self::values();

        return Order::Lesser === $comparator->compare($value, $otherValue);
    }

    /**
     * Returns true if value comes before or equals otherValue.
     *
     * This is the same as:
     * ```
     * $comparator->compare($value, $otherValue) !== Order::Greater;
     * ```
     *
     * @template T type of value to test
     *
     * @param T $value The value to test
     * @param T $otherValue the other value to test against
     * @param Comparator<T>|null $comparator Optional comparator to use, defaults to `Par\Core\Comparison\Comparators::values()`
     *
     * @return bool `true` if value comes before or equals otherValue
     */
    public static function comesBeforeOrEquals(mixed $value, mixed $otherValue, ?Comparator $comparator = null): bool
    {
        return !self::comesAfter($value, $otherValue, $comparator);
    }

    /**
     * Returns a comparator to compare two `float` values.
     *
     * The comparator will automatically implement a guard to make sure both values are floats.
     *
     * @return Comparator<float>
     */
    public static function floats(): Comparator
    {
        return new GuardComparator(
            self::with(static fn(float $a, float $b): Order => Order::from($a <=> $b)),
            is_float(...),
            'Both values must be a float.',
        );
    }

    /**
     * Returns a comparator to compare two `integer` values.
     *
     * The comparator will automatically implement a guard to make sure both values are integers.
     *
     * @return Comparator<int>
     */
    public static function integers(): Comparator
    {
        return new GuardComparator(
            self::with(static fn(int $a, int $b): Order => Order::from($a <=> $b)),
            is_int(...),
            'Both values must be an integer.',
        );
    }

    /**
     * Returns a comparator to compare two `string` or `Stringable` values using a natural case insensitive order.
     *
     * The comparator will automatically implement a guard to make sure both values are strings or objects implementing
     * the `Stringable` interface.
     *
     * @return Comparator<StringType>
     */
    public static function naturalCaseSensitiveOrder(): Comparator
    {
        return self::createStringGuard(
            self::with(
                static fn(string|Stringable $value, string|Stringable $otherValue): Order => Order::from(
                    strnatcmp((string) $value, (string) $otherValue),
                ),
            ),
        );
    }

    /**
     * Returns a comparator to compare two `string` or `Stringable` values using a natural case sensitive order.
     *
     * The comparator will automatically implement a guard to make sure both values are strings or objects implementing
     * the `Stringable` interface.
     *
     * @return Comparator<StringType>
     */
    public static function naturalOrder(): Comparator
    {
        return self::createStringGuard(
            self::with(
                static fn(string|Stringable $value, string|Stringable $otherValue): Order => Order::from(
                    strnatcasecmp((string) $value, (string) $otherValue),
                ),
            ),
        );
    }

    /**
     * Returns a comparator to compare two `string` or `Stringable`  values.
     *
     * The comparator will automatically implement a guard to make sure both values are strings or objects implementing the `Stringable` interface.
     *
     * @return Comparator<StringType>
     */
    public static function strings(): Comparator
    {
        return self::createStringGuard(
            self::with(
                static fn(string|Stringable $a, string|Stringable $b): Order => Order::from(
                    (string) $a <=> (string) $b,
                ),
            ),
        );
    }

    /**
     * Returns a comparator that uses the objects comparator if possible, otherwise falls back on the native comparison `$a <=> $b`.
     *
     * @return Comparator<mixed>
     */
    public static function values(): Comparator
    {
        return self::with(
            static function (mixed $value, mixed $otherValue): Order {
                if ($value instanceof Comparable && $otherValue instanceof Comparable) {
                    return $value->compare($otherValue);
                }

                return Order::from($value <=> $otherValue);
            },
        );
    }

    /**
     * Returns a comparator that uses the comparator callback to determine value order.
     *
     * Internally this is the same as:
     * ```php
     * $comparator = new CallableComparator(
     *     $comparator
     * );
     * ```
     *
     * @template T
     *
     * @param (Closure(T, T): OrderType)|Comparator<T> $comparator The comparator callback
     *
     * @return Comparator<T>
     */
    public static function with(Closure|Comparator $comparator): Comparator
    {
        if ($comparator instanceof Comparator) {
            return $comparator;
        }

        return new ClosureComparator($comparator);
    }

    /**
     * @param Comparator<StringType> $comparator
     *
     * @return Comparator<StringType>
     */
    private static function createStringGuard(Comparator $comparator): Comparator
    {
        return new GuardComparator(
            $comparator,
            static fn(mixed $value): bool => is_string($value) || $value instanceof Stringable,
            'Both values must be a string or an object implementing the Stringable interface.',
        );
    }
}
