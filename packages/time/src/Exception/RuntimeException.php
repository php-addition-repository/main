<?php

declare(strict_types=1);

namespace Par\Time\Exception;

use RuntimeException as GlobalRuntimeException;

final class RuntimeException extends GlobalRuntimeException implements ExceptionInterface {}
