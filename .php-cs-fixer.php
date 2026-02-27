<?php

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude([
        'vendor',
        'language',
    ])
;

return new PhpCsFixer\Config()
    ->setRules([
        '@PhpCsFixer' => true,
    ])
    ->setFinder($finder)
;
