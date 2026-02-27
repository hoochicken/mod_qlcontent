<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths([
        __DIR__ . '/php',
        __DIR__ . '/tmpl',
    ])
    // register single rule
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])
    // here we can define, what prepared sets of rules will be applied
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )
    ->withPhpSets(php85: true)
    ->withFileExtensions(['php']);
