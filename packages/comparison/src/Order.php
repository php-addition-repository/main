<?php

declare(strict_types=1);

namespace Par\Comparison;

/**
 * This enum represents the value of something when compared to another.
 */
enum Order: int
{
    case Lesser = -1;
    case Equal = 0;
    case Greater = 1;
}
