<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
                           ->in(__DIR__ . '/packages');

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@PSR12' => true,
            'strict_param' => true,
            'array_syntax' => ['syntax' => 'short'],
            'declare_strict_types' => true,
        ],
    )
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
