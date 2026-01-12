<?php

declare(strict_types=1);

namespace Par\Core\Tests;

use Exception;
use Par\Core\Exception\NoSuchElementException;
use Par\Core\Optional;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

/**
 * @internal
 */
final class OptionalTest extends TestCase
{
    /**
     * @return iterable<array{0:mixed}>
     */
    public static function allValuesProvider(): iterable
    {
        yield from self::nonNullableValuesProvider();
        yield [null];
    }

    /**
     * @return iterable<array{0:Optional<mixed>,1:mixed,2:bool}>
     */
    public static function equalsProvider(): iterable
    {
        yield 'both-empty' => [Optional::empty(), Optional::empty(), true];
        yield 'same-value' => [Optional::fromAny('foo'), Optional::fromAny('foo'), true];
        yield 'different-value' => [Optional::fromAny('foo'), Optional::fromAny('bar'), false];
        yield 'value-vs-empty' => [Optional::fromAny('foo'), Optional::empty(), false];
        yield 'value-vs-null' => [Optional::fromAny('foo'), null, false];
    }

    /**
     * @return iterable<array{0:mixed}>
     */
    public static function nonNullableValuesProvider(): iterable
    {
        yield ['foo'];
        yield [''];
        yield [true];
        yield [false];
        yield [0];
        yield [1];
        yield [new stdClass()];
        yield [[]];
    }

    #[Test]
    public function filterReturnsEmptyOptionalWhenPredicateNotMatches(): void
    {
        $optional = Optional::fromAny('bar');

        self::assertEquals(Optional::empty(), $optional->filter(static fn(string $value): bool => 'bar' !== $value));
    }

    #[Test]
    public function filterReturnsOptionalWhenPredicateMatches(): void
    {
        $optional = Optional::fromAny('foo');

        self::assertEquals($optional, $optional->filter(static fn(string $value): bool => 'foo' === $value));
    }

