<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use Par\Time\Format\TextStyle;

interface TemporalTextField
{
    public function getPattern(TextStyle $textStyle): string;
}
