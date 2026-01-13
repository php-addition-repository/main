<?php

declare(strict_types=1);

namespace Par\Time\Format;

/**
 * Enumeration of the style of text formatting and parsing.
 *
 * Text styles define three sizes for the formatted text - 'full', 'short' and 'narrow'. Each of these three sizes is available in both 'standard' and 'stand-alone' variations.
 *
 * The difference between the three sizes is obvious in most languages. For example, in English the 'full' month is 'January', the 'short' month is 'Jan' and the 'narrow' month is 'J'. Note that the narrow size is often not unique. For example, 'January', 'June' and 'July' all have the 'narrow' text 'J'.
 *
 * The difference between the 'standard' and 'stand-alone' forms is trickier to describe as there is no difference in English. However, in other languages there is a difference in the word used when the text is used alone, as opposed to in a complete date. For example, the word used for a month when used alone in a date picker is different to the word used for month in association with a day and year in a date.
 */
enum TextStyle: string
{
    /**
     * Full text style, e.g., "Monday".
     */
    case FULL = 'full';

    /**
     * Short text style, e.g., "Mon".
     */
    case SHORT = 'short';

    /**
     * Narrow text style, e.g., "M".
     */
    case NARROW = 'narrow';

    /**
     * Full stand-alone text style, e.g., "Monday".
     */
    case FULL_STANDALONE = 'full_standalone';

    /**
     * Short stand-alone text style, e.g., "Mon".
     */
    case SHORT_STANDALONE = 'short_standalone';

    /**
     * Narrow stand-alone text style, e.g., "M".
     */
    case NARROW_STANDALONE = 'narrow_standalone';
}
