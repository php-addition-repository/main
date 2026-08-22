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
    public static function ofLocalized(
        ?FormatStyle $dateStyle,
        ?FormatStyle $timeStyle,
        ?string $locale,
    ): self {
        return new self($dateStyle, $timeStyle, null, $locale);
    }

    public static function ofPattern(string $pattern, ?string $locale = null): self
    {
        return new self(null, null, $pattern, $locale);
    }

    private function __construct(
        private ?FormatStyle $dateStyle,
        private ?FormatStyle $timeStyle,
        private ?string $pattern,
        private ?string $locale,
    ) {
        if (!class_exists(IntlDateFormatter::class)) {
            throw new RuntimeException('The intl extension (IntlDateFormatter) is required.');
        }
    }

    public function format(DateTimeInterface $native): string
    {
        $format = [];
        if (null !== $this->dateStyle) {
            $format[] = $this->dateStyle;
        }
        if (null !== $this->timeStyle) {
            $format[] = $this->timeStyle;
        }

        if (null !== $this->pattern) {
            $format = $this->pattern;
        }
        $text = IntlDateFormatter::formatObject($native, $format, $this->locale ?? setlocale(LC_TIME, '0') ?: 'en_EN');

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
