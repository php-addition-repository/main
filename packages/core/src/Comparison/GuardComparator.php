<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Closure;
use Par\Core\Comparison\Exception\IncomparableException;
use Stringable;

/**
 * This comparator makes sure that both values pass provided predicate.
 *
 * It will throw a `Par\Core\Comparison\Exception\IncomparableException` when the predicate returns` false` for either value in the comparison.
 *
 * @template TValue
 *
 * @implements Comparator<TValue>
 */
final readonly class GuardComparator implements Comparator
{
    /**
     * @use InvokableComparatorTrait<TValue>
     */
    use InvokableComparatorTrait;

    /**
     * @use ReversibleComparatorTrait<TValue>
     */
    use ReversibleComparatorTrait;

    /**
     * @use ThenableComparatorTrait<TValue>
     */
    use ThenableComparatorTrait;

    /**
     * @use UsingComparatorTrait<TValue>
     */
    use UsingComparatorTrait;

    /**
     * @param Comparator<TValue> $guardedComparator The comparator that is guarded
     * @param Closure(mixed): bool $predicate The predicate to use to verify the values being compared
     * @param string $additionalInfo optional additional info to add to the thrown exception message
     */
    public function __construct(
        private Comparator $guardedComparator,
        private Closure $predicate,
        private string $additionalInfo = '',
    ) {}

    /**
     * @phpstan-assert string|Stringable $v1
     * @phpstan-assert string|Stringable $v2
     */
    public function compare(mixed $v1, mixed $v2): Order
    {
        $test = $this->predicate;
        if (!$test($v1) || !$test($v2)) {
            throw IncomparableException::fromValues($v1, $v2, $this->additionalInfo);
        }

        return $this->guardedComparator->compare($v1, $v2);
    }
}
