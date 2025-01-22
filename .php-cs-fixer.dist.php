<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'array_indentation' => true,
        'no_superfluous_phpdoc_tags' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw']
        ],
    ])
    ->setFinder($finder)
    ;