<?php

declare(strict_types=1);

namespace Par\Core\Tests\PHPUnit;

use Par\Core\Equable;
use Par\Core\PHPUnit\EquableComparator;
use Par\Core\Tests\EquableObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @internal
 */
#[CoversClass(EquableComparator::class)]
final class EquableComparatorTest extends TestCase
{
    #[Test]
    public function itAcceptsWhenExpectedIsEquable(): void
    {
        $comparator = new EquableComparator();

        self::assertTrue(
            $comparator->accepts(new EquableObject('foo'), new EquableObject('bar')),
            'Accepts when both are Equable',
        );
        self::assertTrue(
            $comparator->accepts(new EquableObject('foo'), null),
            'Accepts when expected is Equable and actual is NULL',
        );
        self::assertFalse(
            $comparator->accepts(null, new EquableObject('foo')),
            'Does not accept when expected is not Equable',
        );
    }

    #[Test]
    public function itCanAssertEquals(): void
    {
        $comparator = new EquableComparator();

        $objectEqualityMock = self::createStub(Equable::class);
        $objectEqualityMock->method('equals')->willReturn(true, false);

        $comparator->assertEquals($objectEqualityMock, $objectEqualityMock);

        $this->expectException(ComparisonFailure::class);
        $comparator->assertEquals($objectEqualityMock, null);
    }
}
