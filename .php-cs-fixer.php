<?php
// .php-cs-fixer.php configuration
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        '@PHP82Migration' => true, // Or try @PHP83Migration if your version supports it
        'modernize_types_casting' => true,
        'no_alias_functions' => true, // Risky: replaces alias functions with their master names
    ])
    ->setFinder(
        Finder::create()
            ->in(__DIR__ . '/private')
            ->in(__DIR__ . '/api')
            ->in(__DIR__ . '/services')
            ->in(__DIR__ . '/utils')
    );
