<?php

declare(strict_types=1);

namespace Par\Time\Format;

enum FormatStyle: string
{
    case FULL = 'FULL';
    case LONG = 'LONG';
    case MEDIUM = 'MEDIUM';
    case SHORT = 'SHORT';
}
