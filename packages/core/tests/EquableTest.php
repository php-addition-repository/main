<?php

declare(strict_types=1);

namespace Par\Core\Tests;

use Par\Core\Equable;
use Par\Core\Tests\Fixtures\EquableObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class EquableTest extends TestCase
{
    #[Test]
    public function itReturnsFalseWhenComparingToDifferentEquableImplementation(): void
    {
        $equable1 = new EquableObject('foo');
        $equable2 = new class implements Equable {
            public function equals(?Equable $other): bool
            {
                return false;
            }
        };

        self::assertFalse($equable1->equals($equable2));
    }

    #[Test]
    public function itReturnsFalseWhenComparingToDifferentInstanceWithDifferentValue(): void
    {
        $equable1 = new EquableObject('foo');
        $equable2 = new EquableObject('bar');

        self::assertFalse($equable1->equals($equable2));
    }

    #[Test]
    public function itReturnsFalseWhenComparingToNull(): void
    {
        $equable = new EquableObject('foo');

        self::assertFalse($equable->equals(null));
    }

    #[Test]
    public function itReturnsTrueWhenComparingToDifferentInstanceWithSameValue(): void
    {
        $equable1 = new EquableObject('foo');
        $equable2 = new EquableObject('foo');

        self::assertTrue($equable1->equals($equable2));
    }

    #[Test]
    public function itReturnsTrueWhenComparingToSameInstance(): void
    {
        $equable = new EquableObject('foo');

        self::assertTrue($equable->equals($equable));
    }
}
