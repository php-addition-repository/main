<?php

declare(strict_types=1);

namespace Par\Comparison\Exception;

use InvalidArgumentException;

use function sprintf;

final class IncomparableException extends InvalidArgumentException
{
    public static function fromValues(mixed $a, mixed $b, string $additionalInfo = ''): self
    {
        return new self(
            sprintf(
                'Unable to compare "%s" and "%s"%s',
                get_debug_type($a),
                get_debug_type($b),
                $additionalInfo
            )
        );
    }
}
