<?php

declare(strict_types=1);

namespace Par\Core\Exception;

use InvalidArgumentException as GlobalInvalidArgumentException;

abstract class InvalidArgumentException extends GlobalInvalidArgumentException implements ExceptionInterface
{
}
