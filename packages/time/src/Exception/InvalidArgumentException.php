<?php

declare(strict_types=1);

namespace Par\Time\Exception;

use InvalidArgumentException as GlobalInvalidArgumentException;

final class InvalidArgumentException extends GlobalInvalidArgumentException implements ExceptionInterface {}
