<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
                           ->in(__DIR__ . '/packages');

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@PER-CS3x0' => true,
            'declare_strict_types' => true,
            'global_namespace_import' => [
                'import_classes' => true,
                'import_constants' => true,
                'import_functions' => true,
            ],
            'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
            'final_class' => true,
            'single_line_empty_body' => false,
            'strict_param' => true,
            'php_unit_internal_class' => ['types' => ['abstract', 'final', 'normal']],
            'php_unit_method_casing' => ['case' => 'camel_case'],
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        ],
    )
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
