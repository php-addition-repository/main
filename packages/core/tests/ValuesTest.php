<?php

declare(strict_types=1);

namespace Par\Core\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Par\Core\Values;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
#[CoversClass(Values::class)]
final class ValuesTest extends TestCase
{
    public function testEqualsAnyIn(): void
    {
        self::assertTrue(Values::equalsAnyIn(1, [1, 2, 3]));
        self::assertTrue(Values::equalsAnyIn(1, [3, 2, 1]));
        self::assertFalse(Values::equalsAnyIn(1, [2, 3, 4]));
        self::assertFalse(Values::equalsAnyIn(1, []));

        $obj1 = new EquableObject('foo');
        $obj2 = new EquableObject('bar');
        $obj3 = new EquableObject('baz');
        self::assertTrue(Values::equalsAnyIn($obj1, [$obj2, new EquableObject('foo')]));
        self::assertFalse(Values::equalsAnyIn($obj1, [$obj2, $obj3]));
    }

    public function testEqualsAnyOf(): void
    {
        self::assertTrue(Values::equalsAnyOf(1, 1, 2, 3));
        self::assertTrue(Values::equalsAnyOf(1, 3, 2, 1));
        self::assertFalse(Values::equalsAnyOf(1, 2, 3, 4));
        self::assertFalse(Values::equalsAnyOf(1));

        $obj1 = new EquableObject('foo');
        $obj2 = new EquableObject('bar');
        $obj3 = new EquableObject('baz');
        self::assertTrue(Values::equalsAnyOf($obj1, $obj2, new EquableObject('foo')));
        self::assertFalse(Values::equalsAnyOf($obj1, $obj2, $obj3));
    }

    public function testEqualsNoneIn(): void
    {
        self::assertTrue(Values::equalsNoneIn(1, [2, 3, 4]));
        self::assertTrue(Values::equalsNoneIn(1, []));
        self::assertFalse(Values::equalsNoneIn(1, [1, 2, 3]));
        self::assertFalse(Values::equalsNoneIn(1, [3, 2, 1]));

        $obj1 = new EquableObject('foo');
        $obj2 = new EquableObject('bar');
        $obj3 = new EquableObject('baz');
        self::assertFalse(Values::equalsNoneIn($obj1, [$obj2, new EquableObject('foo')]));
        self::assertTrue(Values::equalsNoneIn($obj1, [$obj2, $obj3]));
    }

    public function testEqualsNoneOf(): void
    {
        self::assertTrue(Values::equalsNoneOf(1, 2, 3, 4));
        self::assertTrue(Values::equalsNoneOf(1));
        self::assertFalse(Values::equalsNoneOf(1, 1, 2, 3));
        self::assertFalse(Values::equalsNoneOf(1, 3, 2, 1));

        $obj1 = new EquableObject('foo');
        $obj2 = new EquableObject('bar');
        $obj3 = new EquableObject('baz');
        self::assertFalse(Values::equalsNoneOf($obj1, $obj2, new EquableObject('foo')));
        self::assertTrue(Values::equalsNoneOf($obj1, $obj2, $obj3));
    }

    public function testEqualsWithDateTime(): void
    {
        $dt1 = new DateTime('2024-01-01 10:00:00.123456', new DateTimeZone('UTC'));
        $dt2 = new DateTime('2024-01-01 10:00:00.123456', new DateTimeZone('UTC'));
        $dt3 = new DateTime('2024-01-01 10:00:00.123456', new DateTimeZone('Europe/Berlin'));
        $dt4 = new DateTime('2024-01-01 10:00:00.654321', new DateTimeZone('UTC'));

        self::assertTrue(Values::equals($dt1, $dt2), 'Same value, timezone and milliseconds should be equal');
        self::assertFalse(Values::equals($dt1, $dt3), 'Different timezone should not be equal');
        self::assertFalse(Values::equals($dt1, $dt4), 'Different milliseconds should not be equal');
    }

    public function testEqualsWithDateTimeImmutable(): void
    {
        $dt1 = new DateTimeImmutable('2024-01-01 10:00:00.123456', new DateTimeZone('UTC'));
        $dt2 = new DateTimeImmutable('2024-01-01 10:00:00.123456', new DateTimeZone('UTC'));
        $dt3 = new DateTimeImmutable('2024-01-01 10:00:00.123456', new DateTimeZone('Europe/Berlin'));
        $dt4 = new DateTimeImmutable('2024-01-01 10:00:00.654321', new DateTimeZone('UTC'));

        self::assertTrue(Values::equals($dt1, $dt2), 'Same value, timezone and milliseconds should be equal');
        self::assertFalse(Values::equals($dt1, $dt3), 'Different timezone should not be equal');
        self::assertFalse(Values::equals($dt1, $dt4), 'Different milliseconds should not be equal');
    }

    public function testEqualsWithDifferentTypes(): void
    {
        self::assertFalse(Values::equals(1, 1.0));
        self::assertFalse(Values::equals('1', 1));
    }

    public function testEqualsWithDifferentObjectInstances(): void
    {
        self::assertFalse(Values::equals(new stdClass(), new stdClass()));
    }

    public function testEqualsWithMixedDateTimeTypes(): void
    {
        $dt1 = new DateTimeImmutable('2024-01-01 10:00:00.123456', new DateTimeZone('UTC'));
        $dt2 = new DateTime('2024-01-01 10:00:00.123456', new DateTimeZone('UTC'));

        self::assertFalse(Values::equals($dt1, $dt2), 'DateTimeImmutable and DateTime should not be equal');
        self::assertFalse(Values::equals($dt2, $dt1), 'DateTime and DateTimeImmutable should not be equal');
    }

    public function testEqualsWithEquable(): void
    {
        $obj1 = new EquableObject('foo');
        $obj2 = new EquableObject('foo');
        $obj3 = new EquableObject('bar');

        self::assertTrue(Values::equals($obj1, $obj1), 'Same instance should be equal');
        self::assertTrue(Values::equals($obj1, $obj2), 'Different instances with same value should be equal');
        self::assertFalse(
            Values::equals($obj1, $obj3),
            'Different instances with different values should not be equal',
        );
    }

    public function testEqualsWithMixedEquableAndNonEquable(): void
    {
        $equable = new EquableObject('foo');
        $nonEquable = 'foo';

        self::assertFalse(
            Values::equals($equable, $nonEquable),
            'Equable vs non-equable should not be equal (even if values match internal)',
        );
        self::assertFalse(Values::equals($nonEquable, $equable), 'Non-equable vs equable should not be equal');
    }

    public function testEqualsWithNulls(): void
    {
        self::assertTrue(Values::equals(null, null));
        self::assertFalse(Values::equals(null, 'null'));
        self::assertFalse(Values::equals('null', null));
    }

    public function testEqualsWithScalars(): void
    {
        self::assertTrue(Values::equals(1, 1));
        self::assertTrue(Values::equals('foo', 'foo'));
        self::assertTrue(Values::equals(true, true));
        self::assertFalse(Values::equals(1, 2));
        self::assertFalse(Values::equals('foo', 'bar'));
        self::assertFalse(Values::equals(true, false));
        self::assertFalse(Values::equals(1, '1'));
    }
}