    #[Test]
    public function ifPresentDoesNotExecuteActionIfEmpty(): void
    {
        $optional = Optional::empty();

        $invocations = [];
        $optional->ifPresent(
            static function (?string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
        );

        self::assertEquals([], $invocations);
    }

    #[Test]
    public function ifPresentExecutesActionIfNotEmpty(): void
    {
        $optional = Optional::fromAny('foo');

        $invocations = [];
        $optional->ifPresent(
            static function (string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
        );

        self::assertEquals(['foo'], $invocations);
    }

    #[Test]
    public function ifPresentOrElseEmptyExecutesActionIfEmpty(): void
    {
        $optional = Optional::empty();

        $invocations = [];
        $optional->ifPresentOrElse(
            static function (?string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
            static function () use (&$invocations): void {
                $invocations[] = 'empty';
            },
        );

        self::assertEquals(['empty'], $invocations);
    }

    #[Test]
    public function ifPresentOrElseExecutesActionIfNotEmpty(): void
    {
        $optional = Optional::fromAny('foo');

        $invocations = [];
        $optional->ifPresentOrElse(
            static function (string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
            static function () use (&$invocations): void {
                $invocations[] = '<empty>';
            },
        );

        self::assertEquals(['foo'], $invocations);
    }

    /**
     * @param Optional<mixed> $subject
     */
    #[Test]
    #[DataProvider('equalsProvider')]
    public function itCanDetermineEquality(Optional $subject, mixed $other, bool $expected): void
    {
        self::assertEquals($expected, $subject->equals($other));
    }

    #[Test]
    public function itHasNoValueWhenConstructedEmpty(): void
    {
        $optional = Optional::empty();

        self::assertFalse($optional->isPresent());
        self::assertTrue($optional->isEmpty());

        $this->expectException(NoSuchElementException::class);
        $optional->get();
    }

    #[Test]
    #[DataProvider('allValuesProvider')]
    public function itHasValueWhenConstructedFromAny(mixed $a): void
    {
        $optional = Optional::fromAny($a);

        self::assertTrue($optional->isPresent());
        self::assertFalse($optional->isEmpty());
        self::assertEquals($a, $optional->get());
    }

    #[Test]
    public function itHasValueWhenConstructedFromIterableWithCurrent(): void
    {
        $optional = Optional::fromCurrent([1]);
        self::assertTrue($optional->isPresent());
    }

    #[Test]
    #[DataProvider('nonNullableValuesProvider')]
    public function itHasValueWhenConstructedFromNullableWithNonNull(mixed $a): void
    {
        $optional = Optional::fromNullable($a);

        self::assertTrue($optional->isPresent());
        self::assertFalse($optional->isEmpty());
        self::assertEquals($a, $optional->get());
    }

    #[Test]
    public function itIsEmptyWhenConstructedFromIterableWithoutCurrent(): void
    {
        $optional = Optional::fromCurrent([]);
        self::assertTrue($optional->isEmpty());
    }

    #[Test]
    public function itIsEmptyWhenConstructedFromNullableWithNull(): void
    {
        $optional = Optional::fromNullable(null);

        self::assertFalse($optional->isPresent());
        self::assertTrue($optional->isEmpty());

        $this->expectException(NoSuchElementException::class);
        $optional->get();
    }

    #[Test]
    public function mapReturnsEmptyOptionalWhenEmpty(): void
    {
        $optional = Optional::empty();

        self::assertEquals(Optional::empty(), $optional->map(static fn(?string $value): string => $value . '-mapped'));
    }

    #[Test]
    public function mapReturnsOptionalWithResultFromMapperWhenNotEmpty(): void
    {
        $optional = Optional::fromAny('foo');

        self::assertEquals(
            Optional::fromAny('foo-mapped'),
            $optional->map(static fn(string $value): string => $value . '-mapped'),
        );
    }

    #[Test]
    public function mapsToEmptyWhenNotPresent(): void
    {
        $optional = Optional::empty();
        self::assertEquals(Optional::empty(), $optional->map(static fn(string $value): string => $value . '-mapped'));
    }

    #[Test]
    public function mapsToOptionalIfPresent(): void
    {
        $optional = Optional::fromAny('foo');
        self::assertEquals(
            Optional::fromAny('foo-mapped'),
            $optional->map(static fn(string $value): string => $value . '-mapped'),
        );
    }

    #[Test]
    public function orElseGetReturnsResponseFromSupplierWhenEmpty(): void
    {
        $optional = Optional::empty();

        $otherValue = 'foo';

        self::assertEquals($otherValue, $optional->orElseGet(static fn(): string => $otherValue));
    }

    #[Test]
    public function orElseGetReturnsValueWhenNotEmpty(): void
    {
        $value = 'foo';
        $optional = Optional::fromAny($value);

        $otherValue = 'bar';

        self::assertEquals($value, $optional->orElseGet(static fn(): string => $otherValue));
    }

    #[Test]
    public function orElseReturnsOtherValueWhenEmpty(): void
    {
        $optional = Optional::empty();

        $otherValue = 'bar';

        self::assertEquals($otherValue, $optional->orElse($otherValue));
    }

    #[Test]
    public function orElseReturnsValueWhenNotEmpty(): void
    {
        $value = 'foo';
        $optional = Optional::fromAny($value);

        $otherValue = 'bar';

        self::assertEquals($value, $optional->orElse($otherValue));
    }

    #[Test]
    public function orElseThrowReturnsValueWhenSet(): void
    {
        $optional = Optional::fromAny('foo');
        self::assertEquals('foo', $optional->orElseThrow());
    }

    #[Test]
    public function orElseThrowThrowsNoExceptionFromSupplierWhenEmpty(): void
    {
        $optional = Optional::empty();

        $customException = new class extends Exception {};
        $this->expectExceptionObject($customException);
        $optional->orElseThrow(static fn(): Throwable => $customException);
    }

    #[Test]
    public function orElseThrowThrowsNoSuchElementExceptionWhenEmpty(): void
    {
        $this->expectException(NoSuchElementException::class);
        $optional = Optional::empty();
        $optional->orElseThrow();
    }
}
