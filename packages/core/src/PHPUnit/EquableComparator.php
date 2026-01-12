<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit;

use Par\Core\Equable;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;

use function assert;

final class EquableComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof Equable && ($actual instanceof Equable || null === $actual);
    }

    public function assertEquals(
        mixed $expected,
        mixed $actual,
        float $delta = 0.0,
        bool $canonicalize = false,
        bool $ignoreCase = false,
    ): void {
        assert($expected instanceof Equable);
        assert(null === $actual || $actual instanceof Equable);
        if (!$expected->equals($actual)) {
            $exporter = new Exporter();

            throw new ComparisonFailure(
                $expected,
                $actual,
                $exporter->export($expected),
                $exporter->export($actual),
                'Failed asserting that two objects are equal.',
            );
        }
    }
}
