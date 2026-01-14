<?php

declare(strict_types=1);

namespace Par\Time\Format;

use DateTimeInterface;
use IntlDateFormatter;
use Par\Time\Exception\RuntimeException;

use function sprintf;

use const LC_TIME;

final readonly class DateTimeFormatter
{
    public static function ofPattern(string $pattern, ?string $locale = null): self
    {
        return new self($pattern, $locale ?? setlocale(LC_TIME, '0') ?: 'en_EN');
    }

    private function __construct(
        private ?string $pattern,
        private ?string $locale,
    ) {
        if (!class_exists(IntlDateFormatter::class)) {
            throw new RuntimeException('The intl extension (IntlDateFormatter) is required.');
        }
    }

    public function format(DateTimeInterface $native): string
    {
        $text = IntlDateFormatter::formatObject($native, $this->pattern, $this->locale);

        if (false === $text) {
            // If formatting fails, surface a useful error.
            throw new RuntimeException(
                sprintf(
                    'Failed to format pattern "%s" in locale "%s" :%s',
                    $this->pattern,
                    $this->locale,
                    intl_get_error_message(),
                ),
            );
        }

        return $text;
    }
}
