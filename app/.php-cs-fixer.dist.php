<?php

# PHP CS Fixer configuration file

# Uses the default Symfony rules and excludes the tools, var and vendor directories

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('tools')
    ->exclude('var')
    ->exclude('vendor');

$config = new PhpCsFixer\Config();
return $config
    ->setRules(['@Symfony' => true])
    ->setFinder($finder);
