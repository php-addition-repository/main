<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Throwable;
use function is_int;

/**
 * This enum represents the value of something when compared to another.
 *
 * @phpstan-type _OrderInt int<-1,1>
 */
enum Order: int
{
    case Lesser = -1;
    case Equal = 0;
    case Greater = 1;

    /**
     * @param callable(mixed):Throwable $throwableSupplier
     *
     * @phpstan-assert _OrderInt|Order $value
     */
    public static function castOrThrow(mixed $value, callable $throwableSupplier): Order
    {
        if (is_int($value)) {
            return Order::from($value);
        }

        if (!$value instanceof Order) {
            throw $throwableSupplier($value);
        }

        return $value;
    }

    /**
     * Returns the inverted value of current `Par\Core\Comparison\Order`.
     */
    public function invert(): self
    {
        return match ($this) {
            self::Equal => $this,
            self::Lesser => self::Greater,
            self::Greater => self::Lesser,
        };
    }
}
