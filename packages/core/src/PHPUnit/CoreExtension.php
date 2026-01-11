<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use SebastianBergmann\Comparator\Factory;

/**
 * A PHPUnit extension that adds several features.
 *
 * - The ability to use the "assertEquals" assertion on objects that implement the `Par\Core\Equable` interface.
 *
 * Include the following in your phpunit configuration xml to enable the extension:
 * ```xml
 * <phpunit ...>
 *   ...
 *   <extensions>
 *     // PHPUnit 12
 *     <bootstrap class="\Par\Core\PHPUnit\CoreExtension"/>
 *     // PHPUnit 10
 *     <extension class="\Par\Core\PHPUnit\CoreExtension"/>
 *   </extensions>
 *   ...
 * </phpunit>
 * ```
 */
final class CoreExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        Factory::getInstance()->register(new EquableComparator());
    }
}
