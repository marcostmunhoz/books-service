<?php

// definimos as pastas que serão excluídas da verificação
$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->exclude('bootstrap/cache')
    ->exclude('database')
    ->exclude('public')
    ->exclude('resources')
    ->exclude('storage')
    ->exclude('tests')
    ->exclude('vendor')
    ->in(__DIR__);

// definimos a configuração
return PhpCsFixer\Config::create()
    ->setFinder($finder) // configuramos o finder
    ->setRules([ // definimos as regras usadas
        '@Symfony:risky' => true,
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false, // evita que tags @param e @return sejam removidas de blocos PHPDoc
    ])
    ->setLineEnding("\n")
    ->setRiskyAllowed(true);
