<?php

declare(strict_types=1);

namespace Par\Time\Format;

use DateTimeInterface;
use Par\Time\Exception\RuntimeException;
use Par\Time\Temporal\TemporalTextField;

final class DateTimeFormatterBuilder
{
    private ?string $locale = null;
    private ?FormatStyle $dateStyle = null;
    private ?FormatStyle $timeStyle = null;
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

    public function appendLocalized(?FormatStyle $dateStyle = null, ?FormatStyle $timeStyle = null): self
    {
        $this->dateStyle = $dateStyle;
        $this->timeStyle = $timeStyle;

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
        if (null === $this->dateStyle && null === $this->timeStyle && empty(trim($this->pattern))) {
            throw new RuntimeException('Pattern cannot be empty');
        }

        if (!empty(trim($this->pattern))) {
            return DateTimeFormatter::ofPattern($this->pattern, $this->locale);
        }

        return DateTimeFormatter::ofLocalized($this->dateStyle, $this->timeStyle, $this->locale);
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
