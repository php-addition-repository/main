<?php

declare(strict_types=1);

namespace Par\Time\Format;

use DateTimeInterface;
use Par\Time\Exception\RuntimeException;
use Par\Time\Temporal\TemporalTextField;

final class DateTimeFormatterBuilder
{
    private ?string $locale = null;
    private string $pattern = '';

    public static function create(): self
    {
        return new self();
    }

    private function __construct() {}

    public function appendLiteral(string $literal): self
    {
        if ('' !== $literal) {
            $this->pattern .= '\'' . str_replace('\'', '\'\'', $literal) . '\'';
        }

        return $this;
    }

    public function appendPattern(string $pattern): self
    {
        if ('' !== $pattern) {
            $this->pattern .= $pattern;
        }

        return $this;
    }

    public function appendText(TemporalTextField $field, TextStyle $textStyle): self
    {
        $this->pattern .= $field->getPattern($textStyle);

        return $this;
    }

    public function build(): DateTimeFormatter
    {
        if (empty(trim($this->pattern))) {
            throw new RuntimeException('Pattern cannot be empty');
        }

        return DateTimeFormatter::ofPattern($this->pattern, $this->locale);
    }

    public function format(DateTimeInterface $dateTime): string
    {
        return $this->build()->format($dateTime);
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
