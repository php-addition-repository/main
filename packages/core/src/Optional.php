<?php

declare(strict_types=1);

namespace Par\Core;

use loophp\iterators\IterableIteratorAggregate;
use Par\Core\Exception\NoSuchElementException;
use Throwable;
use function is_callable;

/**
 * A container object which may or may not contain a value.
 *
 * If a value is present, `Par\Core\Optional::isPresent()` returns `true`. If no value is present, the object is considered **empty** and
 * `Par\Core\Optional::isPresent()` returns `false`.
 *
 * __NOTE:__ `Par\Core\Optional` is primarily intended for use as a method return type where there is a clear need to represent
 * "no result" and where using `null` is likely to cause errors. A variable whose type is `Par\Core\Optional` should never itself
 * be `null`; it should always point to an `Par\Core\Optional` instance.
 *
 * @template-covariant TValue
 */
final readonly class Optional implements Equable
{
    /**
     * Returns an empty `Par\Core\Optional` instance.
     *
     * @return Optional<null>
     */
    public static function empty(): self
    {
        return new self();
    }

    /**
     * Returns an `Par\Core\Optional` describing the given value.
     *
     * @template T
     *
     * @param T $value The value to describe, which can be `null`
     *
     * @return Optional<T>
     */
    public static function fromAny(mixed $value): self
    {
        return new self(true, $value);
    }

    /**
     * Returns an `Par\Core\Optional` instance using the current element value of an iterable, if present, otherwise returns an
     * empty `Par\Core\Optional`.
     *
     * @template T
     *
     * @param iterable<T> $iterable The iterable to use the current value from
     *
     * @return Optional<T>|Optional<null>
     */
    public static function fromCurrent(iterable $iterable): self
    {
        $iterator = (new IterableIteratorAggregate($iterable))->getIterator();
        if ($iterator->valid()) {
            return new self(true, $iterator->current());
        }

        return self::empty();
    }

    /**
     * Returns an `Par\Core\Optional` describing the given value, if non-null, otherwise returns an empty `Par\Core\Optional`.
     *
     * @template T
     *
     * @param T $value The value to describe, which can be `null`
     *
     * @return Optional<T>|Optional<null>
     */
    public static function fromNullable(mixed $value): self
    {
        if (null !== $value) {
            return new self(true, $value);
        }

        return self::empty();
    }

    /**
     * @param TValue $value
     */
    private function __construct(private bool $hasValue = false, private mixed $value = null) {}

    public function equals(?Equable $other): bool
    {
        if ($other instanceof self) {
            return $this->hasValue === $other->hasValue && Values::equals($this->value, $other->value);
        }

        return false;
    }

    /**
     * If a value is present, and the value matches the given predicate, returns an `Par\Core\Optional` describing the value,
     * otherwise returns an empty `Par\Core\Optional`.
     *
     * @param callable(TValue): bool $predicate the predicate to apply to a value, if present
     *
     * @return Optional<TValue>|Optional<null>
     */
    public function filter(callable $predicate): self
    {
        if ($this->isPresent() && $predicate($this->value)) {
            return $this;
        }

        return self::empty();
    }

    /**
     * Retrieve the value of the optional.
     *
     * @return TValue
     *
     * @throws NoSuchElementException if no value is present
     */
    public function get(): mixed
    {
        return $this->orElseThrow();
    }

    /**
     * If a value is present, performs the given action with the value, otherwise does nothing.
     *
     * @param callable(TValue): void $action The action to execute if a value is present
     */
    public function ifPresent(callable $action): void
    {
        $this->ifPresentOrElse(
            $action,
            static function (): void {},
        );
    }

    /**
     * If a value is present, performs the given action with the value, otherwise performs the given empty-based action.
     *
     * @param callable(TValue): void $action      The action to execute when a value is present
     * @param callable(): void       $emptyAction The action to execute when the optional is empty
     */
    public function ifPresentOrElse(callable $action, callable $emptyAction): void
    {
        if ($this->isPresent()) {
            $action($this->value);
        } else {
            $emptyAction();
        }
    }

    /**
     * If a value is not present, returns true, otherwise false.
     */
    public function isEmpty(): bool
    {
        return !$this->isPresent();
    }

    /**
     * If a value is present, returns true, otherwise false.
     */
    public function isPresent(): bool
    {
        return $this->hasValue;
    }

    /**
     * If a value is present, returns an `Par\Core\Optional` describing the result of applying
     * the given mapping function to the value, otherwise returns an empty `Par\Core\Optional`.
     *
     * @template UValue
     *
     * @param callable(mixed): UValue $mapper The mapping function to apply to a value, if present
     *
     * @return Optional<UValue>|Optional<TValue>
     */
    public function map(callable $mapper): self
    {
        if ($this->isPresent()) {
            return self::fromAny($mapper($this->value));
        }

        return $this;
    }

    /**
     * If a value is present, returns the value, otherwise returns other.
     *
     * @template UValue
     *
     * @param UValue $other the value to be returned, if no value is present
     *
     * @return TValue|UValue
     */
    public function orElse(mixed $other): mixed
    {
        if ($this->isPresent()) {
            return $this->value;
        }

        return $other;
    }

    /**
     * If a value is present, returns the value, otherwise returns the result produced by the supplying function.
     *
     * @template UValue
     *
     * @param callable():UValue $supplier the supplying function that produces a value to be returned
     *
     * @return TValue|UValue
     */
    public function orElseGet(callable $supplier): mixed
    {
        if ($this->isPresent()) {
            return $this->value;
        }

        return $supplier();
    }

    /**
     * If a value is present, returns the value, otherwise throws an exception produced by the exception supplying
     * function.
     *
     * If no supplying function is provided a default supplier will be used which returns a `Par\Core\Exception\NoSuchElementException`.
     *
     * @param callable():Throwable|null $exceptionSupplier the supplying function that produces an exception to be thrown
     *
     * @return TValue
     *
     * @throws NoSuchElementException|Throwable if no value is present
     */
    public function orElseThrow(?callable $exceptionSupplier = null): mixed
    {
        if ($this->isPresent()) {
            return $this->value;
        }

        throw $this->exceptionSupplier($exceptionSupplier);
    }

    /**
     * @param callable():Throwable|null $supplier
     *
     * @return ($supplier is callable ? Throwable : NoSuchElementException)
     */
    private function exceptionSupplier(?callable $supplier = null): NoSuchElementException|Throwable
    {
        if (is_callable($supplier)) {
            return $supplier();
        }

        return new NoSuchElementException();
    }
}
