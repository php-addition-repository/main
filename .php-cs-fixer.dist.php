<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
                           ->in(__DIR__ . '/packages');

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@Symfony' => true,
            '@Symfony:risky' => true,

            # Basic
            'single_line_empty_body' => true,

            # Strict
            'declare_strict_types' => true,
            'strict_param' => true,

            # Import
            'global_namespace_import' => [
                'import_classes' => true,
                'import_constants' => true,
                'import_functions' => true,
            ],

            # Class Notation
            'final_class' => true,

            # Function Notation
            'single_line_throw' => false,

            # Operator
            'concat_space' => ['spacing' => 'one'],

            # PHPUnit
            'php_unit_internal_class' => true,
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],

            # PHPDoc
            'phpdoc_separation' => [
                'groups' => [
                    ['Annotation', 'NamedArgumentConstructor', 'Target'],
                    ['template'],
                    ['template-*', 'implements', 'extends'],
                    ['phpstan-*'],
                    ['author', 'copyright', 'license'],
                    ['category', 'package', 'subpackage'],
                    ['property', 'property-read', 'property-write'],
                    ['deprecated', 'link', 'see', 'since'],
                ],
                'skip_unlisted_annotations' => false,
            ],
        ],
    )
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
